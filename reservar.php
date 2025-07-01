<?php
include_once 'Encabezado.php';
include_once 'menu.php';
include_once 'conexion.php';

?>
<script>
  new tempusDominus.TempusDominus(document.getElementById('datetimepicker1'), {
    display: {
      components: {
        calendar: true,
        date: true,
        month: true,
        year: true,
        decades: true,
        clock: false
      }
    }
  });
</script>

<main class="contenido-principal">
<section class="mx-extra">
        <h1 class="hInicio">Reserva cita</h1>

</main>  

<?php
include_once 'footer.php';
?>