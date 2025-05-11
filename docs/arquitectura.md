andrei-grey/
├── app/
│   ├── config.php          # Configuración global y constantes (por ejemplo, BASE_URL)
│   ├── Controllers/        # Controladores (por ejemplo, FileController.php)
│   └── Helpers/            # Clases auxiliares (por ejemplo, Translation.php)
├── public/
│   ├── css/
│   │   ├── auth.css        # Estilos modernos para Login y Registro
│   │   └── otros.css       # Otros estilos generales
│   ├── index.php           # Dashboard: formulario de carga ODS y listado/previsualización
│   ├── login.php           # Página de Login
│   ├── register.php        # Página de Registro
│   ├── logout.php          # Cierre de sesión
│   ├── view/
│   │   └── preview.php     # Previsualización de archivos ODS usando PhpSpreadsheet
│   └── uploads/            # Carpeta donde se guardan los archivos ODS subidos
├── storage/
│   └── users.json          # Archivo JSON para almacenar usuarios registrados (con contraseñas hasheadas)
├── translations/
│   └── translations.csv    # Archivo CSV con las traducciones en múltiples idiomas
├── vendor/                # Dependencias instaladas a través de Composer
└── README.md              # Documentación del proyecto