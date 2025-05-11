<?php
namespace App\Services;

use PhpOffice\PhpSpreadsheet\Reader\Ods;
use App\Models\Database;
use PDO;
use Exception;

class TransformationService {
    
    /**
     * Procesa el archivo ODS y lo transforma insertando los datos en SQLite.
     *
     * @param string $filePath Ruta completa del archivo ODS.
     * @return void
     * @throws Exception Si ocurre algún error durante la transformación.
     */
    public function transform($filePath) {
        // Verificar si el archivo existe
        if (!file_exists($filePath)) {
            throw new Exception("El archivo especificado no existe: {$filePath}");
        }

        // Instanciar el lector ODS de PhpSpreadsheet
        $reader = new Ods();
        try {
            $spreadsheet = $reader->load($filePath);
        } catch (Exception $e) {
            throw new Exception("Error al cargar el archivo ODS: " . $e->getMessage());
        }
        
        $sheet = $spreadsheet->getActiveSheet();
        $sheetData = $sheet->toArray();

        // Validar que se tengan datos dentro del archivo
        if (count($sheetData) < 1) {
            throw new Exception("El archivo ODS no contiene datos.");
        }

        // Conectarse a la base de datos usando la clase Database
        $database = new Database();
        $pdo = $database->getConnection();

        // Creación de la tabla de ejemplo en caso de que no exista.
        // Esta tabla es solo un ejemplo y se adapta a 4 columnas.
        $createTableSQL = "CREATE TABLE IF NOT EXISTS imported_data (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            col1 TEXT,
            col2 TEXT,
            col3 TEXT,
            col4 TEXT
        )";
        $pdo->exec($createTableSQL);

        // Suponemos que la primera fila es el encabezado y se omite,
        // mientras que las siguientes son los datos a insertar.
        $header = array_shift($sheetData);

        // Preparar la sentencia para insertar datos.
        // Para este ejemplo se toman las primeras 4 columnas de cada fila.
        $insertSQL = "INSERT INTO imported_data (col1, col2, col3, col4) 
                      VALUES (:col1, :col2, :col3, :col4)";
        $stmt = $pdo->prepare($insertSQL);

        // Recorrer los datos y ejecutar la inserción por cada fila.
        foreach ($sheetData as $row) {
            // Nos aseguramos de que la fila tenga 4 columnas.
            // array_pad agrega valores nulos en caso de faltar columnas.
            $rowData = array_pad($row, 4, null);

            // Ejecutar la inserción con los valores correspondientes.
            $stmt->execute([
                ':col1' => $rowData[0],
                ':col2' => $rowData[1],
                ':col3' => $rowData[2],
                ':col4' => $rowData[3],
            ]);
        }
    }
}