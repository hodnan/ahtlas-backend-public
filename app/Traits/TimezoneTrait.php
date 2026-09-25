<?php

namespace App\Traits;

use Illuminate\Support\Carbon;

trait TimezoneTrait
{
    protected function formatWithTimezone($value, $format = null)
    {
        if (!$value) return;

        $format = $format ?: 'Y-m-d H:i:s.u';

        // Extrair o tipo de cast a partir do formato (se existir)
        preg_match('/^datetime:(.+)$/', $format, $matches);

        if (isset($matches[1])) {
            // Se for um cast de datetime, use o formato correspondente
            return Carbon::parse($value)->timezone('America/Sao_Paulo')->format($matches[1]);
        } else {
            // Se não for um cast de datetime, use o formato padrão
            return Carbon::parse($value)->timezone('America/Sao_Paulo')->format($format);
        }
    }

    public function getApprovedAtAttribute($value)
    {
        return $this->formatWithTimezone($value, $this->casts['approved_at'] ?? null);
    }

    public function getStartAtAttribute($value)
    {

        return $this->formatWithTimezone($value, $this->casts['start_at'] ?? null);
    }

    public function getEndAtAttribute($value)
    {
        return $this->formatWithTimezone($value, $this->casts['end_at'] ?? null);
    }

    public function getCreatedAtAttribute($value)
    {
        return $this->formatWithTimezone($value, $this->casts['created_at'] ?? null);
    }

    public function getUpdatedAtAttribute($value)
    {
        return $this->formatWithTimezone($value, $this->casts['updated_at'] ?? null);
    }
}
