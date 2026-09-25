<?php

namespace App\Services\Modules\Administration;

use App\Models\Modules\Administration\Intelligence\Indicator;
use App\Models\Modules\Administration\Intelligence\KpiRelated;
use App\Models\Modules\Administration\Incentives\RV\Term;
use App\Models\Modules\Administration\Incentives\RV\TermPayment;
use App\Models\Modules\Employee\Employee;
use App\Models\Modules\Employee\SectorN1;
use App\Services\Core\User\MetadataService;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class TermService
{
    public static function termsList($request = null)
    {
        $filterSectorsN1 = isset($request['sectorsN1']) ? explode(',',$request['sectorsN1']) : null;
        $filterStatus = isset($request['status']) ? array_map('intval', explode(',', $request['status'])) : null;
        $filterDateFef = isset($request['month_ref']) ? Carbon::parse($request['month_ref'])->firstOfMonth()->toDateString() : Carbon::now()->format('Y-m-01');

        return  Term::with(['owner'])
            ->where('month_ref', $filterDateFef)
            ->where(function ($query) use ($filterSectorsN1, $filterStatus) {
                if (!empty($filterSectorsN1)) {                  
                    $query->whereIn('sector_n1_id', $filterSectorsN1);
                }
                if (!empty($filterStatus)) {
                    $query->whereIn('status', $filterStatus);
                }
            })
            ->get(['id', 'term_name', 'owner', 'status', 'created_at','updated_at',  'approved_at', 'month_ref']);
    }

    public static function termStore($request)
    {
        try {
            $data =  self::getDefaultData($request);
            if(! Auth::user() && $request['updated_by']){               
                $data['created_by'] = $request['created_by'];
                $data['updated_by'] = $request['updated_by'];
            }

            self::cancelOldVersion($request); // Cencela versões antigas
            $term = Term::create($data); // Gera um novo termo
            self::storeTermFile($term->id);        
        } catch (\Throwable $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function getTermById($termId, $username = null)
    {
        try {
            $term =  Term::with(['sectorN1', 'sectorN2', 'payment', 'owner'])->find($termId);
            $accelerator = collect($term->accelerator)
            ->groupBy('indicator_id') // Agrupa por indicator_id
            ->map(function ($group) {
                return $group->max('value'); // Pega o maior valor de cada grupo
            })
            ->sum(); // Soma os maiores valores
        
            $accelerator = $accelerator/100;
            $term->max_accelerated = $accelerator;
            $term->basket = collect($term->basket)->map(function($item) use ( $accelerator) {
                $item['value_accelerated'] = $item['value']  *  ($accelerator +1);
                return $item;
              });

            return $term;
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }
    
    public static function storeTermFile($termId, $username = null)
    {
        $term = TermService::getTermById($termId, $username);

        $position = strtolower($term->position['short']);

        $termPDF = $term;

        // Carregar a view Blade e obter o conteúdo HTML 
        $pdf = new Dompdf();
        $pdf->loadHTML(view("templates.rv.term-$position", ['term' => $termPDF]));

        $pdf->render();

        // Converter o conteúdo do PDF para base64
        $base64Pdf = base64_encode($pdf->output());

        $term->term = $base64Pdf;

        unset($term->max_accelerated);

        $term->save();
    }

    public static function cancelOldVersion($request)
    {
        $data = $request->all();

        Term::where('month_ref', date('Y/m/d', strtotime($data['month_ref'])))
            ->where('sector_n1_id', $data['sector_n1_id'])
            ->where('sector_n2_id', $data['sector_n2_id'])
            ->where('campaign', $data['campaign'])
            ->where('position', $data['position'])
            ->where('level', $data['level'])
            ->update(['status' => TermInterface::STATUS_CANCELED]);
    }

    public  static function getDefaultData($request)
    {
        $data = $request->all();
       
        $version = $data['version'] ?? self::getNewVersion($request);

        $newData['month_ref'] = Carbon::parse($data['month_ref'])->format('Y-m-d');
        $newData['owner'] = $data['owner'];
        $newData['sector_n1_id'] = $data['sector_n1_id'];
        $newData['sector_n2_id'] = $data['sector_n2_id'];
        $newData['campaign'] = $data['campaign'];
        $newData['payment_id'] = $data['payment_id'];
        $newData['position'] = $data['position'];
        $newData['level'] = $data['level'];
        $newData['roof'] = $data['roof'];
        $newData['apprentice'] = $data['apprentice'];
  
        $newData['term_name'] = Carbon::parse($data['month_ref'])->format('Y-m')
            . "_" . str_pad($data['sector_n1_id'], 6, '0', STR_PAD_LEFT)
            . "_" . str_pad($data['sector_n2_id'], 6, '0', STR_PAD_LEFT)
            . "_V" . $version
            . "_" . TermInterface::POSITIONS[$data['position']]['short']
            . "_" . TermInterface::LEVELS[$data['level']]['short']
            . "_" . $data['campaign'];
        $newData['version'] = $version;
        $newData['sector_n2_id'] = $data['sector_n2_id'] ?? 0;
        $newData['notes'] = $data['notes'] ;
        $newData['due_date_at'] = self::getDueDate();
        $newData['basket'] = isset($data['basket']) ? self::getIndicatorData($data['basket']) : [];
        $newData['accelerator'] = isset($data['accelerator']) ? self::getIndicatorData($data['accelerator']) : [];
        $newData['deflator'] = isset($data['deflator']) ? self::getIndicatorData($data['deflator']) : [];
        $newData['elimination'] = isset($data['elimination']) ? self::getIndicatorData($data['elimination']) : [];

        if (isset($data['status'])) {
            $newData['status'] =  $data['status']['id'] ?? $data['status'];
        } else {

            $newData['status'] = TermInterface::STATUS_STANDBY;
        }

        $newData['created_meta'] = MetadataService::getMetadata($request);
        return $newData;
    }

    public static function getIndicatorData($directives)
    {
        $directivesTemp = [];

        $temp = [];

        foreach ($directives as $value) {
            $indicator = Indicator::find($value['indicator_id']);
            $temp['indicator_id'] = $indicator->id;
            $temp['indicator_name'] = $indicator->name;
            if (isset($value['range'])) {
                $temp['range'] = $value['range'];
            }
            $temp['operation'] = $value['operation'];
            $temp['calc'] = $value['calc'];
            $temp['target'] = $value['target'];
            if (isset($value['value'])) {
                $temp['value'] = (float) $value['value'] ;
                $temp['value_label'] = number_format($value['value'], 2, ',', '.') . $indicator->simbol;
            }
            $temp['target_label'] = $value['target'] . $indicator->simbol;
            $directivesTemp[] = $temp;
        }
        return $directivesTemp;
    }

    public static function getDueDate($dateTime = null)
    {
        $dateTime = $dateTime ? Carbon::parse($dateTime) : Carbon::now();

        for ($i = 0; $i < 3; $i++) {
            $dateTime->addDay();
            while ($dateTime->isWeekend()) {
                $dateTime->addDay();
            }
        }

        return $dateTime;
    }

    public static function getNewVersion($request)
    {
        $data = $request->all();

        $latestVersion = Term::where('month_ref', date('Y/m/d', strtotime($data['month_ref'])))
            ->where('sector_n1_id', $data['sector_n1_id'])
            ->where('sector_n2_id', $data['sector_n2_id'])
            ->where('campaign', $data['campaign'])
            ->where('position', $data['position'])
            ->where('level', $data['level'])
            ->max('version');

        // Incrementa a versão
        $newVersion = $latestVersion + 1;

        return $newVersion;
    }

    public static function getOwners()
    {
        return Employee::with(['avatar'])->whereIn('sector_n1_id', [2672, 1302, 2584, 2693, 2583, 2609, 2226,2433])->get(['username', 'name', 'position_summary', 'hierarchical_level', 'active']);
    }

    public static function getPayments()
    {
        return TermPayment::get(['id', 'event']);
    }

    public static function getSectors()
    {
        return SectorN1::with(['sectorsN2'])->orderBy('id', 'asc')->get(['id', 'name', 'uf']);
    }

    public static function getKpis()
    {
        return KpiRelated::with(['indicator'])->get(['sector_n1_id', 'indicator_id']);
    }
}
