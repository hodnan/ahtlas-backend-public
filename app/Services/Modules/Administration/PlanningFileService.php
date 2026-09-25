<?php

namespace App\Services\Modules\Administration;

use App\Jobs\Modules\Administration\Planning\FileLoadStoreJob;
use App\Models\Addons\Charges\PlanningDimSlaP1;
use App\Models\Addons\Charges\PlanningDimSlaP2;
use App\Models\Modules\Administration\Planning\PlanningFileLoad;
use App\Models\Modules\TacticalCenter\HeadCount\HeadCount;
use App\Services\Modules\TacticalCenter\HeadCount\HeadCountService;
use Carbon\Carbon;

use Illuminate\Support\Facades\Storage;

class PlanningFileService
{
    protected static $errors = [];
    protected static $validate = false;
    protected static $failLines = 0;
    protected static $fileLoad = null;

    public function __construct($id)
    {
        static::$fileLoad = PlanningFileLoad::find($id);
        static::$errors = [];
        static::$validate = false;
        static::$failLines = 0;
    }

    public static function store($request)
    {
        if ($request->hasFile('file')) {

            try {
                $file = $request->file('file');
                $extension = strtolower($file->getClientOriginalExtension());

                // Verifica o tipo MIME real
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $file->getPathname());
                finfo_close($finfo);
                $allowedMimeTypes = ['text/csv', 'text/plain'];
                if (!in_array($mimeType, $allowedMimeTypes) || !in_array($extension, ['csv', 'txt'])) {
                    static::$errors['file']['message'] = 'O arquivo deve ser um CSV ou TXT válido.';
                    throw new \Exception('O arquivo deve ser um CSV ou TXT válido.');
                }

                // Verifica se é um CSV válido (para .csv)
                if ($extension === 'csv') {
                    $handle = fopen($file->getPathname(), 'r');
                    $firstLine = fgets($handle);
                    fclose($handle);
                    if (strpos($firstLine, ';') === false) { // Assume delimitador ';'
                        static::$errors['file']['message'] = 'O arquivo CSV não possui um formato válido.';
                        throw new \Exception('O arquivo CSV não possui um formato válido.');
                    }
                }
                $monthRef = Carbon::parse($request->month_ref);

                $filePath = PlanningFileInterface::STORAGE_FILE_PATH . "/" . $monthRef->copy()->format('Y-m');

                $data = [
                    'month_ref' =>  $monthRef,
                    'type' =>  $request->type,
                    'status' =>  0,
                ];

                $fileLoad = PlanningFileLoad::create($data);

                $fileName = normalizeString(PlanningFileInterface::TYPES[$request->type]['name'] . "_" . str_pad($fileLoad->id, 4, '0', STR_PAD_LEFT));
                $fileName = $fileName  . "." . $extension;
                $fileLoad->file = $filePath . "/" . $fileName;
                $fileLoad->save();

                $request->file('file')->storeAs($filePath, $fileName, PlanningFileInterface::STORAGE_FILE_DISK);

                if (env('APP_ENV') == 'production') {
                    FileLoadStoreJob::dispatch($fileLoad->id)
                        ->onQueue(PlanningFileInterface::QUEUE);
                }
            } catch (\Throwable $e) {
                throw new \Exception($e->getMessage());
            }
        } else {
            throw new \Exception('Arquivo não localizado');
        }
    }


    public static function fileValidate(): bool
    {
        $fileLoad = static::$fileLoad;
        $filePath = $fileLoad['file'];

        $defaultFields = PlanningFileInterface::DEFAULT_FIELDS[$fileLoad['type']['id']]['fields'];

        if (Storage::disk(PlanningFileInterface::STORAGE_FILE_DISK)->exists($filePath)) {
            try {
                $fileStream = Storage::disk(PlanningFileInterface::STORAGE_FILE_DISK)->readStream($filePath);
                $firstRow = [];
                if ($fileStream !== false) {
                    $handle = fopen('php://temp', 'w+');
                    stream_copy_to_stream($fileStream, $handle);
                    rewind($handle);

                    if (($data = fgetcsv($handle, 0, ';')) !== false) {
                        $firstRow = $data;
                    }

                    fclose($handle);
                }

                // Comparar colunas do CSV com os campos esperados
                $missingFields = array_diff($defaultFields, $firstRow); // Campos faltando
                if (!empty($missingFields)) {

                    static::$errors['file']['message'] = "Arquivo fora do padrão";
                    static::$validate = false;
                } else {
                    static::$validate = true;

                    $fileLoad->status = PlanningFileInterface::STATUS_PENDING;
                    $fileLoad->save();
                    return true;
                }
            } catch (\Throwable $th) {
                static::$errors['file']['message'] = "Falha não definida";
                static::$validate = false;
            }
        } else {
            static::$errors['file']['message'] = "Arquivo não encontrado: $filePath";
            static::$validate = false;
        }

        $fileLoad->errors = static::$errors;
        $fileLoad->status = PlanningFileInterface::STATUS_ERROR;
        $fileLoad->save();

        static::$errors = [];
        return false;
    }

    public static function walkCSV()
    {

        if (!static::$validate) return;

        $fileLoad = static::$fileLoad;
        $filePath = $fileLoad['file'];
        $fileCharge = $fileLoad['type']['charge'];
        $chargeMethod = $fileLoad['type']['chargeMethod'];
        $monthRef = $fileLoad['month_ref'];
        $modelClass = PlanningFileInterface::MODELS[$fileLoad['type']['id']]['model'];
        $afterCharge = PlanningFileInterface::MODELS[$fileLoad['type']['id']]['after_charge'];

        if (Storage::disk(PlanningFileInterface::STORAGE_FILE_DISK)->exists($filePath)) {


            // Abre o arquivo original para leitura
            $fileStream = Storage::disk(PlanningFileInterface::STORAGE_FILE_DISK)->readStream($filePath);
            $fileTempStream = fopen('php://temp', 'w+'); // Abre um stream temporário para os dados processados


            if ($fileStream && $fileTempStream) {
                $header = null; // Inicializa o cabeçalho
                $fileLoad->start_at = Carbon::now();
                $fileLoad->status = PlanningFileInterface::STATUS_LOADING;
                $fileLoad->save();

                $inputHandle = fopen('php://temp', 'w+');



                stream_copy_to_stream($fileStream, $inputHandle); // Copia o conteúdo do arquivo original para o stream temporário
                rewind($inputHandle);

                // Se o método de carga for DELETE, remove os registros do mês de referência
                if ($chargeMethod === PlanningFileInterface::CHARGE_METHOD_DELETE) {
                    $modelClass::where('month_ref', $monthRef)->delete();
                }

                // Processa cada linha do CSV
                while (($row = fgetcsv($inputHandle, 0, ';')) !== false) {


                    if (empty(array_filter($row))) {
                        continue;
                    }
                    $outputRow = $row;
                    if (!$header) {
                        // A primeira linha é o cabeçalho, adiciona a nova coluna
                        $header = $row;
                        $outputHeader = $row;
                        $outputHeader[] = 'charge_error'; // Adiciona coluna de erro
                        fputcsv($fileTempStream, $outputHeader, ';'); // Escreve o cabeçalho com a coluna de erro
                        continue;
                    }

                    try {
                        // Realiza o mapeamento de valores para as colunas do cabeçalho
                        $data = array_combine($header, $row);
                        $data['month_ref'] = $monthRef;
                        $data['charge_id'] = $fileLoad->id;

                        // Limpa colunas inválidas
                        $data = array_filter($data, fn($value, $key) => !empty($key) && $value !== "", ARRAY_FILTER_USE_BOTH);


                        // Processa os dados

                        static::$fileCharge($data);

                        // Adiciona coluna de erro como `null` (sem erro)

                        $outputRow[] = null;
                    } catch (\Exception $e) {
                        // Adiciona o erro como uma nova coluna
                        $outputRow[] = $e->getMessage();
                    }

                    // Escreve a linha processada no stream temporário
                    fputcsv($fileTempStream, $outputRow, ';');
                }

                fclose($inputHandle);
                fclose($fileStream);
                rewind($fileTempStream); // volta o arquivo para o começo para ser regravado; 
                fwrite($fileTempStream, "\xEF\xBB\xBF");

                // Agora, move os dados processados do arquivo temporário para o arquivo original
                Storage::disk(PlanningFileInterface::STORAGE_FILE_DISK)->put($filePath, stream_get_contents($fileTempStream));

                fclose($fileTempStream);

                $fileLoad->end_at = Carbon::now();
                $fileLoad->save();

                if ($afterCharge) {
                    static::$afterCharge($fileLoad);
                }
            } else {
                static::$errors['reader']['message'] = "Erro ao abrir arquivos para leitura/escrita.";
            }
        } else {
            static::$errors['reader']['message'] = "Arquivo não encontrado: $filePath";
        }

        if (!empty(static::$errors)) {
            $fileLoad->errors = static::$errors;
            $fileLoad->save();
        }
    }

    protected static function chargeSLAP1(array $data)
    {
        try {
            $newData = [
                'CARGA_ID' => $data['charge_id'],
                'CD_DATA' => $data['CD_DATA'],
                'CD_HORA' => $data['CD_HORA'],
                'NO_SITE' => $data['NO_SITE'],
                'CD_SETOR' => $data['CD_SETOR'],
                'PREV_REC' => $data['PREV_REC'],
                'PREV_ATE' => $data['PREV_ATE'],
                'PREV_TMA' => $data['PREV_TMA'],
                'PREV_NS' => $data['PREV_NS'],
                'PREV_HC' => $data['PREV_HC'],
                'PREV_HC_PAUSA' => $data['PREV_HC_PAUSA'],
                'PREV_DN' => $data['PREV_DN'],
                'PESO_DIA' => $data['PESO_DIA'],
            ];

            $key = [
                'CD_DATA' => $data['CD_DATA'],
                'CD_HORA' => $data['CD_HORA'],
                'CD_SETOR' => $data['CD_SETOR'],
            ];

            PlanningDimSlaP1::updateOrcreate($key, $newData);
        } catch (\Throwable $e) {

            static::$failLines =  static::$failLines + 1;
            static::$errors['line_error']['message'] = "(" . static::$failLines . ") linhas não carregadas";

            throw new \Exception("Linha nao carregada | " . $e->getMessage() . " | " . json_encode($data));
        }
    }

    protected static function chargeSLAP2(array $data)
    {
        try {
            $newData = [
                'CARGA_ID' => $data['charge_id'],
                'ANO' => $data['ANO'],
                'MES' => $data['MES'],
                'DATA' => $data['DATA'],
                'CD_DPTO' => $data['CD_DPTO'],
                'CD_TTV' => $data['CD_TTV'],
                'DIM' => $data['DIM'],
                'DIM_FERIAS' => $data['DIM_FERIAS'],
                'DIM_FTE' => $data['DIM_FTE'],
                'PA' => $data['PA'],
                'DIM_PRO_RATA' => $data['DIM_PRO_RATA'],
                'DIM_PRO_RATA_FERIAS' => $data['DIM_PRO_RATA_FERIAS'],
                'VERSAO_CAPACITY' => $data['VERSAO_CAPACITY'],
                'PER_ESCALA' => $data['PER_ESCALA'],
                'AG_4HS' => $data['AG_4HS'],
                'AG_6HS' => $data['AG_6HS'],
                'AG_7HS' => $data['AG_7HS'],
                'HC_CLIENTE' => $data['HC_CLIENTE'],
                'FTE_AJUSTADO' => $data['FTE_AJUSTADO'],
                'TX_OCUP' => $data['TX_OCUP']
            ];

            $key = [
                'ANO' => $data['ANO'],
                'MES' => $data['MES'],
                'DATA' => $data['DATA'],
                'CD_DPTO' => $data['CD_DPTO'],
            ];

            PlanningDimSlaP2::updateOrcreate($key, $newData);
        } catch (\Throwable $e) {

            static::$failLines =  static::$failLines + 1;
            static::$errors['line_error']['message'] = "(" . static::$failLines . ") linhas não carregadas";
            throw new \Exception("Linha não carregada | " . $e->getMessage() . " | " . json_encode($data));
        }
    }

    protected static function chargeHC(array $data)
    {
        try {

            $newData = [
                'month_ref' => $data['month_ref'],
                'sector_n1_id' => $data['CD_SETOR'],
                'date_ref' => $data['DT_DATA'],
                'training_dim' => $data['NU_TREINO'],
                'training_initial_dim' => $data['NU_TR_INI'],
                'training_migration_dim' => $data['NU_TR_MIG'],
                'hc_dim' => $data['NU_HC'],
                'vacation_dim' => $data['NU_FERIAS'],
                'total_dim' => $data['NU_TOTAL'],
                'hd_client_dim' => $data['NU_CLI'],
                'manager_n4_id' => $data['CD_MAT_GERENTE_RELAC'],
                'manager_n3_id' => $data['CD_MAT_GERENTE_LOCAL'],
                'manager_n5_id' => $data['CD_MAT_DIRETOR'],
                'version' => $data['NU_VERSAO']
            ];

            HeadCount::create($newData);
        } catch (\Throwable $th) {
            throw new \Exception("Dado não carregado");
        }
    }

    /**
     * Processa uma linha do CSV.
     */
    protected static function chargeHCAfter()
    {
        $fileLoad = static::$fileLoad;
        $headCounts = HeadCount::where('month_ref', $fileLoad->month_ref)->get();

        try {

            foreach ($headCounts as $headCount) {
                HeadCountService::setRealData($headCount);
                HeadCountService::copyMisPrimary($headCount);
            }

            $fileLoad->status = PlanningFileInterface::STATUS_COMPLETED;
            $fileLoad->save();
        } catch (\Throwable $e) {
            static::$errors['charge_error']['message'] = "Erro ao carregar no destino";
            throw new \Exception($e->getMessage());
        }
    }

    protected static function appendRowToCSV(array  $header, string $filePath): void
    {
        if (!empty($allData)) {

            $header[] = 'charge_error'; // Adiciona a coluna de erros no cabeçalho

            // Abre o arquivo para escrita no storage do Laravel
            $filePathToWrite = Storage::disk(PlanningFileInterface::STORAGE_FILE_DISK)->path($filePath); // Caminho absoluto do arquivo
            $fileHandle = fopen($filePathToWrite, 'a'); // Modo de adição

            if ($fileHandle) {
                // Escreve o cabeçalho apenas se o arquivo estiver vazio
                if (ftell($fileHandle) == 0) {
                    fputcsv($fileHandle, $header, ';'); // Escreve o cabeçalho no arquivo
                }

                // Escreve todas as linhas processadas no arquivo
                foreach ($allData as $line) {
                    fputcsv($fileHandle, $line, ';');
                }

                // Fecha o arquivo após escrever
                fclose($fileHandle);
            } else {
                throw new \Exception("Não foi possível abrir o arquivo para escrita.");
            }
        }
    }

    protected static function chargeFinish()
    {
        $fileLoad = static::$fileLoad;
    }
}
