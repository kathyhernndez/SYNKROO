
// ==============================================
// FUNCIONES PARA ARCHIVOS
// ==============================================

function setupFileButtons() {
    const deleteButtons = document.querySelectorAll('.delete-btn');
    const editButtons = document.querySelectorAll('.edit-btn');
    const downloadButtons = document.querySelectorAll('.download-btn');

    // Limpiar listeners anteriores
    deleteButtons.forEach(button => {
        button.replaceWith(button.cloneNode(true));
    });
    editButtons.forEach(button => {
        button.replaceWith(button.cloneNode(true));
    });
    downloadButtons.forEach(button => {
        button.replaceWith(button.cloneNode(true));
    });

    // Agregar nuevos listeners
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const fileId = this.dataset.fileId;
            eliminarArchivo(fileId);
        });
    });

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const fileId = this.dataset.fileId;
            editarArchivo(fileId);
        });
    });

    downloadButtons.forEach(button => {
        button.addEventListener('click', function() {
            const fileName = this.dataset.fileName;
            descargarArchivo(fileName);
        });
    });
}

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
            alert(data);
            location.reload();
        })
        .catch(error => {
            console.error('Error al eliminar el archivo:', error);
            alert('Error al eliminar el archivo.');
        });
    }
}

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
            alert(data);
            location.reload();
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
        .then(response => response.json())
        .then(data => {
            if (data.existe) {
                const link = document.createElement("a");
                link.href = data.ruta;
                link.download = nombreArchivo;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
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

// ==============================================
// FUNCIONES PARA CARPETAS
// ==============================================

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
            alert(data);
            location.reload();
        })
        .catch(error => {
            console.error('Error al eliminar la carpeta:', error);
            alert('Error al eliminar la carpeta.');
        });
    }
}

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
            alert(data);
            location.reload();
        })
        .catch(error => {
            console.error('Error al editar la carpeta:', error);
            alert('Error al editar la carpeta.');
        });
    }
}

function cargarCarpetas() {
    fetch('obtener_carpetas.php')
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
                option.value = carpeta.id;
                option.textContent = carpeta.nombre_carpeta;
                folderSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error al cargar las carpetas:', error);
        });
}

// ==============================================
// EVENT LISTENERS Y CONFIGURACIÓN INICIAL
// ==============================================

document.addEventListener('DOMContentLoaded', function() {
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
    const userTable = document.querySelector('.user-table tbody');

    // Configurar tema inicial
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        body.classList.add('dark-mode');
        themeBtn.innerHTML = '<i class="fas fa-sun"></i>';
    } else {
        body.classList.remove('dark-mode');
        themeBtn.innerHTML = '<i class="fas fa-moon"></i>';
    }

    // Evento para abrir menú
    if (menuBtn) {
        menuBtn.addEventListener('click', function() {
            sidebar.style.left = '0px';
        });
    }

    // Evento para cerrar menú
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            sidebar.style.left = '-250px';
        });
    }

    // Evento para mostrar/ocultar dropdown del usuario
    if (userBtn) {
        userBtn.addEventListener('click', function(event) {
            event.stopPropagation();
            userDropdown.style.display = userDropdown.style.display === 'block' ? 'none' : 'block';
        });
    }

    // Evento para cambiar tema
    if (themeBtn) {
        themeBtn.addEventListener('click', function() {
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

    // Cerrar dropdown al hacer clic fuera
    document.addEventListener('click', function() {
        if (userDropdown) {
            userDropdown.style.display = 'none';
        }
    });

    // Abrir modal de carga de archivos
    if (uploadBtn && uploadModal) {
        uploadBtn.addEventListener('click', function() {
            cargarCarpetas();
            uploadModal.style.display = 'flex';
        });
    }

    // Cerrar modal de carga de archivos
    if (closeModal) {
        closeModal.addEventListener('click', function() {
            uploadModal.style.display = 'none';
        });
    }

    // Cerrar modal al hacer clic fuera
    window.addEventListener('click', function(event) {
        if (event.target === uploadModal) {
            uploadModal.style.display = 'none';
        }
    });

    // Mostrar u ocultar input para nueva carpeta
    if (folderSelect) {
        folderSelect.addEventListener('change', function() {
            if (folderSelect.value === 'new') {
                newFolderInput.style.display = 'block';
            } else {
                newFolderInput.style.display = 'none';
            }
        });
    }

    // Manejar envío del formulario de carga
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const folderSelect = document.getElementById('folder-select');
            const folderNameInput = document.getElementById('folder-name');
            const fileInput = document.getElementById('file-input');
            const files = fileInput.files;

            // Limpiar mensajes de error anteriores
            errorMessage.style.display = 'none';
            errorMessage.textContent = '';
            errorMessage.classList.remove('error', 'success');

            // Validaciones
            if (folderSelect.value === 'new' && !folderNameInput.value.trim()) {
                errorMessage.textContent = 'Por favor, ingrese un nombre para la nueva carpeta.';
                errorMessage.classList.add('error');
                errorMessage.style.display = 'block';
                return;
            }

            if (files.length === 0) {
                errorMessage.textContent = 'Por favor, seleccione al menos un archivo.';
                errorMessage.classList.add('error');
                errorMessage.style.display = 'block';
                return;
            }

            const maxFileSize = 10 * 1024 * 1024; // 10 MB
            for (let i = 0; i < files.length; i++) {
                if (files[i].size > maxFileSize) {
                    errorMessage.textContent = `El archivo ${files[i].name} excede el tamaño máximo permitido (10 MB).`;
                    errorMessage.classList.add('error');
                    errorMessage.style.display = 'block';
                    return;
                }
            }

            // Crear FormData y enviar
            const formData = new FormData();
            formData.append('folder-select', folderSelect.value);
            formData.append('folder-name', folderNameInput.value.trim());

            for (let i = 0; i < files.length; i++) {
                formData.append('archivo[]', files[i]);
            }

            fetch('subir_archivo.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.text())
            .then(data => {
                errorMessage.textContent = data;
                errorMessage.classList.add('success');
                errorMessage.style.display = 'block';

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
        viewToggleBtn.addEventListener('click', function() {
            const isFolderView = filesGrid.classList.toggle('folder-view');
            viewToggleBtn.innerHTML = isFolderView ? '<i class="fas fa-file"></i> Ver por Archivos' : '<i class="fas fa-folder"></i> Ver por Carpetas';

            if (isFolderView) {
                fetch('consultar_carpetas.php')
                    .then(response => response.text())
                    .then(data => {
                        filesGrid.innerHTML = data;
                        setupFileButtons();
                    })
                    .catch(error => {
                        console.error('Error al cargar las carpetas:', error);
                    });
            } else {
                fetch('consultar_archivos.php')
                    .then(response => response.text())
                    .then(data => {
                        filesGrid.innerHTML = data;
                        setupFileButtons();
                    })
                    .catch(error => {
                        console.error('Error al cargar los archivos:', error);
                    });
            }

            setupFileButtons();
        });
    }

    // Configurar botones iniciales
    setupFileButtons();
});