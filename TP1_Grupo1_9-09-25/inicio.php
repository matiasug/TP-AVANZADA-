<?php
session_start();

// Si no existe, lo redirigimos al login.
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

// 2. Si ha iniciado sesión, recuperamos sus datos para mostrarlos.
$nombre_usuario = $_SESSION['usuario'];
$correo_usuario = $_SESSION['DatosPersona']['correo'];
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
    <?php require_once("./Includes/header.php")?>

<body class="d-flex flex-column min-vh-100"
      style="background: url('imgs/fondo1.jpg') no-repeat center center fixed; background-size: cover;">
    <main class="flex-fill d-flex justify-content-center align-items-center">
        <div class="card shadow-lg p-4">
            <div class="card-body">
                <h1 class="card-title text-center">¡Bienvenido!</h1>
                <p class="card-text">Has iniciado sesión correctamente.</p>
                <p>Nombre de usuario: <strong><?php echo htmlspecialchars($nombre_usuario); ?></strong></p>
                <p>Correo electrónico: <strong><?php echo htmlspecialchars($correo_usuario); ?></strong></p>
                
                <div class="d-grid gap-2 mt-4">
                    <a href="abmProductos.php" class="btn btn-primary">Gestionar Libros</a>

                    <?php if (isset($_SESSION['DatosPersona']['rol']) && $_SESSION['DatosPersona']['rol'] == 'admin'): ?>
                        <a href="abmPersonas.php" class="btn btn-info">Administrar Usuarios</a>
                    <?php endif; ?>

                    <a href="logout.php" class="btn btn-danger mt-3">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </main>

    <?php require_once('./Includes/footer.php') ?>
</body>
</html>
