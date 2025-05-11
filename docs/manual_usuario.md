Manual de Usuario

Este manual proporciona instrucciones básicas para utilizar la aplicación andrei | grey. Siga estos pasos para gestionar la autenticación, la carga de archivos ODS, la previsualización y otros aspectos clave de la aplicación.

------------------------------------------------------------
1. Acceso a la aplicación
------------------------------------------------------------
- Abra su navegador y diríjase a la siguiente URL:
  http://localhost/andrei-grey/andrei-grey/public/login.php

- La página de Login se mostrará en el idioma seleccionado (o por defecto en inglés), de acuerdo con la configuración y las traducciones cargadas.

------------------------------------------------------------
2. Iniciar Sesión (Login)
------------------------------------------------------------
- En la página de Login, encontrará:
  • Un formulario para ingresar su nombre de usuario y contraseña.
  • Un menú desplegable para seleccionar el idioma en el que desea ver la interfaz (por ejemplo, English, Español, Français, etc.).
  • Un botón “Login” para enviar sus credenciales.

- Ingrese sus datos. Si sus credenciales son correctas, se iniciará su sesión y será redirigido al Dashboard de la aplicación.
- Si los datos son incorrectos, se mostrará un mensaje de error indicando “Invalid username or password” (o el equivalente en el idioma seleccionado).

- En caso de no tener una cuenta, puede hacer clic en el enlace “Register” para dirigirse a la página de registro.

------------------------------------------------------------
3. Registro de Usuario (Register)
------------------------------------------------------------
- Si aún no posee una cuenta, haga clic en el enlace “Register” en la página de Login.
- En la página de Registro, complete los siguientes campos:
  • Nombre de Usuario
  • Contraseña
  • Confirmar Contraseña

- Presione el botón “Register” para enviar el formulario.
- Si el registro es exitoso, se le redirigirá a la página de Login para iniciar sesión con sus nuevas credenciales.
- Si existen errores (por ejemplo, campos vacíos, contraseñas que no coinciden o el usuario ya existe), se mostrará un mensaje de error adecuado.

------------------------------------------------------------
4. Uso del Dashboard
------------------------------------------------------------
- Una vez que haya iniciado sesión correctamente, será redirigido al Dashboard.
- En el Dashboard encontrará:
  • Un formulario para la subida de archivos ODS. Utilice la sección “Importer” para seleccionar y subir su archivo.
  • Una lista de archivos subidos, donde podrá visualizar cada archivo haciendo clic sobre él.
  • Un enlace “Logout” para cerrar sesión.

------------------------------------------------------------
5. Previsualización de Archivos ODS
------------------------------------------------------------
- Al seleccionar un archivo de la lista, se abrirá la página de previsualización.
- Esta página mostrará el contenido del archivo ODS en forma de tabla HTML, utilizando la librería PhpSpreadsheet para procesar el archivo.
- La interfaz y los mensajes (como “File not found”) se mostrarán en el idioma que haya seleccionado.
- Para volver al Dashboard, utilice el enlace “Volver al inicio” disponible en la parte inferior de la página.

------------------------------------------------------------
6. Cierre de Sesión (Logout)
------------------------------------------------------------
- Para cerrar sesión, haga clic en el enlace “Logout” (generalmente ubicado en el header o en una sección claramente identificada).
- Al cerrar sesión, será redirigido a la página de Login, la cual se mostrará en el idioma que había seleccionado anteriormente (almacenado en la cookie).

------------------------------------------------------------
7. Consideraciones Adicionales
------------------------------------------------------------
- La aplicación utiliza un archivo CSV de traducciones para adaptar la interfaz al idioma seleccionado por el usuario.
- Las contraseñas se almacenan de forma segura utilizando hash (por ejemplo, password_hash() en PHP).
- Los archivos ODS se suben a la carpeta “uploads” y se procesan para su visualización.
- Asegúrese de que las carpetas “uploads” y “storage” tengan permisos de escritura para el correcto funcionamiento de la aplicación.

Si necesita más información o asistencia, consulte la documentación técnica o contacte al administrador del sistema.

------------------------------------------------------------
Fin del Manual de Usuario