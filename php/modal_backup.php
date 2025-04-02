
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<!-- Modal para confirmar backup -->
<div id="backupModal" class="modal-backup" style="display: none;">
  <div class="modal-content-backup">
    <div class="modal-header-backup">
      <h3>Confirmar Respaldo</h3>
      <span class="close-modal-backup">&times;</span>
    </div>
    <div class="modal-body-backup">
      <p>¿Estás seguro de que deseas realizar un respaldo de la base de datos?</p>
    </div>
    <div class="modal-footer-backup">
      <button id="cancelBackupBtn" class="btn-modal-backup btn-cancel-backup">Cancelar</button>
      <button id="confirmBackupBtn" class="btn-modal-backup btn-confirm-backup">Confirmar</button>
    </div>
  </div>
</div>

<script>
// Asegurarse de que la función esté disponible globalmente
window.confirmBackup = function() {
    console.log('Función confirmBackup ejecutada');
    
    const modal = document.getElementById('backupModal');
    if (!modal) {
        console.error('Modal no encontrado en el DOM');
        return;
    }
    
    // Mostrar modal
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    // Configurar eventos de cierre
    const closeModal = function() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    };
    
    // Cerrar al hacer clic en la X
    document.querySelector('.close-modal-backup').onclick = closeModal;
    
    // Cerrar al hacer clic en Cancelar
    document.getElementById('cancelBackupBtn').onclick = closeModal;
    
    // Confirmar backup
    document.getElementById('confirmBackupBtn').onclick = function() {
        closeModal();
        console.log('Iniciando proceso de backup...');
        
        // Mostrar loader
        const loading = document.createElement('div');
        loading.innerHTML = '<div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:2000; display:flex; justify-content:center; align-items:center; color:white; font-size:1.5rem;"><p>Generando respaldo, por favor espere...</p></div>';
        document.body.appendChild(loading);
        
        // Redirigir a backup.php
        setTimeout(() => {
            window.location.href = 'backup.php';
        }, 500);
    };
    
    // Cerrar al hacer clic fuera del modal
    window.onclick = function(event) {
        if (event.target === modal) {
            closeModal();
        }
    };
};
</script>