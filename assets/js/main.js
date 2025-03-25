// Función para confirmar el respaldo
function confirmBackup() {
    console.log('confirmBackup ejecutado');
    if (confirm('¿Estás seguro de que deseas hacer un respaldo?')) {
        console.log('Redirigiendo a backup.php');
        window.location.href = 'backup.php';
    }
}

// Función para configurar los botones de archivos
function setupFileButtons() {
    const deleteButtons = document.querySelectorAll('.delete-btn');
    const editButtons = document.querySelectorAll('.edit-btn');
    const downloadButtons = document.querySelectorAll('.download-btn');

    // Limpiar listeners anteriores
    deleteButtons.forEach(button => {
        button.replaceWith(button.cloneNode(true)); // Clonar el botón para eliminar listeners
    });
    editButtons.forEach(button => {
        button.replaceWith(button.cloneNode(true)); // Clonar el botón para eliminar listeners
    });
    downloadButtons.forEach(button => {
        button.replaceWith(button.cloneNode(true)); // Clonar el botón para eliminar listeners
    });

    // Agregar nuevos listeners
    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const fileId = this.dataset.fileId;
            eliminarArchivo(fileId);
        });
    });

    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const fileId = this.dataset.fileId;
            editarArchivo(fileId);
        });
    });

    downloadButtons.forEach(button => {
        button.addEventListener('click', function () {
            const fileName = this.dataset.fileName;
            descargarArchivo(fileName);
        });
    });
}

// Función para eliminar un archivo
function eliminarArchivo(id) {
    if (confirm('¿Estás seguro de que deseas eliminar este archivo?')) {
        fetch('eliminar_archivo.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + encodeURIComponent(id),
        })
        .then(response => response.text())
        .then(data => {
            alert(data); // Mostrar la respuesta del servidor
            location.reload(); // Recargar la página para actualizar la lista de archivos
        })
        .catch(error => {
            console.error('Error al eliminar el archivo:', error);
            alert('Error al eliminar el archivo.');
        });
    }
}

// Función para editar un archivo
function editarArchivo(id) {
    const nuevoNombre = prompt('Introduce el nuevo nombre del archivo:');
    if (nuevoNombre) {
        fetch('editar_archivo.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + encodeURIComponent(id) + '&nombre_archivo=' + encodeURIComponent(nuevoNombre),
        })
        .then(response => response.text())
        .then(data => {
            alert(data); // Mostrar la respuesta del servidor
            location.reload(); // Recargar la página para actualizar la lista de archivos
        })
        .catch(error => {
            console.error('Error al editar el archivo:', error);
            alert('Error al editar el archivo.');
        });
    }
}

// Función para descargar un archivo
function descargarArchivo(nombreArchivo) {
    if (confirm("¿Estás seguro de que deseas descargar este archivo?")) {
        fetch('verificar_archivo.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'nombre_archivo=' + encodeURIComponent(nombreArchivo),
        })
        .then(response => response.json()) // Esperar una respuesta JSON
        .then(data => {
            if (data.existe) {
                // Crear un enlace temporal para forzar la descarga
                const link = document.createElement("a");
                link.href = data.ruta; // Ruta del archivo
                link.download = nombreArchivo; // Nombre del archivo
                document.body.appendChild(link); // Agregar el enlace al DOM
                link.click(); // Simular clic en el enlace
                document.body.removeChild(link); // Eliminar el enlace del DOM
            } else {
                alert("El archivo no existe.");
            }
        })
        .catch(error => {
            console.error("Error al verificar el archivo:", error);
            alert("Error al intentar descargar el archivo.");
        });
    } else {
        console.log("Descarga cancelada.");
    }
}

// Función para eliminar una carpeta
function eliminarCarpeta(id) {
    if (confirm('¿Estás seguro de que deseas eliminar esta carpeta?')) {
        fetch('eliminar_carpeta.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + encodeURIComponent(id),
        })
        .then(response => response.text())
        .then(data => {
            alert(data); // Mostrar la respuesta del servidor
            location.reload(); // Recargar la página para actualizar la lista de carpetas
        })
        .catch(error => {
            console.error('Error al eliminar la carpeta:', error);
            alert('Error al eliminar la carpeta.');
        });
    }
}

// Función para editar una carpeta
function editarCarpeta(id) {
    const nuevoNombre = prompt('Introduce el nuevo nombre de la carpeta:');
    if (nuevoNombre) {
        fetch('editar_carpeta.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + encodeURIComponent(id) + '&nombre_carpeta=' + encodeURIComponent(nuevoNombre),
        })
        .then(response => response.text())
        .then(data => {
            alert(data); // Mostrar la respuesta del servidor
            location.reload(); // Recargar la página para actualizar la lista de carpetas
        })
        .catch(error => {
            console.error('Error al editar la carpeta:', error);
            alert('Error al editar la carpeta.');
        });
    }
}



// Función para cargar las carpetas desde la base de datos
function cargarCarpetas() {
    fetch('obtener_carpetas.php') // Endpoint para obtener las carpetas
        .then(response => response.json())
        .then(data => {
            const folderSelect = document.getElementById('folder-select');

            // Limpiar opciones existentes (excepto las primeras dos)
            while (folderSelect.options.length > 2) {
                folderSelect.remove(2);
            }

            // Agregar las carpetas obtenidas al <select>
            data.forEach(carpeta => {
                const option = document.createElement('option');
                option.value = carpeta.id; // Usar el ID de la carpeta como valor
                option.textContent = carpeta.nombre_carpeta; // Mostrar el nombre de la carpeta
                folderSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error al cargar las carpetas:', error);
        });
}

// Resto del código JavaScript (sidebar, modales, etc.)
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const menuBtn = document.getElementById('menu-btn');
    const closeBtn = document.getElementById('close-btn');
    const userBtn = document.getElementById('user-btn');
    const userDropdown = document.getElementById('user-dropdown');
    const themeBtn = document.getElementById('theme-btn');
    const statusBtn = document.getElementById('status-btn');
    const body = document.body;
    const uploadBtn = document.getElementById('upload-btn');
    const uploadModal = document.getElementById('upload-modal');
    const closeModal = document.getElementById('close-modal');
    const uploadForm = document.getElementById('upload-form'); // Definir uploadForm aquí
    const viewToggleBtn = document.getElementById('view-toggle-btn');
    const filesGrid = document.getElementById('files-grid');
    const folderSelect = document.getElementById('folder-select');
    const newFolderInput = document.getElementById('new-folder-input');
    const errorMessage = document.getElementById('error-message');

    // Variables para la gestión de usuarios
    const userTable = document.querySelector('.user-table tbody');

    // Verificar el modo guardado en localStorage
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        body.classList.add('dark-mode');
        themeBtn.innerHTML = '<i class="fas fa-sun"></i>';
    } else {
        body.classList.remove('dark-mode');
        themeBtn.innerHTML = '<i class="fas fa-moon"></i>';
    }

    // Abrir menú
    if (menuBtn) {
        menuBtn.addEventListener('click', function () {
            sidebar.style.left = '0px';
        });
    }

    // Cerrar menú
    if (closeBtn) {
        closeBtn.addEventListener('click', function () {
            sidebar.style.left = '-250px';
        });
    }

    // Mostrar/ocultar dropdown del usuario
    if (userBtn) {
        userBtn.addEventListener('click', function (event) {
            event.stopPropagation();
            userDropdown.style.display = userDropdown.style.display === 'block' ? 'none' : 'block';
        });
    }

    // Cambiar entre modo oscuro y claro
    if (themeBtn) {
        themeBtn.addEventListener('click', function () {
            body.classList.toggle('dark-mode');
            if (body.classList.contains('dark-mode')) {
                localStorage.setItem('theme', 'dark');
                themeBtn.innerHTML = '<i class="fas fa-sun"></i>';
            } else {
                localStorage.setItem('theme', 'light');
                themeBtn.innerHTML = '<i class="fas fa-moon"></i>';
            }
        });
    }

    // Cerrar dropdown del usuario al hacer clic fuera
    document.addEventListener('click', function () {
        if (userDropdown) {
            userDropdown.style.display = 'none';
        }
    });

    // Abrir modal de carga de archivos
    if (uploadBtn && uploadModal) {
        uploadBtn.addEventListener('click', function () {
            // Cargar las carpetas solo cuando se abra el modal
            cargarCarpetas();
            uploadModal.style.display = 'flex';
        });
    }

    // Cerrar modal de carga de archivos
    if (closeModal) {
        closeModal.addEventListener('click', function () {
            uploadModal.style.display = 'none';
        });
    }

    // Cerrar modal al hacer clic fuera del contenido
    window.addEventListener('click', function (event) {
        if (event.target === uploadModal) {
            uploadModal.style.display = 'none';
        }
    });

    // Mostrar u ocultar el input para el nombre de la carpeta
    if (folderSelect) {
        folderSelect.addEventListener('change', function () {
            if (folderSelect.value === 'new') {
                newFolderInput.style.display = 'block'; // Mostrar el input
            } else {
                newFolderInput.style.display = 'none'; // Ocultar el input
            }
        });
    }

    // Manejar el envío del formulario de carga de archivos
    if (uploadForm) {
        uploadForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const folderSelect = document.getElementById('folder-select');
            const folderNameInput = document.getElementById('folder-name');
            const fileInput = document.getElementById('file-input');
            const files = fileInput.files;

            // Limpiar mensajes de error anteriores
            errorMessage.style.display = 'none';
            errorMessage.textContent = '';
            errorMessage.classList.remove('error', 'success');

            // Validar si se seleccionó "Crear nueva carpeta" y no se ingresó un nombre
            if (folderSelect.value === 'new' && !folderNameInput.value.trim()) {
                errorMessage.textContent = 'Por favor, ingrese un nombre para la nueva carpeta.';
                errorMessage.classList.add('error');
                errorMessage.style.display = 'block';
                return;
            }

            // Validar si no se seleccionó ningún archivo
            if (files.length === 0) {
                errorMessage.textContent = 'Por favor, seleccione al menos un archivo.';
                errorMessage.classList.add('error');
                errorMessage.style.display = 'block';
                return;
            }

            // Validar el tamaño de los archivos (ejemplo: máximo 10 MB por archivo)
            const maxFileSize = 10 * 1024 * 1024; // 10 MB
            for (let i = 0; i < files.length; i++) {
                if (files[i].size > maxFileSize) {
                    errorMessage.textContent = `El archivo ${files[i].name} excede el tamaño máximo permitido (10 MB).`;
                    errorMessage.classList.add('error');
                    errorMessage.style.display = 'block';
                    return;
                }
            }

            // Crear un FormData para enviar los archivos y datos del formulario
            const formData = new FormData();
            formData.append('folder-select', folderSelect.value);
            formData.append('folder-name', folderNameInput.value.trim());

            // Agregar cada archivo al FormData
            for (let i = 0; i < files.length; i++) {
                formData.append('archivo[]', files[i]);
            }

            // Enviar los datos al servidor usando fetch
            fetch('subir_archivo.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.text())
            .then(data => {
                // Mostrar el mensaje de éxito o error
                errorMessage.textContent = data;
                errorMessage.classList.add('success');
                errorMessage.style.display = 'block';

                // Recargar la página para actualizar la lista de archivos
                setTimeout(() => {
                    location.reload();
                }, 2000);
            })
            .catch(error => {
                console.error('Error al subir el archivo:', error);
                errorMessage.textContent = 'Error al subir el archivo.';
                errorMessage.classList.add('error');
                errorMessage.style.display = 'block';
            });
        });
    }

    // Cambiar entre vista de archivos y carpetas
    if (viewToggleBtn) {
        viewToggleBtn.addEventListener('click', function () {
            const isFolderView = filesGrid.classList.toggle('folder-view');
            viewToggleBtn.innerHTML = isFolderView ? '<i class="fas fa-file"></i> Ver por Archivos' : '<i class="fas fa-folder"></i> Ver por Carpetas';

            if (isFolderView) {
                // Cargar carpetas dinámicamente desde PHP
                fetch('consultar_carpetas.php') // Ruta al archivo PHP que genera el HTML
                    .then(response => response.text()) // Obtener el contenido como texto
                    .then(data => {
                        filesGrid.innerHTML = data; // Insertar el HTML en el contenedor
                        setupFileButtons(); // Configurar los botones de carpetas
                    })
                    .catch(error => {
                        console.error('Error al cargar las carpetas:', error);
                    });
            } else {
                // Cargar archivos dinámicamente desde PHP
                fetch('consultar_archivos.php') // Ruta al archivo PHP que genera el HTML
                    .then(response => response.text()) // Obtener el contenido como texto
                    .then(data => {
                        filesGrid.innerHTML = data; // Insertar el HTML en el contenedor
                        setupFileButtons(); // Configurar los botones de archivos
                    })
                    .catch(error => {
                        console.error('Error al cargar los archivos:', error);
                    });
            }

            // Configurar los botones de archivos después de cambiar la vista
            setupFileButtons();
        });
    }
});