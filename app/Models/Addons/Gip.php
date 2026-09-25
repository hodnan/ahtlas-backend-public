<?php

namespace App\Models\Addons;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gip extends Model
{
    use HasFactory;
    protected $connection = 'hr_oracle';
    protected $table = 'SIWEBAPIGIP.GIP_CONS_QUADRO_UNIFICADO';
    public $timestamps = false;
    protected $primarykey = null;
    public $incrementing = false;
    protected $fillable = []; // defina um array vazio para prevenir atribuição em massa

    // Sobrescreva os métodos para lançar uma exceção
    public function create(array $attributes = [])
    {
        throw new \Exception('Operação de criação não permitida.');
    }

    public function update(array $attributes = [], array $options = [])
    {
        throw new \Exception('Operação de atualização não permitida.');
    }

    public function delete()
    {
        throw new \Exception('Operação de exclusão não permitida.');
    }

    public function save(array $options = [])
    {
        throw new \Exception('Operação de salvamento não permitida.');
    }

}
