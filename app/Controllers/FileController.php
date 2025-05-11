<?php
namespace App\Controllers;

use App\Services\TransformationService;

/**
 * Class FileController
 *
 * Este controlador gestiona la carga y procesamiento de archivos ODS.
 */
class FileController {

    /**
     * Maneja la carga del archivo ODS y llama al servicio de transformación.
     *
     * Se espera que la solicitud se realice con el método POST y que el archivo se envíe 
     * en el input con nombre 'ods_file'.
     *
     * @return void
     */
    public function upload(): ?string {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['ods_file'])) {
        $fileTmpPath = $_FILES['ods_file']['tmp_name'];
        $fileName = preg_replace('/[^a-zA-Z0-9_\.-]/', '', $_FILES['ods_file']['name']);
        $uploadsDir = __DIR__ . '/../../public/uploads/';
        
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }
        
        $destinationPath = $uploadsDir . $fileName;
        
        if (move_uploaded_file($fileTmpPath, $destinationPath)) {
            try {
                $transformationService = new TransformationService();
                $transformationService->transform($destinationPath);
                // Retornar el nombre del archivo subido
                return $fileName;
            } catch (\Exception $e) {
                echo "Error durante la transformación: " . $e->getMessage();
                return null;
            }
        } else {
            echo "Error al mover el archivo subido.";
            return null;
        }
    } else {
        echo "No se recibió un archivo ODS por petición POST.";
        return null;
    }
}
}