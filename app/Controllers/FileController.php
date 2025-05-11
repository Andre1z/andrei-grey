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
    public function upload() {
        // Verificar que la solicitud sea POST y se haya enviado el archivo
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['ods_file'])) {
            $fileTmpPath = $_FILES['ods_file']['tmp_name'];

            // Sanitizamos el nombre del archivo para evitar posibles vulnerabilidades
            $fileName = preg_replace('/[^a-zA-Z0-9_\.-]/', '', $_FILES['ods_file']['name']);

            // Definir el directorio destino para los archivos subidos (ubicado en public/uploads/)
            $uploadsDir = __DIR__ . '/../../public/uploads/';
            
            // Se asegura de que el directorio de uploads exista, creándolo si es necesario
            if (!is_dir($uploadsDir)) {
                mkdir($uploadsDir, 0755, true);
            }
            
            // Ruta completa destino del archivo subido
            $destinationPath = $uploadsDir . $fileName;

            // Mover el archivo desde la ubicación temporal a la carpeta de uploads
            if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                try {
                    // Instanciar el servicio de transformación y procesar el archivo ODS
                    $transformationService = new TransformationService();
                    $transformationService->transform($destinationPath);

                    echo "Transformación completada exitosamente.";
                } catch (\Exception $e) {
                    // Manejo de cualquier error que se produzca durante la transformación
                    echo "Error durante la transformación: " . $e->getMessage();
                }
            } else {
                echo "Error al mover el archivo subido.";
            }
        } else {
            // Notificar que no se recibió un archivo o la petición no es POST
            echo "No se recibió un archivo ODS por petición POST.";
        }
    }
}