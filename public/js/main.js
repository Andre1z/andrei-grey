// public/js/main.js

// Ejecutar el código cuando el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function () {
  // Obtener el input de archivo y el formulario
  const fileInput = document.getElementById('ods_file');
  const form = document.querySelector('form');

  // Manejo del evento "change" para el input de archivo
  // Permite mostrar en consola o en un elemento (si se desea) el nombre del archivo seleccionado
  if (fileInput) {
    fileInput.addEventListener('change', function (event) {
      let fileName = '';
      if (event.target.files && event.target.files.length > 0) {
        fileName = event.target.files[0].name;
      }
      // Si existe un elemento con id "fileNameDisplay", se actualiza su contenido
      const fileNameDisplay = document.getElementById('fileNameDisplay');
      if (fileNameDisplay) {
        fileNameDisplay.textContent = 'Archivo seleccionado: ' + fileName;
      } else {
        // Si no existe, se imprime en consola el nombre del archivo
        console.log('Archivo seleccionado:', fileName);
      }
    });
  }

  // Manejo del envío del formulario para mostrar feedback al usuario
  if (form) {
    form.addEventListener('submit', function (e) {
      // Se deshabilita el botón de envío y se cambia su texto para indicar que se está procesando la solicitud
      const submitButton = form.querySelector('button[type="submit"]');
      if (submitButton) {
        submitButton.disabled = true;
        submitButton.textContent = 'Procesando...';
      }
      // Se puede agregar aquí algún indicador visual (como un spinner) si se desea
    });
  }
});