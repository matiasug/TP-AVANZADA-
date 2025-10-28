<?php
    require_once __DIR__ . '/Model/ConexionBD.php';
    
    // Iniciar sesión si no está iniciada
    if (!session_id()) session_start();

    // --- INICIALIZACIÓN DE VARIABLES ---
    $mensaje = '';

    $titulo = $_POST['titulo'] ?? '';
    $autor = $_POST['autor'] ?? '';
    $editorial = $_POST['editorial'] ?? '';

?>

<?php if (file_exists(__DIR__ . '/Includes/header.php')) include __DIR__ . '/Includes/header.php'; ?>
<body class="d-flex flex-column min-vh-100"
      style="background: url('imgs/fondo1.jpg') no-repeat center center fixed; background-size: cover;">

<main class="flex-fill container py-4">
    <div class="card shadow-lg rounded-4 p-4 mx-auto" style="background: #f6fff9; border: 2px solid #856133; max-width: 600px;">
        <div class="card-body">
            <h2 class="fw-bold text-center mb-4" style="color: #856133; -webkit-text-stroke: 1.5px #6e5215ff; font-size: 2rem;">
                Añadir un Nuevo Libro
            </h2>

            <?php if (!empty($mensaje)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
            <?php endif; ?>
            
            <!-- El action del formulario ahora apunta a abmProductos.php -->
            <form id="productosData" name="productosData" method="POST" action="abmProductos.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="save">
                <div class="mb-3">
                    <label for="titulo" class="form-label">Título:</label>
                    <input type="text" id="titulo" name="titulo" class="form-control" maxlength="200" value="<?php echo htmlspecialchars($titulo); ?>" required />
                </div>
                <div class="mb-3">
                    <label for="autor" class="form-label">Autor:</label>
                    <input type="text" id="autor" name="autor" class="form-control" maxlength="200" value="<?php echo htmlspecialchars($autor); ?>" required />
                </div>
                <div class="mb-3">
                    <label for="editorial" class="form-label">Editorial:</label>
                    <input type="text" id="editorial" name="editorial" class="form-control" maxlength="200" value="<?php echo htmlspecialchars($editorial); ?>" required />
                </div>
                <div class="mb-3">
                    <label for="archivo_libro" class="form-label">Archivo del Libro (PDF/TXT, máx. 5MB)</label>
                    <input type="file" name="archivo_libro" id="archivo_libro" class="form-control" accept=".pdf,.txt">
                </div>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="abmProductos.php" class="btn btn-secondary">Cancelar y Volver</a>
                    <button type="submit" class="btn btn-primary">Guardar Libro</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php if (file_exists(__DIR__ . '/Includes/footer.php')) include __DIR__ . '/Includes/footer.php'; ?>