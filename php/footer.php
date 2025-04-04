<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;400;500;600;700&display=swap');

/* Variables y estilos base */
:root {
    --primary-color: rgba(255, 185, 105, 0.925);
    --primary-color-dark: #ff9742e0;
    --text-dark: #0c0a09;
    --text-light: #717171;
    --white: #ffffff;
    --max-width: 1200px;
    --background-light: #f0f0f0;
    --background-dark: #505050;
    --text-light-mode: #505050;
    --text-dark-mode: #fff;
}

/* Footer */
.slim-footer {
    background-color: none;
    color: #ffffff;
    padding: 10px 0;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    border-top: 2px solid var(--primary-color-dark);
    margin-top: 20px;
    margin-left: 80px;
    margin-right: 80px;
    margin-bottom: 8px;
    
}

.footer-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
}

.footer-social {
    display: flex;
    gap: 10px;
    flex-shrink: 0;
    margin-left: 10px;
}

.social-icon {
    color: #bdc3c7;
    font-size: 14px;
    transition: all 0.3s ease;
}

.social-icon:hover {
    color: var(--primary-color);
    transform: translateY(-1px);
}

.footer-info {
    display: flex;
    align-items: center;
    gap: 8px;
    color:rgb(0, 0, 0);
    flex-wrap: wrap;
    justify-content: center;
    flex-grow: 1;
    max-width: 50%;
}

.footer-separator {
    opacity: 0.5;
}

.footer-developers {
    font-size: 12px;
}

.footer-brand {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

.footer-logo {
    height: 20px;
    width: auto;
}

.footer-brand span {
    font-size: 15px;
    font-weight: 500;
}

/* Modo oscuro */


body.dark-mode .footer-brand span {
    color: var(--text-dark);
}
body.dark-mode .social-icon{
    color: var(--text-dark);
}

body.dark-mode .footer-info{
    color: var(--primary-color-dark);
}


/* Responsive - Mobile */
@media (max-width: 768px) {
    .slim-footer{
        margin:0px;
        border-radius: 0px;
        margin-bottom: 0px;
    }

    .footer-content {
        flex-direction: column;
        gap: 8px;
        text-align: center;
    }
    
    .footer-info {
        order: 2;
        max-width: 100%;
        flex-direction: column;
        flex-wrap: wrap;
        gap: 5px;
    }

    #current-datetime {
    white-space: nowrap;
    }
    
    .footer-separator {
        display: none;
    }
    
    .footer-social {
        order: 3;
        margin-top: 5px;
    }
    
    .footer-brand {
        order: 1;
        margin-bottom: 5px;
    }
    
    .footer-logo {
        height: 20px;
    }
}
</style>


<footer class="slim-footer">
    <div class="footer-content">
        
        
        <!-- Logo y nombre  -->
        <div class="footer-brand">
            <img src="../assets/image/synkroo.png" alt="Synkroo Logo" class="footer-logo">
            <img src="../assets/image/UPTAG.png" alt="UPTAG Logo" class="footer-logo">
            <img src="../assets/image/logo.png" alt="Comunicacional Logo" class="footer-logo">
        </div>
        
        
       <!-- Copyright y desarrolladores  -->
<div class="footer-info">
    <span class="footer-copyright">&copy; <span id="current-year">2023</span> UPTAG</span>
    <span class="footer-separator">|</span>
    <span class="footer-developers">Desarrollado por: Hernandez, Pachano, Perez, Bracho</span>
</div>

<div  class="footer-info">
<span id="current-datetime"></span>
</div>

</footer>

<script>
    function formatTwoDigits(num) {
        return num < 10 ? `0${num}` : num;
    }

    function updateDateTime() {
    const now = new Date();
    const days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    
    const day = now.getDate().toString().padStart(2, '0');
    const month = (now.getMonth() + 1).toString().padStart(2, '0');
    const year = now.getFullYear();
    
    let hours = now.getHours();
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // La hora '0' debe ser '12'
    
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const seconds = now.getSeconds().toString().padStart(2, '0');

    document.getElementById('current-datetime').textContent = 
        `${days[now.getDay()]}, ${day}/${month}/${year} - ${hours}:${minutes}:${seconds} ${ampm}`;
}

    updateDateTime();
    setInterval(updateDateTime, 1000);
</script>