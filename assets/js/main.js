// Sistema de confirmación de acciones
let currentAction = null;
let currentActionParams = null;

// Mostrar modal de confirmación
function showConfirmation(title, message, action, params = {}) {
    const modal = document.getElementById('confirmationModal');
    const titleElement = document.getElementById('confirmationTitle');
    const messageElement = document.getElementById('confirmationMessage');
    
    currentAction = action;
    currentActionParams = params;
    
    titleElement.textContent = title;
    messageElement.textContent = message;
    modal.style.display = 'flex';
}

// Configurar eventos del modal de confirmación
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('confirmationModal');
    const closeBtn = document.querySelector('.close-confirmation');
    const cancelBtn = document.getElementById('cancelAction');
    const confirmBtn = document.getElementById('confirmAction');
    
    // Cerrar modal al hacer clic en la X o en Cancelar
    [closeBtn, cancelBtn].forEach(btn => {
        btn.addEventListener('click', function() {
            modal.style.display = 'none';
            currentAction = null;
            currentActionParams = null;
        });
    });
    
    // Confirmar acción
    confirmBtn.addEventListener('click', function() {
        if (currentAction) {
            currentAction(currentActionParams);
        }
        modal.style.display = 'none';
        currentAction = null;
        currentActionParams = null;
    });
    
    // Cerrar modal al hacer clic fuera del contenido
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            currentAction = null;
            currentActionParams = null;
        }
    });
});

// ==============================================
// FUNCIONES MEJORADAS PARA ARCHIVOS
// ==============================================

function showConfirmationModal(type, message, callback, options = {}) {
    const modal = document.createElement('div');
    modal.className = `confirmation-modal ${type}-confirmation`;
    modal.innerHTML = `
        <div class="confirmation-modal-content">
            <div class="confirmation-modal-header">
                <h3>${options.title || 'Confirmación'}</h3>
                <button class="close-confirmation-modal">&times;</button>
            </div>
            <div class="confirmation-modal-body">
                ${message}
                ${options.input ? `<input type="text" class="confirmation-input" placeholder="${options.placeholder || ''}" value="${options.value || ''}">` : ''}
            </div>
            <div class="confirmation-modal-footer">
                <button class="confirmation-btn confirmation-btn-cancel">Cancelar</button>
                <button class="confirmation-btn confirmation-btn-confirm">${options.confirmText || 'Confirmar'}</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    modal.style.display = 'flex';
    
    // Event listeners
    modal.querySelector('.close-confirmation-modal').addEventListener('click', () => {
        modal.remove();
    });
    
    modal.querySelector('.confirmation-btn-cancel').addEventListener('click', () => {
        modal.remove();
    });
    
    modal.querySelector('.confirmation-btn-confirm').addEventListener('click', () => {
        const inputValue = options.input ? modal.querySelector('.confirmation-input').value : null;
        callback(inputValue);
        modal.remove();
    });
    
    // Cerrar al hacer clic fuera del modal
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

function eliminarArchivo(id) {
    showConfirmationModal('delete', '¿Estás seguro de que deseas eliminar este archivo? Esta acción no se puede deshacer.', () => {
        fetch('eliminar_archivo.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + encodeURIComponent(id),
        })
        .then(response => response.text())
        .then(data => {
            showAlert('success', data);
            setTimeout(() => location.reload(), 1500);
        })
        .catch(error => {
            console.error('Error al eliminar el archivo:', error);
            showAlert('error', 'Error al eliminar el archivo.');
        });
    }, {
        title: 'Eliminar Archivo',
        confirmText: 'Eliminar'
    });
}

function editarArchivo(id, currentName = '') {
    showConfirmationModal('edit', 'Introduce el nuevo nombre del archivo:', (newName) => {
        if (newName && newName.trim() !== '') {
            fetch('editar_archivo.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + encodeURIComponent(id) + '&nombre_archivo=' + encodeURIComponent(newName),
            })
            .then(response => response.text())
            .then(data => {
                showAlert('success', data);
                setTimeout(() => location.reload(), 1500);
            })
            .catch(error => {
                console.error('Error al editar el archivo:', error);
                showAlert('error', 'Error al editar el archivo.');
            });
        } else {
            showAlert('error', 'Debes ingresar un nombre válido.');
        }
    }, {
        title: 'Renombrar Archivo',
        input: true,
        placeholder: 'Nuevo nombre del archivo',
        value: currentName,
        confirmText: 'Guardar'
    });
}

function descargarArchivo(nombreArchivo) {
    showConfirmationModal('download', `¿Estás seguro de que deseas descargar el archivo "${nombreArchivo}"?`, () => {
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
                showAlert('success', 'Descarga iniciada');
            } else {
                showAlert('error', "El archivo no existe.");
            }
        })
        .catch(error => {
            console.error("Error al verificar el archivo:", error);
            showAlert('error', "Error al intentar descargar el archivo.");
        });
    }, {
        title: 'Descargar Archivo',
        confirmText: 'Descargar'
    });
}

// ==============================================
// FUNCIONES PARA CARPETAS (actualizadas)
// ==============================================

function eliminarCarpeta(id) {
    showConfirmationModal('delete', '¿Estás seguro de que deseas eliminar esta carpeta y todo su contenido? Esta acción no se puede deshacer.', () => {
        fetch('eliminar_carpeta.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + encodeURIComponent(id),
        })
        .then(response => response.text())
        .then(data => {
            showAlert('success', data);
            setTimeout(() => location.reload(), 1500);
        })
        .catch(error => {
            console.error('Error al eliminar la carpeta:', error);
            showAlert('error', 'Error al eliminar la carpeta.');
        });
    }, {
        title: 'Eliminar Carpeta',
        confirmText: 'Eliminar'
    });
}

function editarCarpeta(id, currentName = '') {
    showConfirmationModal('edit', 'Introduce el nuevo nombre de la carpeta:', (newName) => {
        if (newName && newName.trim() !== '') {
            fetch('editar_carpeta.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + encodeURIComponent(id) + '&nombre_carpeta=' + encodeURIComponent(newName),
            })
            .then(response => response.text())
            .then(data => {
                showAlert('success', data);
                setTimeout(() => location.reload(), 1500);
            })
            .catch(error => {
                console.error('Error al editar la carpeta:', error);
                showAlert('error', 'Error al editar la carpeta.');
            });
        } else {
            showAlert('error', 'Debes ingresar un nombre válido.');
        }
    }, {
        title: 'Renombrar Carpeta',
        input: true,
        placeholder: 'Nuevo nombre de la carpeta',
        value: currentName,
        confirmText: 'Guardar'
    });
}

// Función para mostrar alertas bonitas
function showAlert(type, message) {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = `
        <div class="alert-content">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
        <button class="alert-close">&times;</button>
    `;
    
    document.body.appendChild(alert);
    
    // Cerrar alerta después de 5 segundos
    setTimeout(() => {
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 300);
    }, 5000);
    
    // Cerrar al hacer clic en el botón
    alert.querySelector('.alert-close').addEventListener('click', () => {
        alert.remove();
    });
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