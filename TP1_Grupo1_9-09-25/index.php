<?php
    // Iniciar la sesión al principio del script
    session_start();

    // --- GENERACIÓN DE TOKEN CSRF ---
    // Generamos un token para proteger el formulario contra ataques CSRF.
    if (empty($_SESSION['token'])) {
        $_SESSION['token'] = bin2hex(random_bytes(32));
    }
    $token = $_SESSION['token'];
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
   <?php require_once("header.php")?>
      <script src="Verificar.js"></script>
      <link href="bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
      <link href="css/estilos.css" rel="stylesheet">

<body class="d-flex flex-column min-vh-100"
      style="background: url('imgs/fondo1.jpg') no-repeat center center fixed; background-size: cover;">

  
  <main class="flex-fill d-flex justify-content-center align-items-center">

    <!-- Login centrado -->
<div class="card shadow-lg rounded-4 p-4" style="background: #f6fff9; width: 100%; max-width: 350px; border: 2px solid #856133;">
      <div class="card-body">

<h3 class="fw-bold text-center" style="color: #856133; -webkit-text-stroke: 1.5px #6e5215ff; font-size: 2rem;">
  Iniciar sesión
</h3>
    <!-- esta parte es la ventana de error que sale al ingresar mal la contraseña, por ahora queda asi -->
<?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
   <div class="alert alert-danger text-center fw-bold" id="errorMsg">
  Acceso inválido. Por favor, inténtelo otra vez.
  </div>

  <script>
   window.history.replaceState({}, document.title, window.location.pathname);
   setTimeout(() => {
  const errorMsg = document.getElementById("errorMsg");
  if (errorMsg) {
    errorMsg.style.display = "none";
  }
}, 4000); // 4 segundos
  </script>
<?php endif; ?>


        <form id="formulario1" action="ProcesarLogin.php" method="POST">
          
          <div class="mb-2">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="name" required>
          </div>

          <div class="mb-2">
            <label for="correo" class="form-label">Correo</label>
            <input type="email" class="form-control" id="correo" name="correo" placeholder="example@email.com" required>
          </div>

          <div class="mb-3">
            <label for="cont" class="form-label">Contraseña</label>
            <input type="password" id="cont" name="cont" class="form-control" placeholder="password" required>
          </div>

          <div class="mb-3">
              <label for="rand_code" class="form-label">Código de seguridad:</label>
              <div class="d-flex align-items-center">
                  <img src="includes/rdnimg.php" alt="CAPTCHA" class="me-2">
                  <input type="text" class="form-control" name="rand_code" id="rand_code" placeholder="Ingrese el código" required>
              </div>
          </div>

          <button type="submit" class="btn btn-marron w-100 fw-bold mb-2">Iniciar sesión</button>

          <!--enlace a la página de registro -->
          <a href="ProcesarUsuario.php" class="btn btn-marron w-100 fw-bold mb-2">Crear una cuenta</a>
        </form>

      </div>
    </div>

  </main>
  
<?php require_once('footer.php') ?> 
