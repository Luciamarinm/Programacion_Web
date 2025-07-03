<?php
include_once 'Encabezado.php';
include_once 'menu.php';
include_once 'conexion.php';
?>
<main class="contenido-principal">
<main class="container">
    <section class="row">
    <section class="col">
    </section>
    <section class="col-10">
        <h1 class="hInicio">Bienvenidos a Vetpet</h1>
        <h3 class="hInicio">Nuestro sueño es ayudarte a tí y a tu mascota.</h3>
        </section>
    <section class="col">
    </section>
    </section>
    </main>
    <section id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel" style="margin-top: 100px">
    <article class="carousel-indicators">
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="3" aria-label="Slide 4"></button>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="4" aria-label="Slide 5"></button>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="5" aria-label="Slide 6"></button>
    </article>
    <article class="carousel-inner">
        <aside class="carousel-item active">
            <a href="servicios.php#veterinario">
                <img src="Imagenes/Inicio/veterinario.webp" class="d-block w-100" alt="Veterinario">
            </a>
            <aside class="carousel-caption d-none d-md-block">
                <h2>VETERINARIO</h2>
            </aside>
        </aside>
        <aside class="carousel-item">
            <a href="servicios.php#exoticos">
                <img src="Imagenes/Inicio/exoticos.jpg" class="d-block w-100" alt="Especialización en animales exóticos">
            </a>
            <aside class="carousel-caption d-none d-md-block">
                <h2>ESPECIALIZACIÓN DE ANIMALES EXÓTICOS</h2>
            </aside>
        </aside>
        <aside class="carousel-item">
            <a href="servicios.php#peluqueria">
                <img src="Imagenes/Inicio/peluqueria.webp" class="d-block w-100" alt="Peluquería">
            </a>
            <aside class="carousel-caption d-none d-md-block">
                <h2>PELUQUERÍA</h2>
            </aside>
        </aside>
        <aside class="carousel-item">
            <a href="servicios.php#adiestramiento">
                <img src="Imagenes/Inicio/adiestramiento.jpg" class="d-block w-100" alt="Adiestramiento">
            </a>
                <aside class="carousel-caption d-none d-md-block">
                <h2>ADIESTRAMIENTO</h2>
            </aside>
        </aside>
        <aside class="carousel-item">
            <a href="servicios.php#rehabilitacion">
                <img src="Imagenes/Inicio/rehabilitacion.jpg" class="d-block w-100" alt="Rehabilitación">
            </a>
            <aside class="carousel-caption d-none d-md-block">
                <h2>REHABILITACIÓN</h2>
            </aside>
        </aside>
        <aside class="carousel-item">
            <a href="servicios.php#residencia">
                <img src="Imagenes/Inicio/residencia.jpg" class="d-block w-100" alt="Residencia">
            </a>
            <aside class="carousel-caption d-none d-md-block">
                <h2>RESIDENCIA</h2>
            </aside>
        </aside>

    </article>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
    </section>
</main>  
<?php
include_once 'footer.php';
?>