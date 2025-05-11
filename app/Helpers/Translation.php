<?php
namespace App\Helpers;

class Translation
{
    /**
     * Array con las traducciones cargadas.
     *
     * @var array
     */
    private array $translations = [];

    /**
     * Idioma por defecto.
     *
     * @var string
     */
    private string $defaultLanguage = 'en';

    /**
     * Constructor.
     *
     * @param string      $csvFilePath Ruta del archivo CSV de traducciones.
     * @param string|null $language    Idioma deseado (por ejemplo, 'es', 'fr', etc.). Si es null se usará el idioma por defecto.
     *
     * @throws \Exception Si el archivo CSV no existe o está vacío.
     */
    public function __construct(string $csvFilePath, ?string $language = null)
    {
        $this->loadCSV($csvFilePath, $language);
    }

    /**
     * Carga el archivo CSV y parsea las traducciones.
     *
     * Se espera que el CSV tenga la siguiente estructura:
     * key,en,es,fr,de,it,ja,ko,zh
     * login_title,Login,Iniciar sesión,Connexion,Anmelden,Accedi,ログイン,로그인,登录
     * ...
     *
     * @param string      $csvFilePath Ruta del archivo CSV.
     * @param string|null $language    Idioma deseado.
     *
     * @return void
     *
     * @throws \Exception Si el archivo CSV no existe o el encabezado no es válido.
     */
    private function loadCSV(string $csvFilePath, ?string $language = null): void
    {
        if (!file_exists($csvFilePath)) {
            throw new \Exception("El archivo CSV de traducciones no se encuentra: {$csvFilePath}");
        }

        // Establece el idioma seleccionado o usa el predeterminado.
        $selectedLanguage = $language ?? $this->defaultLanguage;

        // Abrir el archivo CSV.
        if (($handle = fopen($csvFilePath, 'r')) !== false) {
            // Leer el encabezado para identificar las columnas.
            $headers = fgetcsv($handle);
            if ($headers === false) {
                throw new \Exception("El archivo CSV está vacío o el encabezado no es válido.");
            }

            // Buscar el índice de la columna correspondiente al idioma seleccionado.
            $languageIndex = array_search($selectedLanguage, $headers);
            if ($languageIndex === false) {
                // Si no se encuentra el idioma seleccionado, se utiliza el por defecto.
                $languageIndex = array_search($this->defaultLanguage, $headers);
            }

            // Parsear cada fila y almacenar la traducción
            while (($data = fgetcsv($handle)) !== false) {
                // La primera columna es la clave para la traducción.
                $key = trim($data[0]);
                // Asigna la traducción desde la columna correspondiente al idioma.
                $this->translations[$key] = isset($data[$languageIndex]) ? trim($data[$languageIndex]) : $key;
            }
            fclose($handle);
        }
    }

    /**
     * Devuelve la traducción correspondiente a una clave.
     *
     * @param string $key Clave de la traducción.
     *
     * @return string La cadena traducida o la clave si no se encuentra la traducción.
     */
    public function get(string $key): string
    {
        return $this->translations[$key] ?? $key;
    }
}