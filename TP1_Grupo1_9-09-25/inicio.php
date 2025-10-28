<?php
session_start();
 
// Si no existe, lo redirigimos al login.
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

// 2. Si ha iniciado sesión, recuperamos sus datos para mostrarlos.
$nombre_usuario = $_SESSION['usuario'] ?? 'Usuario';
$correo_usuario = $_SESSION['DatosPersona']['correo'] ?? 'No disponible';
$es_admin = (isset($_SESSION['DatosPersona']['rol']) && $_SESSION['DatosPersona']['rol'] == 'admin');
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
    <?php require_once("./Includes/header.php")?>

<body class="d-flex flex-column min-vh-100"
      style="background: url('imgs/fondo1.jpg') no-repeat center center fixed; background-size: cover; --bs-primary: #6e5215ff; --bs-secondary: #856133;">
    <main class="flex-fill d-flex justify-content-center align-items-center">
        <div class="card shadow-lg rounded-4 p-4" style="background: #f6fff9; border: 2px solid #856133; width: 100%; max-width: 450px;">
            <div class="card-body">
                <h2 class="fw-bold text-center mb-4" style="color: #856133; -webkit-text-stroke: 1.5px #6e5215ff; font-size: 2rem;">
                    Panel de Control
                </h2>
                <div class="text-center mb-4">
                    <p class="lead mb-1">¡Hola, <strong><?php echo htmlspecialchars($nombre_usuario); ?></strong>!</p>
                    <p class="text-muted"><?php echo htmlspecialchars($correo_usuario); ?></p>
                </div>
                
                <div class="d-grid gap-2 mt-4">
                    <a href="abmProductos.php" class="btn btn-primary fw-bold">Gestionar Libros</a>

                    <?php if ($es_admin): ?>
                        <a href="abmPersonas.php" class="btn btn-info fw-bold">Administrar Usuarios</a>
                    <?php endif; ?>

                    <a href="logout.php" class="btn btn-danger mt-3">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </main>

    <?php require_once('./Includes/footer.php') ?>
</body>
</html>
