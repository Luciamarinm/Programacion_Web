<?php
include_once 'Encabezado.php';
include_once 'menu.php';
include_once 'conexion.php';

?>

<main class="contenido-principal">
<section class="mx-extra">
        <h1 class="hInicio">Inicia sesión</h1>
        <h3 class="hInicio">Si no tienes una cuenta crea una <a href="crearCuenta.php">aquí</a>.</h3>
    </section>
  <section class="login">

  <form class="row g-3 mx-extra">
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Email:</label>

      <aside class="col-sm-6">
        <input type="email" class="form-control" name="emailRecuperar" value="" placeholder="email@example.com" required>
      </aside>
    </article>

    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Contaseña:</label>

      <aside class="col-sm-6">
        <input type="password" class="form-control" name="passLogin" required>
      </aside>
    </article>

    <article class="col-auto">
      <button type="submit" name="login" class="btn btn-info mb-3">Login</button>

    </article>
  </form>
  </section>
  <section class="mx-extra">

  <p class="hInicio">Si no recuerdas tu contraseña clica <a href="recuperarPass.php">aquí</a>.</p>
    </section>
</main>  

<?php
include_once 'footer.php';
?>