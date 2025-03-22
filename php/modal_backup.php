<!-- Modal para el respaldo -->
<div class="modal" id="backup-modal">
    <div class="modal-content">
        <span class="close-modal" id="close-backup-modal">&times;</span>
        <h2>Seleccionar Tipo de Respaldo</h2>
        <form id="backup-form">
            <div class="form-group">
                <p>¿Deseas vaciar la carpeta de archivos subidos antes de hacer el backup?</p>
                <div class="form-check">
                    <input class="form-check-input" type="radio" id="si" name="vaciar_uploads" value="1" required>
                    <label class="form-check-label" for="si">Sí</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" id="no" name="vaciar_uploads" value="0" required>
                    <label class="form-check-label" for="no">No</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Hacer Backup</button>
        </form>
    </div>
</div>