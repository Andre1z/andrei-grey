<?php
namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\Reader\Ods;
use Exception;

class PreviewController {

    /**
     * Extrae el contenido del archivo ODS y carga la vista de previsualización.
     *
     * @param string $fileName Nombre del archivo ODS a mostrar.
     * @return void
     */
    public function preview(string $fileName): void {
        // Definir la carpeta donde se encuentran los archivos subidos
        $uploadsDir = __DIR__ . '/../../public/uploads/';
        $filePath = $uploadsDir . $fileName;

        // Verificar que el archivo existe
        if (!file_exists($filePath)) {
            echo "Archivo no encontrado: " . htmlspecialchars($fileName);
            exit;
        }

        // Instanciar el lector ODS de PhpSpreadsheet
        $reader = new Ods();

        try {
            $spreadsheet = $reader->load($filePath);
        } catch (Exception $e) {
            echo "Error al leer el archivo ODS: " . $e->getMessage();
            exit;
        }

        // Obtener los datos de la hoja activa en un array
        $data = $spreadsheet->getActiveSheet()->toArray();

        // Incluir la vista de previsualización, pasando las variables $fileName y $data
        include __DIR__ . '/../../public/views/preview.php';
    }
}
// Fin de la clase PreviewController