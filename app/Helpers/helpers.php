<?php

use Illuminate\Support\Str;

// Esta função recebe uma string e a transforma em uma frase com a primeira letra de cada palavra em maiúscula,
// exceto para palavras específicas definidas em outras funções.
if (!function_exists('ucfirstException')) {
    function ucfirstException($string)
    {
        // Limpa o texto do excesso de espaços
        $string = preg_replace('/( )+/', ' ', $string);

        // Coloca todo o texto em maiúsculas
        $string = mb_convert_case($string, MB_CASE_TITLE, 'UTF-8');

        // Separa o texto por palavras
        $arr = explode(' ', $string);

        $tempArray = [];

        // percorre as palavras
        foreach ($arr as $value) {
            $subTempArray = [];

            // substitui as palavras que devem ser minúsculas
            $word = keepLC($value);
            // substitui as palavras que devem ser maiúsculas
            $word = keepUC($word);

            // Verifica se há subpalavras separadas por hífen
            if (strpos($word, '-')) {
                $subWords = explode('-', $word); // Corrigido de $string para $word

                foreach ($subWords as $subValue) {
                    // substitui as palavras que devem ser minúsculas
                    $subWord = keepLC($subValue);
                    // substitui as palavras que devem ser maiúsculas
                    $subWord = keepUC($subWord);

                    // salva no array temporário
                    array_push($subTempArray, $subWord);
                }

                $word = trim(implode('-', $subTempArray));
            }

            // salva no array temporário
            array_push($tempArray, $word);
        }

        // junta as palavras novamente
        return trim(implode(' ', $tempArray));
    }
}

// Esta função recebe uma string e verifica se ela está na lista de palavras que devem permanecer em minúsculas.
// Se estiver, a função retorna a string em minúsculas. Caso contrário, retorna a string original em maiúsculas.
if (!function_exists('keepLC')) {
    function keepLC($string)
    {
        // Lista de palavras que devem permanecer em minúsculas
        $keepLC = ['A', 'E', 'I', 'O', 'U', 'DA', 'DAS', 'DE', 'DES', 'DI', 'DIS', 'DO', 'DOS', 'DU', 'DUS'];

        // Limpa a string e a torna maiúscula para comparação
        $cleanWord = cleanWord($string);

        foreach ($keepLC as $value) {
            // Se a palavra estiver na lista, retorna em minúsculas
            $string = $cleanWord === $value ? mb_strtolower($string, 'UTF-8') : $string;
        }

        return $string;
    }
}

// Esta função recebe uma string e verifica se ela está na lista de palavras que devem permanecer em maiúsculas.
// Se estiver, a função retorna a string em maiúsculas. Caso contrário, retorna a string original em minúsculas.
if (!function_exists('keepUC')) {
    function keepUC($string)
    {
        // Lista de palavras que devem permanecer em maiúsculas
        $keepUC = ['UC', 'APFM', 'FTTH', 'MPRJ', 'XDSL', 'CIP', 'CPNB', 'BPO', 'CP1', 'CPC', 'DACC', 'CIOSP', 'RH', 'BO', 'PF', 'TLV', 'PJ', 'WLL', 'CE', 'CGS', 'PPV', 'CRV', 'ST', 'SKY', 'UF', 'RG', 'CPF', 'I', 'II', 'III', 'IV', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII', 'XIII', 'XIV', 'XV', 'XVI', 'XVII', 'XVIII', 'XVIII'];

        // Limpa a string e a torna maiúscula para comparação
        $cleanWord = cleanWord($string);

        foreach ($keepUC as $value) {
            // Se a palavra estiver na lista, retorna em maiúsculas
            $string = $cleanWord == $value ? mb_strtoupper($string, 'UTF-8') : $string;
        }

        return $string;
    }
}

if (!function_exists('cleanWord')) {
    function cleanWord($string)
    {
        // remove todos os caracteres especiais
        $cleanWord = preg_replace('/&([a-z])[a-z]+;/i', '$1', htmlentities(trim($string)));
        $cleanWord = preg_replace("/[^a-zA-Z\s]/", '', $cleanWord);
        $cleanWord = mb_strtoupper(preg_replace('~\P{Xan}+~u', '', $cleanWord));
        $cleanWord = htmlentities(trim($cleanWord));

        return $cleanWord;
    }
}

if (!function_exists('validateDate')) {
    function validateDate($date, $format = 'Y-m-d'): bool
    {
        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) == $date;
    }
}

if (!function_exists('makeCsv')) {
    function makeCsv($data)
    {
        ob_start();
        $output = fopen('php://output', 'w');

        // Adiciona o BOM para compatibilidade com programas como o Excel
        fwrite($output, "\xEF\xBB\xBF");

        // Definir o delimitador como ponto e vírgula
        $delimiter = ';';

        // Verifica se $data é uma coleção ou um array
        if (is_array($data)) {
            $firstRow = $data[0] ?? [];
        } else {
            $firstRow = $data->first() ? $data->first()->toArray() : [];
        }

        // Verifica se a primeira linha não está vazia
        if (!empty($firstRow)) {
            $columnNames = array_keys($firstRow);
            fputcsv($output, $columnNames, $delimiter);
        }

        foreach ($data as $trade) {
            // Se $data é uma coleção, converte cada item para array
            $rowData = is_array($trade) ? $trade : $trade->toArray();
            fputcsv($output, $rowData, $delimiter);
        }

        fclose($output);
        return ob_get_clean();
    }
}

if (!function_exists('normalizeString')) {
    function normalizeString($string)
    {
        // Converte para UTF-8
        $string = mb_convert_encoding($string, 'UTF-8', 'auto');

        // Remove os acentos
        $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);

        // Converte para maiúsculas
        $string = strtoupper($string);

        // Substitui espaços por underscores
        $string = str_replace(' ', '_', $string);

        // Remove caracteres especiais
        $string = preg_replace('/[^A-Z0-9_]/', '', $string);

        // Substitui underscores duplicados
        $string = preg_replace('/_+/', '_', $string);

        return $string;
    }
}

if (!function_exists('publicId')) {
    function publicId()
    {
        $timestamp = now()->timestamp; // Timestamp atual
        $base36Timestamp = base_convert($timestamp, 10, 36); // Converte para base 36
        $randomSuffix = Str::random(4); // 4 caracteres aleatórios
        $id = $base36Timestamp . '-' . $randomSuffix;

        return $id;
    }
}
