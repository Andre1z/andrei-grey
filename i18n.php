<?php
/**
 * Funciones para el parseo de fórmulas y para la internacionalización.
 */

// Se reutiliza la función de parseo de fórmulas
function parseOdsFormulaToPhp($odsFormula, $colMap = []) {
    // (Utiliza el mismo contenido que en funciones/parseFormula.php)
    $formula = preg_replace('/^of:=/i', '', trim($odsFormula));
    $formula = preg_replace_callback(
        '/

\[\.([A-Z]+)(\d+)\]

/i',
        function ($matches) use ($colMap) {
            $colLetter = strtoupper($matches[1]);
            if (isset($colMap[$colLetter])) {
                $fieldName = $colMap[$colLetter];
                return "\$this->cellVal(\$row['$fieldName'])";
            } else {
                return "/* unknown_col_$colLetter */ 0";
            }
        },
        $formula
    );
    $mapFunctions = [
        '/\bSUM\s*\(/i'   => '$this->sum(',
        '/\bROUND\s*\(/i' => '$this->round(',
    ];
    foreach ($mapFunctions as $pattern => $replace) {
        $formula = preg_replace($pattern, $replace, $formula);
    }
    return $formula;
}

function cellVal($val) {
    $num = preg_replace('/[^0-9.\-]+/', '', $val);
    return floatval($num);
}

function sum(...$vals) {
    $total = 0;
    foreach ($vals as $v) {
        $total += $v;
    }
    return $total;
}

function roundVal($val, $precision = 0) {
    return round($val, $precision);
}

/**
 * Función simple de traducción que utiliza el archivo translations.csv.
 */
function t($key) {
    static $translations = null;
    if ($translations === null && file_exists(__DIR__ . '/../translations.csv')) {
        $translations = [];
        if (($handle = fopen(__DIR__ . '/../translations.csv', "r")) !== false) {
            $header = fgetcsv($handle, 1000, ",");
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $translations[$data[0]] = array_combine($header, $data);
            }
            fclose($handle);
        }
    }
    $lang = $_SESSION['lang'] ?? 'en';
    if (isset($translations[$key]) && isset($translations[$key][$lang])) {
        return $translations[$key][$lang];
    }
    return $key;
}
?>