<?php
namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\Reader\Ods;
use Exception;

class PreviewController {

    /**
     * Extrae el contenido del archivo ODS y carga la vista de previsualización.
     *
     * @param string $fileName Nombre del archivo ODS a visualizar.
     * @return void
     */
    public function preview(string $fileName): void {
        // Definir la carpeta donde se encuentran los archivos subidos
        // Observa que el archivo subido se encuentra en /public/uploads/
        $uploadsDir = __DIR__ . '/../../public/uploads/';
        $filePath = $uploadsDir . $fileName;

        // Verificar que el archivo exista
        if (!file_exists($filePath)) {
            echo "Archivo no encontrado: " . htmlspecialchars($fileName);
            exit;
        }

        // Instanciar el lector ODS
        $reader = new Ods();

        try {
            $spreadsheet = $reader->load($filePath);
        } catch (Exception $e) {
            echo "Error al leer el archivo ODS: " . $e->getMessage();
            exit;
        }

        // Convertir la hoja activa a un array
        $data = $spreadsheet->getActiveSheet()->toArray();

        // Incluir la vista de previsualización.
        // Se asume que la vista se encuentra en: public/view/preview.php
        include __DIR__ . '/../../public/view/preview.php';
    }
}