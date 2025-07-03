<?php
include_once 'Encabezado.php';
include_once 'menu.php';
include_once 'conexion.php';
?>
<main class="contenido-principal container">

    <section class="row">
    <section class="col">
    </section>
    <section class="col-10">
            <h1 class="hInicio">Eventos</h1>
            <h3 class="hInicio">Próximos eventos:</h3>
            <h3 class="hInicio"><a href="https://congresovetmadrid.com/">Congreso VETMADRID 2025</a></h3>
            <section style="width: 100%; height: 400px; margin-bottom: 100px">
            <!-- Iframe para ubicación de Google Maps -->
            <iframe src="https://calendar.google.com/calendar/embed?src=luciamarinmartinez94%40gmail.com&ctz=Europe%2FMadrid" 
                style="border: 0" 
                width="100%" 
                height="100%" 
                frameborder="0" 
                scrolling="no"></iframe>
            </section>

    </section>
    <section class="col">
   </section>
   </section>

</main>  
<?php
include_once 'footer.php';
?>