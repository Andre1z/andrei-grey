<?php
namespace App\Models;

class Database {
    /**
     * Instancia de PDO que representa la conexión a la base de datos.
     *
     * @var \PDO
     */
    private \PDO $pdo;

    /**
     * Constructor.
     * Conecta automáticamente a la base de datos al instanciar la clase.
     */
    public function __construct() {
        $this->connect();
    }

    /**
     * Establece la conexión a la base de datos SQLite.
     *
     * Si la constante DB_PATH no está definida (por ejemplo, en el archivo de configuración),
     * se define de forma predeterminada apuntando a '/storage/database.sqlite'.
     *
     * @return void
     */
    private function connect(): void {
        // Verificar si DB_PATH ya está definida en el entorno, de lo contrario definirla.
        if (!defined('DB_PATH')) {
            define('DB_PATH', __DIR__ . '/../../storage/database.sqlite');
        }
        
        $dsn = "sqlite:" . DB_PATH;

        try {
            $this->pdo = new \PDO($dsn);
            // Configuramos PDO para mostrar excepciones ante errores
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            // Finaliza la ejecución en caso de error y muestra el mensaje correspondiente.
            die("Error al conectar con la base de datos: " . $e->getMessage());
        }
    }

    /**
     * Devuelve la instancia de conexión PDO.
     *
     * @return \PDO La conexión a la base de datos.
     */
    public function getConnection(): \PDO {
        return $this->pdo;
    }
}