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

// Resto del código JavaScript
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
    const uploadForm = document.getElementById('upload-form');
    const viewToggleBtn = document.getElementById('view-toggle-btn');
    const filesGrid = document.getElementById('files-grid');
    const folderSelect = document.getElementById('folder-select');
    const newFolderInput = document.getElementById('new-folder-input');
    const errorMessage = document.getElementById('error-message');

    // Variables para la gestión de usuarios
    const createUserBtn = document.getElementById('create-user-btn');
    const userModal = document.getElementById('user-modal');
    const closeUserModal = document.getElementById('close-user-modal');
    const userForm = document.getElementById('user-form');
    const userErrorMsg = document.getElementById('user-error-message');
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
    if (uploadBtn) {
        uploadBtn.addEventListener('click', function () {
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

            // Si todo está bien, mostrar un mensaje de éxito
            errorMessage.textContent = 'Archivo(s) cargado(s) correctamente.';
            errorMessage.classList.add('success');
            errorMessage.style.display = 'block';

            // Aquí puedes agregar la lógica para subir los archivos y crear la carpeta
            const folderName = folderSelect.value === 'new' ? folderNameInput.value : folderSelect.value;
            alert(`Cargando ${files.length} archivo(s) en la carpeta: ${folderName}`);
        });
    }

    // Cambiar entre vista de archivos y carpetas
    if (viewToggleBtn) {
        viewToggleBtn.addEventListener('click', function () {
            const isFolderView = filesGrid.classList.toggle('folder-view');
            viewToggleBtn.innerHTML = isFolderView ? '<i class="fas fa-file"></i> Ver por Archivos' : '<i class="fas fa-folder"></i> Ver por Carpetas';

            if (isFolderView) {
                // Mostrar carpetas
                filesGrid.innerHTML = `
                    <div class="file-item">
                        <div class="file-header">
                            <i class="fas fa-folder file-icon"></i>
                            <h4 class="file-name">Carpeta 1</h4>
                        </div>
                        <p class="file-date">01/10/2023</p>
                        <div class="file-actions">
                            <button class="download-btn"><i class="fas fa-download"></i></button>
                            <button class="edit-btn"><i class="fas fa-edit"></i></button>
                            <button class="delete-btn"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                    <!-- Más carpetas aquí -->
                `;
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

    // Gestión de usuarios
    // Abrir modal para crear usuario
    if (createUserBtn) {
        createUserBtn.addEventListener('click', function () {
            userModal.style.display = 'flex';
            document.getElementById('modal-title').textContent = 'Crear Usuario';
            userForm.reset();
            userErrorMsg.style.display = 'none';
        });
    }

    // Cerrar modal de usuario
    if (closeUserModal) {
        closeUserModal.addEventListener('click', function () {
            userModal.style.display = 'none';
        });
    }

    // Cerrar modal al hacer clic fuera del contenido
    window.addEventListener('click', function (event) {
        if (event.target === userModal) {
            userModal.style.display = 'none';
        }
    });
});

// Función para actualizar los datos del usuario en el menú desplegable
function actualizarDatosUsuario() {
    console.log('actualizarDatosUsuario ejecutado');
    const userNameElement = document.getElementById('user-name');
    const userEmailElement = document.getElementById('user-email');
    const userRolElement = document.getElementById('user-rol');

    if (usuario && usuario.nombre && usuario.email && usuario.rol) {
        console.log('Datos del usuario encontrados:', usuario);
        userNameElement.textContent = `Usuario: ${usuario.nombre}`;
        userEmailElement.textContent = `Correo: ${usuario.email}`;
        userRolElement.textContent = `Rol: ${usuario.rol}`;
    } else {
        console.log('Datos del usuario no disponibles');
        userNameElement.textContent = 'Usuario: No disponible';
        userEmailElement.textContent = 'Correo: No disponible';
        userRolElement.textContent = 'Rol: No disponible';
    }
}

// Llamar a la función cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function () {
    applySavedTheme(); // Aplicar el tema guardado
    initEvents(); // Inicializar eventos
    actualizarDatosUsuario(); // Actualizar datos del usuario
});