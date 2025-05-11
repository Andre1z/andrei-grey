<?php
namespace Tests\ModelsTest;

use PHPUnit\Framework\TestCase;
use App\Models\Database;
use PDO;

class DatabaseTest extends TestCase
{
    /**
     * Instancia de la clase Database
     *
     * @var Database
     */
    private $database;

    protected function setUp(): void
    {
        // Instanciar el objeto Database.
        $this->database = new Database();
    }

    /**
     * Verifica que getConnection() devuelva una instancia de PDO.
     */
    public function testGetConnectionReturnsPDO()
    {
        $pdo = $this->database->getConnection();
        $this->assertInstanceOf(PDO::class, $pdo, "getConnection() debe devolver una instancia de PDO");
    }

    /**
     * Ejecuta una consulta simple y verifica el resultado.
     */
    public function testExecuteSimpleQuery()
    {
        $pdo = $this->database->getConnection();
        $query = "SELECT 1 AS test";
        $statement = $pdo->query($query);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals(1, $result['test'], "La consulta simple debe retornar 1");
    }

    /**
     * Crea una tabla de prueba, inserta un dato, lo consulta y finalmente la elimina.
     */
    public function testDatabaseTableCreationAndDrop()
    {
        $pdo = $this->database->getConnection();

        // Crear una tabla de prueba.
        $createTableSQL = "CREATE TABLE IF NOT EXISTS test_table (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            value TEXT
        )";
        $pdo->exec($createTableSQL);

        // Insertar un registro.
        $pdo->exec("INSERT INTO test_table (value) VALUES ('sample')");

        // Consultar el registro insertado.
        $statement = $pdo->query("SELECT value FROM test_table WHERE id = 1");
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals('sample', $result['value'], "El registro insertado debe ser 'sample'");

        // Eliminar la tabla de prueba.
        $pdo->exec("DROP TABLE test_table");
    }
}