<?php
    session_start();

    // --- GENERACIÓN DE TOKEN CSRF ---
    if (empty($_SESSION['token'])) {
        $_SESSION['token'] = bin2hex(random_bytes(32));
    }
    // Mensaje de registro exitoso
    $registro_msg = $_SESSION['registro_exitoso'] ?? '';
    unset($_SESSION['registro_exitoso']);
    $token = $_SESSION['token'];
?> 

<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<?php require_once("./Includes/header.php"); ?>

<body class="d-flex flex-column min-vh-100"
      style="background: url('imgs/fondo1.jpg') no-repeat center center fixed; background-size: cover;">


  
  <main class="flex-fill d-flex justify-content-center align-items-center">

    <div class="card shadow-lg rounded-4 p-4" style="background: #f6fff9; width: 100%; max-width: 350px; border: 2px solid #856133;">
      <div class="card-body">

<h3 class="fw-bold text-center" style="color: #856133; -webkit-text-stroke: 1.5px #6e5215ff; font-size: 2rem;">
  Iniciar sesión
</h3>

  
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

<?php if ($registro_msg): ?>
    <div class="alert alert-success text-center fw-bold"><?php echo htmlspecialchars($registro_msg); ?></div>
<?php endif; ?>


        <form id="formulario1" action="ProcesarLogin.php" method="POST">

          <input type="hidden" name="token" value="<?php echo $token; ?>">
          <div class="mb-2">
            <label for="correo" class="form-label">Correo</label>
            <input type="email" class="form-control" id="correo" name="correo" placeholder="example@email.com" required>
          </div>
 
          <div class="mb-3">
            <label for="cont" class="form-label">Contraseña</label>
            <div class="password-wrapper">
              <input type="password" id="cont" name="cont" class="form-control" placeholder="password" required>
              <i class="bi bi-eye-slash toggle-password" id="togglePassword"></i>
            </div>
          </div>

          <div class="mb-3">
              <label for="rand_code" class="form-label">Código de seguridad:</label>
              <div class="d-flex align-items-center">
                  <img src="Includes/rdnimg.php?v=<?php echo time(); ?>" alt="CAPTCHA" class="me-2 rounded" style="border: 1px solid #ccc;">
                  <input type="text" class="form-control" name="rand_code" id="rand_code" placeholder="Ingrese el código" required>
                  </div>
          </div>

          <button type="submit" class="btn btn-marron w-100 fw-bold mb-2">Iniciar sesión</button>
        </form>
        <a href="abmPersonas.php" class="btn btn-marron w-100 fw-bold mb-2 mt-2">Crear una cuenta</a>

      </div>
    </div>

  </main>

<script src="SHA512.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#cont');

    togglePassword.addEventListener('click', function () {
        // Cambiar el tipo del input
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        // Cambiar el ícono del ojo
        this.classList.toggle('bi-eye');
        this.classList.toggle('bi-eye-slash');
    });
});
</script>
  
<?php require_once('./Includes/footer.php'); ?>
</body>
</html>