<?php
include_once 'Encabezado.php';
include_once 'conexion.php';
?>
<body>

    <header class="fondo">
        <nav class="menu contenedor" role="navigation">
            <a href="index.php"><canvas id="canvasLogo" class="logo" width="150" height="50" aria-label="Logo Vetpet"></canvas></a>
            <input type="checkbox" id="menu" />
            <label for="menu">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="none" viewBox="0 0 24 24" stroke="white">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </label>
            <ul class="navBarra">
                <li><a href="index.php" class="text-decoration-none">Inicio</a></li>
                <li><a href="servicios.php" class="text-decoration-none">Servicios</a></li>
                <li><a href="reservar.php" class="text-decoration-none">Reservar</a></li>
                <li><a href="sobreNosotros.php" class="text-decoration-none">Sobre nosotros</a></li>
                <li><a href="contacto.php" class="text-decoration-none">Contacto</a></li>
                <li><a href="blog.php" class="text-decoration-none">Blog</a></li>
                <?php
                if (isset($_SESSION['rol'])) {
                    if ($_SESSION['rol'] === "Cliente") {
                        echo '<li><a href="miPerfil.php" class="text-decoration-none">Mi perfil</a></li>';
                    } elseif ($_SESSION['rol'] === "Admin") {
                        echo '<li><a href="gestion.php" class="text-decoration-none">Gestión</a></li>';
                    } else {
                        echo '<li><a href="login.php" class="text-decoration-none">Iniciar sesión</a></li>';
                    }
                } else {
                    echo '<li><a href="login.php" class="text-decoration-none">Iniciar sesión</a></li>';
                }
                
                ?>
            <!--   <li><a href="#"><svg width="16" height="16" fill="white" viewBox="0 0 16 16">
                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85.708-.708-3.85-3.85zm-5.242.656a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11z"/>
                </svg></a></li>-->
            </ul>
        </nav>  
    </header>