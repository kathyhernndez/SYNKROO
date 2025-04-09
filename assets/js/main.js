// ==============================================
// SISTEMA DE VERIFICACIÓN DE SESIÓN
// ==============================================

// Verificación de sesión
function checkSession() {
    fetch('check_session.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'warning') {
                // Mostrar modal de advertencia
                const minutes = Math.floor(data.remaining_time / 60);
                const seconds = data.remaining_time % 60;
                
                if (!document.getElementById('session-warning-modal')) {
                    const modalHTML = `
                        <div id="session-warning-modal" class="session-modal">
                            <div class="session-modal-content">
                                <h3>Tu sesión está por expirar</h3>
                                <p>Tu sesión expirará en ${minutes}:${seconds.toString().padStart(2, '0')} minutos debido a inactividad.</p>
                                <div class="session-modal-buttons">
                                    <button id="extend-session-btn" class="btn-confirm">Extender sesión</button>
                                    <button id="logout-now-btn" class="btn-cancel">Cerrar sesión ahora</button>
                                </div>
                            </div>
                        </div>
                    `;
                    document.body.insertAdjacentHTML('beforeend', modalHTML);
                    
                    document.getElementById('extend-session-btn').addEventListener('click', () => {
                        extendSession();
                        document.getElementById('session-warning-modal').remove();
                    });
                    
                    document.getElementById('logout-now-btn').addEventListener('click', () => {
                        window.location.href = '../php/logout.php';
                    });
                }
            } else if (data.status === 'expired') {
                // Redirigir a logout
                window.location.href = '../php/logout.php';
            }
        });
}

// Extender sesión
function extendSession() {
    fetch('extend_session.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Sesión extendida');
            }
        });
}

// Verificar sesión cada minuto
setInterval(checkSession, 60000); // 60 segundos

// ==============================================
// SISTEMA DE CONFIRMACIÓN DE ACCIONES
// ==============================================

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

// Función para configurar los botones de archivos/carpetas
function setupFileButtons() {
    // Setup event listeners for file/folder actions
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.closest('.file-item, .folder-item').dataset.id;
            const name = this.closest('.file-item, .folder-item').querySelector('.file-name, .folder-name').textContent;
            editarArchivo(id, name);
        });
    });

    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.closest('.file-item, .folder-item').dataset.id;
            eliminarArchivo(id);
        });
    });

    document.querySelectorAll('.download-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const name = this.closest('.file-item').querySelector('.file-name').textContent;
            descargarArchivo(name);
        });
    });

    document.querySelectorAll('.folder-name').forEach(btn => {
        btn.addEventListener('click', function() {
            const folderItem = this.closest('.folder-item');
            if (folderItem) {
                const folderId = folderItem.dataset.id;
                abrirCarpeta(folderId);
            }
        });
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
            const errorMessage = document.getElementById('error-message');
    
            // Limpiar mensajes anteriores
            errorMessage.style.display = 'none';
            errorMessage.textContent = '';
            errorMessage.className = 'error-message'; // Resetear clases
    
            // Validación básica del frontend
            if (folderSelect.value === '#' || folderSelect.value === '') {
                errorMessage.textContent = 'No has seleccionado o creado una carpeta. Por favor, elige una carpeta existente o crea una nueva.';
                errorMessage.classList.add('warning');
                errorMessage.style.display = 'block';
                return;
            }
    
            if (folderSelect.value === 'new' && !folderNameInput.value.trim()) {
                errorMessage.textContent = 'Por favor, ingrese un nombre para la nueva carpeta.';
                errorMessage.classList.add('warning');
                errorMessage.style.display = 'block';
                return;
            }
    
            if (files.length === 0) {
                errorMessage.textContent = 'Por favor, seleccione al menos un archivo.';
                errorMessage.classList.add('warning');
                errorMessage.style.display = 'block';
                return;
            }
    
            const maxFileSize = 10 * 1024 * 1024;
            for (let i = 0; i < files.length; i++) {
                if (files[i].size > maxFileSize) {
                    errorMessage.textContent = `El archivo ${files[i].name} excede el tamaño máximo permitido (10 MB).`;
                    errorMessage.classList.add('warning');
                    errorMessage.style.display = 'block';
                    return;
                }
            }
    
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
            .then(response => response.json())
            .then(data => {
                // Mostrar mensaje con el tipo adecuado
                errorMessage.textContent = data.message;
                
                if (data.status === 'success') {
                    errorMessage.classList.add('success');
                } else if (data.status === 'warning') {
                    errorMessage.classList.add('warning');
                } else {
                    errorMessage.classList.add('error');
                }
                
                errorMessage.style.display = 'block';
    
                if (data.status === 'success') {
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                }
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
                    })
                    .catch(error => {
                        console.error('Error al cargar las carpetas:', error);
                    });
            } else {
                fetch('consultar_archivos.php')
                    .then(response => response.text())
                    .then(data => {
                        filesGrid.innerHTML = data;
                    })
                    .catch(error => {
                        console.error('Error al cargar los archivos:', error);
                    });
            }
        });
    }

    // Configurar los botones de archivos/carpetas al cargar la página
    setupFileButtons();
});