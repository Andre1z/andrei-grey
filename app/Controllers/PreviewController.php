<?php
namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use Exception;

class PreviewController {

    /**
     * Lee el archivo (ODS o Excel) ubicado en uploads y lo muestra en pantalla como HTML.
     *
     * @param string $fileName Nombre del archivo a visualizar.
     * @return void
     */
    public function preview(string $fileName): void {
        // Directorio donde se suben los archivos (asegúrate de que sea correcto)
        $uploadsDir = __DIR__ . '/../../public/uploads/';
        $filePath = $uploadsDir . $fileName;

        // Verificar que el archivo exista
        if (!file_exists($filePath)) {
            echo "Archivo no encontrado: " . htmlspecialchars($fileName);
            exit;
        }

        // Determinar la extensión del archivo para elegir el lector apropiado
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if ($extension === 'ods') {
            $reader = new Ods();
        } elseif (in_array($extension, ['xlsx', 'xls'])) {
            $reader = new Xlsx();
        } else {
            echo "Tipo de archivo no soportado: " . htmlspecialchars($extension);
            exit;
        }

        try {
            $spreadsheet = $reader->load($filePath);
        } catch (Exception $e) {
            echo "Error al cargar el archivo: " . $e->getMessage();
            exit;
        }

        // Usar el escritor HTML para renderizar el contenido en pantalla
        $writer = new Html($spreadsheet);
        $writer->setSheetIndex(0); // Opcional: seleccionar la primera hoja

        // Establecer las cabeceras para salida HTML
        header('Content-Type: text/html; charset=utf-8');
        $writer->save('php://output');
        exit;
    }
}