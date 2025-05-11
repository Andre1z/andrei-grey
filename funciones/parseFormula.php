<?php
/**
 * Convierte una fórmula ODS (ej. "of:=[.C7]*0.21") en una cadena de código PHP.
 *
 * @param string $odsFormula La fórmula en ODS.
 * @param array  $colMap     Array asociativo que relaciona referencias de celda con nombres de campo.
 *                          Ej: ['C' => 'price', 'B' => 'quantity'].
 *
 * Retorna una cadena de código PHP, ej.: "$this->cellVal(\$row['price']) * 0.21"
 *
 * Este ejemplo simplificado maneja:
 * - Referencias a celdas individuales (ignorando el número de fila).
 * - Operadores aritméticos básicos y algunas funciones (SUM, ROUND).
 */
function parseOdsFormulaToPhp($odsFormula, $colMap = [])
{
    // Quitar el prefijo "of:=" si lo tiene
    $formula = preg_replace('/^of:=/i', '', trim($odsFormula));

    // Convertir referencias como "[.C7]" a $this->cellVal($row['price'])
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

    // Reemplazar algunas funciones ODS por métodos auxiliares en PHP
    $mapFunctions = [
        '/\bSUM\s*\(/i'   => '$this->sum(',
        '/\bROUND\s*\(/i' => '$this->round(',
    ];
    foreach ($mapFunctions as $pattern => $replace) {
        $formula = preg_replace($pattern, $replace, $formula);
    }

    return $formula;
}

/**
 * Helper para convertir una cadena (ej. "34.00 €") a un float.
 */
function cellVal($val) {
    $num = preg_replace('/[^0-9.\-]+/', '', $val);
    return floatval($num);
}

/**
 * Helper para sumar valores.
 */
function sum(...$vals) {
    $total = 0;
    foreach ($vals as $v) {
        $total += $v;
    }
    return $total;
}

/**
 * Helper para redondear un valor.
 */
function roundVal($val, $precision = 0) {
    return round($val, $precision);
}
?>