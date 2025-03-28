
    <link rel="stylesheet" href="../assets/css/main.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">


    <!-- Barra de estado -->
    <div class="status-bar">
        <h3>Estado del Sistema</h3>
        <div class="status-progress">
            <div class="progress-bar" style="width: <?php echo $percentage; ?>%;"></div>
        </div>
        <p><?php echo round($percentage, 2); ?>% completado</p>
        <?php if (!empty($message)): ?>
            <p class="alert"><?php echo $message; ?></p>
        <?php endif; ?>
    </div>

