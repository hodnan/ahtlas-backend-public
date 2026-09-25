<?php

namespace App\Services\Modules\Administration;

use App\Models\Modules\Administration\Incentives\RV\Term;
use App\Models\Modules\Administration\Incentives\RV\TermPanelSignature;
use App\Models\Modules\Administration\Incentives\RV\TermSignature;
use App\Models\Modules\Employee\Employee;

use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use setasign\Fpdi\Fpdi;
use ZipArchive;

class TermSignatureService
{
    public static function setEmployeeToSign($term_id)
    {
        $term = Term::where('id', $term_id)
            ->get(['id', 'month_ref',  'position', 'level', 'sector_n1_id', 'sector_n2_id', 'apprentice', 'status'])
            ->first();

        $users = [];

        $statuses = [
            TermInterface::STATUS_PENDING,
            TermInterface::STATUS_APPROVED,
        ];

        // verifica se o termos é pendente ou aprovado 
        if ($term && in_array($term->status['id'], $statuses)) {

            // // pega a lista dos termos que foram assinados 
            // $signatures = TermSignature::where('term_id', $term_id)
            //     ->whereNotNull('uuid')
            //     ->pluck('username');

            $isVeteran = $term->level['id'];
            $apprentice = !$term->apprentice ? '%Jovem%' : '';

            $users = Employee::where('active', 1)
                ->where(function ($query) use ($term) {
                    // Se for gestor pega a lista dos gestores
                    if ($term->position['id'] >= 1) {
                        $managers = self::employeeToSignManager($term->sector_n1_id, $term->sector_n2_id, $term->position['id']);
                        $query->whereIn('username', $managers);
                        $query->where('position', 'not like', '%Trei%');
                    }
                    // Se for agente
                    if ($term->position['id'] == 0) {
                        $query->where('sector_n1_id', $term->sector_n1_id);
                        $query->where('sector_n2_id', $term->sector_n2_id);
                    }
                })
                ->where('hierarchical_level', $term->position['id'])
                ->where(function ($query) use ($isVeteran) {
                    // se não for ambos filtra por isVeteran
                    if ($isVeteran != 2) $query->where('is_veteran', $isVeteran);
                })
                ->where('position_summary', 'not like', $apprentice)
                // ->whereNotIn('username', $signatures)
                ->get([
                    DB::raw("'$term_id' as term_id"),
                    DB::raw("'$term->month_ref' as month_ref"),
                    'username'
                ])
                ->makeHidden(['label', 'active_label'])
                ->toArray();
        };

        // delta tudo que não foi assinado 

        $updatedAt = Carbon::now()->startOfDay();

        LazyCollection::make($users)
            ->chunk(100)
            ->each(function ($chunk) use ($updatedAt) {
                $chunk = json_decode(json_encode($chunk), true);

                foreach ($chunk as $item) {
                    $key = [
                        'username' => $item['username'],
                        'term_id' => $item['term_id'],
                    ];

                    $item['updated_at'] = $updatedAt;

                    TermSignature::updateOrCreate($key, $item);
                }
            });
    }

    public static function employeeToSignManager($sector_n1_id,  $sector_n2_id,  $position)
    {
        $managers = [];
        if ($position > 0) {
            $managers = Employee::where('active', 1)

                ->where('hierarchical_level', 0)
                ->where('sector_n1_id', $sector_n1_id)
                ->where('sector_n2_id', $sector_n2_id)
                ->distinct()
                ->pluck("manager_n" . $position . "_id")
                ->toArray();
        }

        return  $managers;
    }

    public static function storeTermFile($signature_id)
    {
        $signature = TermSignature::with(['user'])->find($signature_id);
        $term = TermService::getTermById($signature->term_id);
        $position = strtolower($term->position['short']);

        $termPDF = $term;
        $termPDF->signed = $signature->toArray();

        // Carregar a view Blade e obter o conteúdo HTML 
        $pdf = new Dompdf();
        $pdf->loadHTML(view("templates.rv.term-$position", ['term' => $termPDF]));

        $pdf->render();

        $base64Pdf = base64_encode($pdf->output());

        $signature->term_file = $base64Pdf;

        $signature->save();
    }

    public static function getSignedById($termId, $username)
    {
        $signed = TermSignature::where('term_id', $termId)
            ->where('username', $username)
            ->whereNotNUll('uuid')
            ->first();

        return $signed;
    }

    public static function getTermsByUser($username)
    {

        $username = preg_replace('/\D/', '', $username);

        $termsAhtlas = TermSignature::with(['term'])->where('username', 'usr' . $username)
            ->orderBy('month_ref', 'desc')
            ->get([
                'id',
                'uuid',
                'term_id',
                'month_ref',
                'accept',
                'term_file',
                'accept_meta',
                'created_at as available_at',
                DB::raw('case when accept IS NULL then NULL else updated_at end as accepted_at'),
                DB::raw("0 as is_backup"),
            ]);

        $termsPainel =  TermPanelSignature::where('username', $username)
            ->orderBy('month_ref', 'desc')
            ->get([
                'id',
                'month_ref',
                'term_file',
                'username',
                'uuid',
                'hostname',
                'ip',
                'available_at',
                'accepted_at',
                'accept',
                DB::raw("1 as is_backup"),
            ]);

        $terms =  $termsAhtlas->merge($termsPainel);

        if ($terms) {
            $terms = $terms->toArray();
        }

        return $terms;
    }

    public static function termFilesList($username)
    {
        $terms = self::getTermsByUser($username);

        $employee = Employee::with(['avatar'])->where('username', 'usr' . $username)
            ->first(['username', 'name', 'admission', 'dismissal']) ?? [];

        $imageData = null;

        if ($employee) {
            // Converter a imagem do avatar para Base64
            $avatarBase64 = $employee['avatar']['avatar'];
            $imageData = 'data:image/jpeg;base64,' . $avatarBase64;
        }

        // Criar instância do Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true); // Permite carregar imagens externas
        $pdf = new Dompdf($options);

        // Carregar a view e gerar o PDF
        $html = view("templates.rv.term-signatures", [
            'terms' => $terms,
            'employee' => $employee,
            'avatarBase64' => $imageData
        ])->render();

        $pdf->loadHtml($html);
        $pdf->render();

        // Obter o conteúdo do PDF
        $pdfData = $pdf->output();

        // Converter o PDF para Base64
        $base64Pdf = base64_encode($pdfData);

        // Retornar o Base64 no JSON
        return  $base64Pdf;
    }

    public static function addStatusOnPanelTerm($sourcePath, $message)
    {
        if (!Storage::disk('nas')->exists($sourcePath)) {
            return response()->json(['error' => "Arquivo não encontrado: $sourcePath"], 404);
        }

        // Lê o PDF do storage
        $pdfContent = Storage::disk('nas')->get($sourcePath);
        $tempFile = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($tempFile, $pdfContent);

        // Criar uma instância do FPDI
        $pdf = new FpdI();

        // Carregar o PDF existente
        $pdf->setSourceFile($tempFile);
        $tplId = $pdf->importPage(1);

        // Obtém as dimensões da página original
        $size = $pdf->getTemplateSize($tplId);

        // Adiciona uma nova página com as mesmas dimensões do PDF original
        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);

        // Usa o template da página original
        $pdf->useTemplate($tplId, 0, 0, $size['width'], $size['height']);

        // Adiciona texto sobre o PDF existente
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor(0, 0, 0); // Cor preta
        $pdf->SetXY(5, 5); // Define a posição do texto
        $pdf->Write(0, $message);

        // Salva o novo PDF gerado temporariamente
        $outputFile = tempnam(sys_get_temp_dir(), 'pdf_output');
        $pdf->Output($outputFile, 'F');

        // Lê o conteúdo do novo PDF gerado
        $pdfData = file_get_contents($outputFile);

        // Converte o PDF em Base64
        $base64Pdf = base64_encode($pdfData);

        // Retorna o Base64 no JSON
        return $base64Pdf;
    }


    public static function downloadTermsByUser($username)
    {
        // Remover caracteres não numéricos do nome de usuário
        $username = preg_replace('/\D/', '', $username);

        // Caminho do arquivo ZIP temporário
        $zipFilePath = storage_path("app/temp/$username.zip");

        // Buscar os termos do usuário
        $terms = self::getTermsByUser($username);

        // Se não houver termos, retorna null
        if (!$terms) return;

        $tempFiles = [];

        // Criar o arquivo ZIP
        $zip = new ZipArchive();

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {

            $resum = self::termFilesList($username);
            $resumContent = base64_decode($resum);
            $tempResumFilePath = storage_path("app/temp/" . $username . "_resumo.pdf");
            file_put_contents($tempResumFilePath, $resumContent);
            $zip->addFile($tempResumFilePath, $username . "_resumo.pdf");
            // Lista temp para deletar 
            $tempFiles[] = $tempResumFilePath;
        } else {
            return "Erro ao criar o arquivo ZIP.";
        }

        // Adicionar os PDFs ao ZIP
        foreach ($terms as $term) {
            $pdfName = $term['term']['term_name'] . '.pdf';
            try {
                if (!$term['is_backup']) {
                    $termFile = !empty($term['term_file']) ? $term['term_file'] : $term['term']['term'];
                    $pdfContent = base64_decode($termFile);
                }

                if ($term['is_backup']) {
                    $message = $term['accept']['id'] == 1 ? "Protocolo: " . $term['uuid'] . " - Data: " . $term['accepted_at'] : 'Pendente de aceite';
                    $termFile = self::addStatusOnPanelTerm($term['term']['term_file'],  $message);
                    $pdfContent = base64_decode($termFile);
                }

                // Caminho do arquivo temporário
                $tempFilePath = storage_path("app/temp/{$pdfName}");

                // Verifica se o conteúdo foi decodificado corretamente
                file_put_contents($tempFilePath, $pdfContent);

                // Adiciona o PDF ao ZIP
                $zip->addFile($tempFilePath, $pdfName);

                // Lista temp para deletar 
                $tempFiles[] = $tempFilePath;
            } catch (\Throwable $th) {
                Log::error("falha ao gerar termo:  $pdfName", [$th->getMessage()]);

                // Nome do arquivo em caso de falha
                $failedFileName = "(falha)_" . pathinfo($pdfName, PATHINFO_FILENAME) . ".txt";
                $failedFilePath = storage_path("app/temp/{$failedFileName}");

                // Cria um arquivo TXT vazio
                file_put_contents($failedFilePath, "");

                // Adiciona o arquivo TXT ao ZIP
                $zip->addFile($failedFilePath, $failedFileName);

                // Adiciona à lista para deletar depois
                $tempFiles[] = $failedFilePath;
            }
            // define o nome do pdf



        }

        // Fechar o ZIP
        $zip->close();

        // Deletar todos os arquivos temporários
        foreach ($tempFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

        // Retorna o arquivo ZIP
        return true;
    }
}
