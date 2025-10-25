<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
   <?php require_once("./Includes/header.php")
    ?>
        

<br>
<body class="d-flex flex-column min-vh-100"
      style="background: url('imgs/fondo1.jpg') no-repeat center center fixed; background-size: cover;">

    <h4 class="text-center">Sistema Recursos Humanos</h4>
    <div class="container d-flex justify-content-center">

      <form method="POST" action="./Controller/ProcesarPersona.php" class="card p-4 mt-3 shadow" style="max-width:600px;width:100%">

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="documento">Documento</label>
            <input type="text" class="form-control" id="documento" name="documento" value="" required>
          </div>

          <div class="col-md-6 mb-3">
            <label for="nombres">Nombres</label>
            <input type="text" class="form-control" id="nombres" name="nombres" required>
          </div>

          <div class="col-md-6 mb-3">
            <label for="apellido">Apellido</label>
            <input type="text" class="form-control" id="apellido" name="apellido" required>
          </div>

          <div class="col-md-6 mb-3">
            <label for="email">Correo Electrónico</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>

          <div class="col-md-6 mb-3">
            <label for="password">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
        </div>

        <div class="d-grid gap-2">
            <button class="btn btn-primary fw-bold" type="submit">Enviar datos</button>
            <a href="index.php" class="btn btn-outline-secondary fw-bold">Volver al inicio</a>
        </div>

      </form>

    </div>
    <br>
   <?php require_once("./Includes/footer.php")?>
   </body>
</html>
