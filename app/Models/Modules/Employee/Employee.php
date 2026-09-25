<?php

namespace App\Models\Modules\Employee;

use App\Models\Core\UserAvatar;
use App\Services\Modules\Employee\EmployeeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class Employee extends Model
{
    use HasFactory, TimezoneTrait;

    protected $connection = 'modules';
    protected $table = 'employees';
    public $incrementing = false;

    protected $fillable = [
        'username',
        'name',
        'nickname',
        'uf',
        'position',
        'position_summary',
        'sector_n1_id',
        'sector_n2_id',
        'manager_n1_id',
        'manager_n2_id',
        'manager_n3_id',
        'manager_n4_id',
        'manager_n5_id',
        'hierarchical_level',
        'type',
        'staff',
        'admission',
        'dismissal',
        'birthdate',
        'is_veteran',
        'start_time',
        'working_hours',
        'active',
        'status'
    ];

    // protected $fillable = [];    
    protected function casts(): array
    {
        return [
            'working_hours' => 'datetime: H:i',
        ];
    }
    public $appends = [
        'label',
        'active_label'
    ];

    public function getLabelAttribute(): string|bool
    {
        $position_summary = $this->position_summary ? ' - ' . $this->position_summary : '';

        return $this->username . ' - ' . $this->name . $position_summary;
    }

    public function avatar()
    {
      
        return $this->belongsTo(UserAvatar::class, 'username', 'username')->withDefault(function ($avatar)  {
            $filePath = public_path('assets/avatar/default.jpg');
            if (file_exists($filePath)) {
                $imageData = file_get_contents($filePath);
                $avatar->avatar = $imageData; // Salva os dados binários da imagem
            } else {
                // Define um valor padrão caso o arquivo não exista
                $avatar->avatar = null;
            }
            $avatar->nickname =  '' ;
        });
    }

    public function getHierarchicalLevelAttribute()
    {
       
        $position_summary = $this->attributes['position_summary'] ? [ 'id' =>  0 , 'label' => $this->attributes['position_summary']] : null;
        

        return $this->attributes['hierarchical_level'] ? EmployeeInterface::HIERARCHICAL_LEVEL[$this->attributes['hierarchical_level']] : $position_summary;
    }
    
    public function getActiveLabelAttribute()
    {
        $active = isset($this->attributes['active']) ?  $active = $this->attributes['active']: null;

        if($active == 0) $active = [ 'id' => 0 , 'label' => 'Desligado'];
        if($active == 1) $active = [ 'id' => 1 , 'label' => 'Ativo'];
        if($active == null) $active = [ 'id' => null , 'label' => 'Não se aplica'];
        
        return  $active;
    }

    public function getNicknameAttribute()
    {
        $nickname = $this->attributes['nickname'] ?? $this->attributes['name'];     
        
        return  $nickname;
    }

    public function sectorN1()
    {
        return $this->belongsTo(SectorN1::class, 'sector_n1_id', 'id')->select('id', 'name');
    }

    public function sectorN2()
    {
        return $this->belongsTo(SectorN2::class, 'sector_n2_id', 'id')->select('id', 'name');
    }

    public function managerN1()
    {
        return $this->belongsTo(Employee::class, 'manager_n1_id', 'username')
        // ->where('hierarchical_level', 1)
        ->select('username', 'name', 'position_summary', 'hierarchical_level', 'active' );
    }
   
    public function managerN2()
    {
        return $this->belongsTo(Employee::class, 'manager_n2_id', 'username')
        // ->where('hierarchical_level', 2)
        ->select('username', 'name', 'position_summary', 'hierarchical_level', 'active' );
    }

    public function managerN3()
    {
        return $this->belongsTo(Employee::class, 'manager_n3_id', 'username')
        // ->where('hierarchical_level', 3)
        ->select('username', 'name', 'position_summary', 'hierarchical_level', 'active' );
    }

    public function managerN4()
    {
        return $this->belongsTo(Employee::class, 'manager_n4_id', 'username')
        // ->where('hierarchical_level', 4)
        ->select('username', 'name', 'position_summary', 'hierarchical_level', 'active' );
    }

    public function managerN5()
    {
        return $this->belongsTo(Employee::class, 'manager_n5_id', 'username')
        // ->where('hierarchical_level', 5)
        ->select('username', 'name', 'position_summary', 'hierarchical_level', 'active' );
    }
}
