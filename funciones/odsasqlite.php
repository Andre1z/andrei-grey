<?php
/**
 * Data Importer → SQLite Importer with Basic Formula-to-PHP Generation and Extended Foreign Key Support
 *
 * Este importador:
 *  1) Descarga un archivo ODS a partir de una URL.
 *  2) Extrae el archivo content.xml.
 *  3) Crea una base de datos SQLite llamada "$dbName.db".
 *  4) Crea una tabla por cada hoja.
 *  5) Inserta filas de la hoja.
 *  6) Detecta celdas con fórmulas y genera archivos PHP que replican esa lógica.
 *  7) Implementa conversión extendida de claves foráneas.
 *
 * Nota: Este ejemplo es simplificado y no cubre todos los casos extremos.
 */

include "parseFormula.php";

/**
 * Limpia y normaliza un nombre de columna.
 */
function cleanColumnName($name) {
    $normalized = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
    $clean = preg_replace('/[^a-zA-Z0-9_]/', '_', $normalized);
    $clean = preg_replace('/_+/', '_', $clean);
    $clean = trim($clean, '_');
    if (preg_match('/^\d/', $clean)) {
        $clean = '_' . $clean;
    }
    return ($clean !== '') ? $clean : 'column';
}

/**
 * Detecta si el nombre de la columna sigue el patrón para clave foránea.
 */
function detectForeignKey($colName) {
    $parts = explode('_', $colName);
    if (count($parts) >= 2) {
        return [
            'referenced_table' => $parts[0],
            'display_columns'  => array_slice($parts, 1),
            'original_name'    => $colName
        ];
    }
    return false;
}

/**
 * Función principal para importar el archivo ODS a SQLite.
 *
 * @param string $fileUrl URL del archivo ODS.
 * @param string $dbName  Nombre base para la base de datos.
 */
function odsasqlite($fileUrl, $dbName)
{
    // 1) Descargar el archivo ODS a una ubicación temporal
    $tempFile = tempnam(sys_get_temp_dir(), 'ods');
    file_put_contents($tempFile, file_get_contents($fileUrl));

    // 2) Extraer content.xml del archivo ODS
    $zip = new ZipArchive;
    if ($zip->open($tempFile) === TRUE) {
        $xmlContent = $zip->getFromName('content.xml');
        $zip->close();
    } else {
        die("Error al abrir el archivo ODS.");
    }

    // 3) Cargar el XML y obtener namespaces
    $xml = simplexml_load_string($xmlContent);
    $namespaces = $xml->getNamespaces(true);

    // 4) Navegar hasta el elemento <office:spreadsheet>
    $office = $xml->children($namespaces['office']);
    $spreadsheet = $office->body->spreadsheet;

    // 5) Crear/abrir la base de datos SQLite usando PDO
    try {
        $db = new PDO('sqlite:' . $dbName . '.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Error en la conexión SQLite: " . $e->getMessage());
    }

    // Se guardarán las fórmulas: $columnFormulas[tableName][columnIndex] = <formula>
    $columnFormulas = [];

    // 6) Procesar cada hoja
    $tables = $spreadsheet->children($namespaces['table'])->table;
    if ($tables) {
        foreach ($tables as $table) {
            // 6.a Obtener el nombre de la hoja y limpiarlo
            $tableAttrs = $table->attributes($namespaces['table']);
            $sheetName = (string)$tableAttrs['name'];
            $sheetNameClean = preg_replace('/[^a-zA-Z0-9_]/', '_', $sheetName);
            if (preg_match('/^\d/', $sheetNameClean)) {
                $sheetNameClean = '_' . $sheetNameClean;
            }

            // Inicializar mapeo de claves foráneas para esta hoja
            $fkMapping = [];
            $columnFormulas[$sheetNameClean] = [];

            // 6.b Procesar la fila de encabezado: generar nombres de columna y detectar claves foráneas
            $headerRow = $table->children($namespaces['table'])->{'table-row'}[0];
            $headerCells = $headerRow->children($namespaces['table'])->{'table-cell'};
            $columns = [];
            $colMapping = [];
            $pos = 0;
            foreach ($headerCells as $cell) {
                $cellAttrs = $cell->attributes($namespaces['table']);
                $repeat = isset($cellAttrs['number-columns-repeated']) ? (int)$cellAttrs['number-columns-repeated'] : 1;
                $text = $cell->children($namespaces['text'])->{'p'};
                $cellValue = trim((string)$text);
                for ($i = 0; $i < $repeat; $i++) {
                    if ($cellValue !== '') {
                        $colNameClean = cleanColumnName($cellValue);
                        // Revisar si sigue patrón de clave foránea
                        $fkData = detectForeignKey($colNameClean);
                        if ($fkData) {
                            $fkMapping[count($columns)] = $fkData;
                        }
                        // Evitar duplicados en el nombre de la columna
                        $original = $colNameClean;
                        $suffix = 1;
                        while (in_array($colNameClean, $columns)) {
                            $colNameClean = $original . '_' . $suffix;
                            $suffix++;
                        }
                        $columns[] = $colNameClean;
                        $colMapping[$pos] = count($columns) - 1;
                    }
                    $pos++;
                }
            }
            if (empty($columns)) {
                continue;
            }

            // 6.c Crear la tabla principal – INTEGER para claves foráneas, TEXT para el resto
            $createSQL = "CREATE TABLE IF NOT EXISTS \"$sheetNameClean\" (id INTEGER PRIMARY KEY AUTOINCREMENT";
            foreach ($columns as $idx => $col) {
                if (isset($fkMapping[$idx])) {
                    $createSQL .= ", \"$col\" INTEGER";
                } else {
                    $createSQL .= ", \"$col\" TEXT";
                }
            }
            $createSQL .= ")";
            $db->exec($createSQL);

            // 6.d Crear tablas referenciadas para claves foráneas
            if (!empty($fkMapping)) {
                foreach ($fkMapping as $meta) {
                    $refTable = $meta['referenced_table'];
                    $displayCols = $meta['display_columns'];
                    $fkTableSQL = "CREATE TABLE IF NOT EXISTS \"$refTable\" (id INTEGER PRIMARY KEY AUTOINCREMENT";
                    foreach ($displayCols as $col) {
                        $fkTableSQL .= ", \"$col\" TEXT";
                    }
                    $fkTableSQL .= ")";
                    $db->exec($fkTableSQL);
                }
            }

            // 6.e Preparar la sentencia INSERT para la tabla principal
            $colList = implode(', ', array_map(fn($c) => "\"$c\"", $columns));
            $placeholders = implode(', ', array_fill(0, count($columns), '?'));
            $insertSQL = "INSERT INTO \"$sheetNameClean\" ($colList) VALUES ($placeholders)";
            $stmtInsert = $db->prepare($insertSQL);

            // 6.f Procesar las filas de datos (descontando el encabezado)
            $rows = $table->children($namespaces['table'])->{'table-row'};
            for ($r = 1; $r < count($rows); $r++) {
                $rowElem = $rows[$r];
                $cells = $rowElem->children($namespaces['table'])->{'table-cell'};
                $rowDataAll = [];
                $pos = 0;
                foreach ($cells as $cell) {
                    $cellAttrs = $cell->attributes($namespaces['table']);
                    $repeat = isset($cellAttrs['number-columns-repeated']) ? (int)$cellAttrs['number-columns-repeated'] : 1;
                    // Detectar celdas con fórmula
                    if (isset($cellAttrs['formula'])) {
                        $cellFormula = (string)$cellAttrs['formula'];
                        for ($rep = 0; $rep < $repeat; $rep++) {
                            if (isset($colMapping[$pos + $rep])) {
                                $colIndex = $colMapping[$pos + $rep];
                                $columnFormulas[$sheetNameClean][$colIndex] = $cellFormula;
                            }
                        }
                    }
                    $text = $cell->children($namespaces['text'])->{'p'};
                    $cellValue = trim((string)$text);
                    for ($rep = 0; $rep < $repeat; $rep++) {
                        $rowDataAll[$pos] = $cellValue;
                        $pos++;
                    }
                }
                $finalRow = array_fill(0, count($columns), '');
                foreach ($colMapping as $cellPos => $colIndex) {
                    $finalRow[$colIndex] = $rowDataAll[$cellPos] ?? '';
                }
                // Saltar filas vacías
                $allEmpty = true;
                foreach ($finalRow as $val) {
                    if ($val !== '') { $allEmpty = false; break; }
                }
                if ($allEmpty) { continue; }

                // 6.g Procesar columnas foráneas: convertir texto en ID de la tabla referenciada
                if (!empty($fkMapping)) {
                    foreach ($fkMapping as $colIndex => $fkMeta) {
                        $cellValue = $finalRow[$colIndex];
                        if (trim($cellValue) === '') continue;
                        $refTable = $fkMeta['referenced_table'];
                        $displayColumns = $fkMeta['display_columns'];
                        $parts = preg_split('/\s+/', trim($cellValue));
                        while (count($parts) < count($displayColumns)) {
                            $parts[] = '';
                        }
                        $conditions = [];
                        $params = [];
                        foreach ($displayColumns as $i => $colName) {
                            $conditions[] = "\"$colName\" = ?";
                            $params[] = $parts[$i];
                        }
                        $whereClause = implode(" AND ", $conditions);
                        $stmt = $db->prepare("SELECT id FROM \"$refTable\" WHERE $whereClause LIMIT 1");
                        $stmt->execute($params);
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($result && isset($result['id'])) {
                            $finalRow[$colIndex] = $result['id'];
                        } else {
                            $colsList = implode(', ', array_map(fn($col) => "\"$col\"", $displayColumns));
                            $placeholdersFK = implode(', ', array_fill(0, count($displayColumns), '?'));
                            $insertStmt = $db->prepare("INSERT INTO \"$refTable\" ($colsList) VALUES ($placeholdersFK)");
                            $insertStmt->execute($params);
                            $finalRow[$colIndex] = $db->lastInsertId();
                        }
                    }
                }
                // 6.h Insertar la fila procesada en la tabla principal
                $stmtInsert->execute($finalRow);
            }

            // 6.i Generar archivos PHP para las columnas con fórmulas
            if (!empty($columnFormulas[$sheetNameClean])) {
                $methodsRoot = __DIR__ . '/../metodos';
                if (!is_dir($methodsRoot)) {
                    mkdir($methodsRoot, 0777, true);
                }
                $tableMethodsDir = $methodsRoot . "/$sheetNameClean";
                if (!is_dir($tableMethodsDir)) {
                    mkdir($tableMethodsDir, 0777, true);
                }
                $alphabet = range('A','Z');
                $colMap = [];
                foreach ($columns as $idx => $colName) {
                    if (isset($alphabet[$idx])) {
                        $colMap[$alphabet[$idx]] = $colName;
                    }
                }
                foreach ($columnFormulas[$sheetNameClean] as $colIdx => $rawFormula) {
                    $colName = $columns[$colIdx];
                    $phpExpr = parseOdsFormulaToPhp($rawFormula, $colMap);
                    $stub  = "<?php\n";
                    $stub .= "/**\n";
                    $stub .= " * Auto-generated method for column '$colName' (ODS formula: $rawFormula)\n";
                    $stub .= " * This file replicates the formula in PHP.\n";
                    $stub .= " * \$row is provided by run_method.php.\n";
                    $stub .= " */\n\n";
                    $stub .= "class FormulaRunner {\n";
                    $stub .= "    public function cellVal(\$val) {\n";
                    $stub .= "        \$num = preg_replace('/[^0-9.\\-]+/', '', \$val);\n";
                    $stub .= "        return floatval(\$num);\n";
                    $stub .= "    }\n\n";
                    $stub .= "    public function sum(...\$vals) {\n";
                    $stub .= "        \$total = 0;\n";
                    $stub .= "        foreach (\$vals as \$v) { \$total += \$v; }\n";
                    $stub .= "        return \$total;\n";
                    $stub .= "    }\n\n";
                    $stub .= "    public function roundVal(\$val, \$precision=0) {\n";
                    $stub .= "        return round(\$val, \$precision);\n";
                    $stub .= "    }\n\n";
                    $stub .= "    public function run(\$row) {\n";
                    $stub .= "        \$result = $phpExpr;\n";
                    $stub .= "        echo \"<p>Formula: " . addslashes($rawFormula) . "</p>\";\n";
                    $stub .= "        echo \"<p>Result: {\$result}</p>\";\n";
                    $stub .= "    }\n";
                    $stub .= "}\n\n";
                    $stub .= "\$runner = new FormulaRunner();\n";
                    $stub .= "\$runner->run(\$row);\n";
                    file_put_contents($tableMethodsDir . "/$colName.php", $stub);
                }
            }
            echo "Processed sheet: $sheetName\n";
        }
    } else {
        echo "No sheets found in the document.\n";
    }
    unlink($tempFile);
}
?>