# andrei | grey

andrei | grey es una aplicación web en PHP que permite gestionar la importación y previsualización de archivos ODS, con soporte multiidioma. El proyecto incluye:

- Sistema de Autenticación: Login y registro con almacenamiento de usuarios en un archivo JSON, con contraseñas hasheadas.
- Traducciones Dinámicas: Utiliza un archivo CSV (ubicado en /translations/translations.csv) para cargar las traducciones en múltiples idiomas (inglés, español, francés, alemán, italiano, japonés, coreano y chino).
- Importación y Previsualización de archivos ODS: Permite subir archivos ODS y visualizar su contenido en una tabla HTML mediante la librería PhpSpreadsheet.
- Estilos Modernos: Diferentes estilos para las páginas de autenticación (login/register) y el dashboard, utilizando hojas de estilo CSS personalizadas (incluyendo auth.css para las pantallas de login y registro).

----------------------------------------------------

Tabla de Contenidos
-------------------
- Requisitos
- Instalación
- Estructura del Proyecto
- Uso
- Traducciones
- Notas Adicionales
- Licencia

----------------------------------------------------

Requisitos
----------
- Servidor PHP: Versión 7.2 o superior (se recomienda utilizar XAMPP o similar para desarrollo local).
- Composer: Para gestionar las dependencias.
- Extensión PHP Zip: Debe estar habilitada para utilizar PhpSpreadsheet.
- SQLite: (opcional) Para otros procesos de almacenamiento o transformación que pueda requerir la aplicación.

----------------------------------------------------

Instalación
-----------
1. Clonar el repositorio:

   git clone https://tu-servidor-repositorio/andrei-grey.git
   cd andrei-grey

2. Instalar dependencias con Composer:

   composer install

3. Configurar el entorno:
   - Asegúrate de que la extensión ZipArchive esté habilitada en tu archivo php.ini.
   - Revisa y, de ser necesario, edita el archivo app/config.php para definir la constante BASE_URL, por ejemplo:
     
     define('BASE_URL', '/andrei-grey/andrei-grey/public');

4. Carpetas y permisos:
   - Crea una carpeta storage en la raíz del proyecto para almacenar el archivo users.json.
   - Crea la carpeta uploads dentro de /public para que se guarden los archivos ODS subidos.
   - Asegúrate de que ambas carpetas tengan permisos de escritura.

5. Ubicación de las Traducciones:
   - Coloca el archivo translations.csv en la carpeta /translations (ubicada en la raíz del proyecto).

6. Configurar Servidor Local:
   - Utiliza XAMPP, WAMP o similar y configura el directorio raíz apuntando a /public para acceder a la aplicación.

----------------------------------------------------

Estructura del Proyecto
------------------------
```
andrei-grey/
│
├── app/
│   ├── config.php          # Definiciones globales y constantes (e.g., BASE_URL)
│   ├── Controllers/        # Controladores (FileController, etc.)
│   └── Helpers/            # Clases auxiliares (e.g., Translation.php)
│
├── public/
│   ├── css/
│   │   ├── auth.css        # Estilos para Login y Registro
│   │   └── otros.css       # Otros estilos (o estilos generales)
│   ├── index.php           # Dashboard – sube archivo ODS y muestra el listado/previsualización
│   ├── login.php           # Página de login
│   ├── register.php        # Página de registro
│   ├── logout.php          # Cierre de sesión
│   ├── view/
│   │   └── preview.php     # Previsualización de archivos ODS
│   └── uploads/            # Carpeta donde se suben los archivos ODS
│
├── storage/
│   └── users.json         # Archivo JSON para almacenar usuarios registrados
│
├── translations/
│   └── translations.csv   # Archivo CSV con las traducciones en múltiples idiomas
│
├── vendor/                # Dependencias instaladas a través de Composer
└── README.md              # Este archivo de documentación
```

----------------------------------------------------

Uso
---
1. Autenticación:
   - Accede a la página de login en http://localhost/andrei-grey/andrei-grey/public/login.php.
   - Selecciona el idioma deseado; la interfaz se traducirá automáticamente y se guardará la opción en una cookie para usarla posteriormente incluso después de cerrar sesión.
   - Si aún no tienes una cuenta, usa el enlace a Register para crear una nueva cuenta. Los datos de usuarios se almacenarán en el archivo storage/users.json.

2. Dashboard:
   - Una vez autenticado, serás redirigido a index.php, donde podrás:
     - Subir un archivo ODS a través de un formulario.
     - Ver la previsualización del archivo ODS cargado en un iframe.
     - Consultar el listado de archivos subidos.

3. Previsualización:
   - Al hacer clic en un archivo del listado, se cargará la vista de previsualización (preview.php) que utiliza PhpSpreadsheet para convertir y mostrar el contenido del ODS en una tabla HTML.
   - La interfaz de previsualización se adapta al idioma seleccionado.

4. Cierre de sesión:
   - Utiliza el enlace de Logout para cerrar sesión y volver a la página de login, la cual se mostrará en el idioma que el usuario eligió en su sesión anterior, gracias a la cookie guardada.

----------------------------------------------------

Traducciones
------------
El proyecto utiliza un archivo CSV ubicado en /translations/translations.csv para gestionar la traducción de los textos. El archivo incluye claves para:

- Dashboard: dashboard_title, hello, logout, importer_title, importer_heading, enter_ods_url, upload_button, tables
- Autenticación: login_title, username, password, login_button, invalid_credentials, login_no_account
- Registro: register_title, register_username, register_password, register_confirm_password, register_button, register_fill_fields, register_password_mismatch, register_user_exists, register_error_saving, register_have_account
- Previsualización: preview_title, file_not_found
  
Puedes modificar este archivo para agregar o ajustar las traducciones según las necesidades de tu aplicación.

----------------------------------------------------

Notas Adicionales
-----------------
- Seguridad:
  Las contraseñas se almacenan utilizando password_hash() y se validan con password_verify().
  Asegúrate de mantener actualizadas las dependencias y de seguir buenas prácticas de seguridad para la gestión de archivos y usuarios.

- Dependencias:
  El proyecto depende de PhpSpreadsheet (https://phpspreadsheet.readthedocs.io/) para leer archivos ODS.
  Se recomienda revisar la documentación de dicha librería para posibles actualizaciones.

- Extensiones PHP:
  Es necesario tener habilitada la extensión ZipArchive para que PhpSpreadsheet funcione correctamente con archivos ODS.

----------------------------------------------------

Licencia
--------
Este proyecto se distribuye bajo la Licencia MIT.
