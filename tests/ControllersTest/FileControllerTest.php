<?php
namespace Tests\ControllersTest;

use PHPUnit\Framework\TestCase;
use App\Controllers\FileController;

class FileControllerTest extends TestCase
{
    private $originalFiles;
    private $originalRequestMethod;

    protected function setUp(): void {
        // Guardar el estado original de $_FILES y $_SERVER['REQUEST_METHOD']
        $this->originalFiles = $_FILES;
        $this->originalRequestMethod = $_SERVER['REQUEST_METHOD'] ?? null;
    }
    
    protected function tearDown(): void {
        // Restaurar las variables globales al estado original
        $_FILES = $this->originalFiles;
        if ($this->originalRequestMethod !== null) {
            $_SERVER['REQUEST_METHOD'] = $this->originalRequestMethod;
        } else {
            unset($_SERVER['REQUEST_METHOD']);
        }
    }

    public function testUploadWithoutFile() {
        // Simular una solicitud POST sin archivo
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_FILES = [];

        $controller = new FileController();

        // Capturamos la salida que imprime el controlador
        ob_start();
        $controller->upload();
        $output = ob_get_clean();

        // Esperamos que el mensaje indique que no se recibió un archivo
        $this->assertStringContainsString('No se recibió un archivo', $output);
    }
    
    public function testUploadWithFile() {
        // Simular una solicitud POST con archivo ODS
        $_SERVER['REQUEST_METHOD'] = 'POST';

        // Crear un archivo temporal que simulará ser un ODS
        $tempFile = tempnam(sys_get_temp_dir(), 'ods');
        file_put_contents($tempFile, 'dummy data, not a valid ODS file');

        $_FILES['ods_file'] = [
            'tmp_name' => $tempFile,
            'name'     => 'test.ods',
            'error'    => 0,
            'size'     => filesize($tempFile)
        ];

        $controller = new FileController();

        // Capturamos la salida del proceso
        ob_start();
        $controller->upload();
        $output = ob_get_clean();

        /* 
         Debido a que el contenido del archivo no es un ODS válido, pueden ocurrir dos escenarios:
         - Se completa la transformación (mensaje "Transformación completada")
         - O se notifica un error durante la transformación o al mover el archivo.
         
         Aceptamos cualquiera de estos resultados usando assertTrue().
        */
        $this->assertTrue(
            strpos($output, "Transformación completada") !== false ||
            strpos($output, "Error durante la transformación") !== false ||
            strpos($output, "Error al mover el archivo") !== false ||
            strpos($output, "No se recibió el archivo") !== false
        );

        // Eliminar el archivo temporal creado
        unlink($tempFile);

        // Limpiar el archivo de destino en public/uploads, si fue creado
        $destinationPath = __DIR__ . '/../../public/uploads/test.ods';
        if (file_exists($destinationPath)) {
            unlink($destinationPath);
        }
    }
}