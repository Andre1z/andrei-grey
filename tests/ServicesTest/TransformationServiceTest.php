<?php
namespace Tests\ServicesTest;

use PHPUnit\Framework\TestCase;
use App\Models\Database;
use App\Services\TransformationService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods;

class TransformationServiceTest extends TestCase
{
    /**
     * Ruta del archivo de base de datos de test.
     *
     * @var string
     */
    protected static $testDbPath;

    /**
     * Ruta del archivo ODS temporal creado para los tests.
     *
     * @var string
     */
    protected static $tempOdsFile;

    /**
     * Se ejecuta antes de ejecutar cualquier test de esta clase.
     * Se define DB_PATH para el entorno de test, evitando conflictos con la base de datos de producción.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        // Definir una ruta específica para la base de datos de test si no está definida.
        if (!defined('DB_PATH')) {
            define('DB_PATH', __DIR__ . '/test_database.sqlite');
        }
        self::$testDbPath = DB_PATH;
    }

    /**
     * Se ejecuta después de ejecutar todos los tests de esta clase.
     * Se encarga de limpiar los archivos creados durante el test (archivo de DB y ODS temporal).
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        // Eliminar el archivo de base de datos de test si existe.
        if (file_exists(self::$testDbPath)) {
            unlink(self::$testDbPath);
        }
        // Eliminar el archivo ODS temporal si existe.
        if (isset(self::$tempOdsFile) && file_exists(self::$tempOdsFile)) {
            unlink(self::$tempOdsFile);
        }
    }

    /**
     * Test para verificar que el método transform inserte correctamente los datos del archivo ODS en la base de datos.
     *
     * @return void
     */
    public function testTransformInsertsDataIntoDatabase()
    {
        // Crear un Spreadsheet con datos de ejemplo.
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Definir la primera fila como encabezado (será omitida durante la transformación).
        $sheet->setCellValue('A1', 'Header1');
        $sheet->setCellValue('B1', 'Header2');
        $sheet->setCellValue('C1', 'Header3');
        $sheet->setCellValue('D1', 'Header4');
        
        // Definir una segunda fila con datos a insertar.
        $sheet->setCellValue('A2', 'Data1');
        $sheet->setCellValue('B2', 'Data2');
        $sheet->setCellValue('C2', 'Data3');
        $sheet->setCellValue('D2', 'Data4');

        // Crear un archivo ODS temporal.
        self::$tempOdsFile = tempnam(sys_get_temp_dir(), 'ods_test_') . '.ods';
        $writer = new Ods($spreadsheet);
        $writer->save(self::$tempOdsFile);

        // Instanciar el servicio de transformación y procesar el archivo ODS.
        $transformationService = new TransformationService();
        $transformationService->transform(self::$tempOdsFile);

        // Abrir la conexión a la base de datos de test.
        $database = new Database();
        $pdo = $database->getConnection();

        // Consultar los datos insertados en la tabla imported_data.
        $stmt = $pdo->query("SELECT * FROM imported_data");
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Verificar que se haya insertado al menos una fila (la fila de datos, ya que el encabezado fue omitido).
        $this->assertNotEmpty($result, "La tabla imported_data no debe estar vacía después de la transformación.");

        // Verificar que los datos insertados sean los esperados.
        // Se toma la primera (y única) fila insertada.
        $insertedRow = $result[0];
        $this->assertEquals('Data1', $insertedRow['col1'], "La columna col1 debe contener 'Data1'.");
        $this->assertEquals('Data2', $insertedRow['col2'], "La columna col2 debe contener 'Data2'.");
        $this->assertEquals('Data3', $insertedRow['col3'], "La columna col3 debe contener 'Data3'.");
        $this->assertEquals('Data4', $insertedRow['col4'], "La columna col4 debe contener 'Data4'.");
    }
}