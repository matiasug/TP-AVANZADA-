<?php
session_start();

// vector de personas directamente de la sesión.
// Si no existe, lo inicializamos como un array vacío 
$vPersonas = isset($_SESSION['personas']) ? $_SESSION['personas'] : [];

?>
<!DOCTYPE html>
<html lang="es">
    <?php require_once("./Includes/header.php")?>

<body class="d-flex flex-column min-vh-100"
      style="background: url('imgs/fondo1.jpg') no-repeat center center fixed; background-size: cover;">

<main class="container flex-fill">
    <br>
    <h4 class="text-center">Listado de Personas</h4>
    <div class="d-flex justify-content-center mt-4">
    
        <table class="table table-striped table-bordered shadow-sm" style="max-width: 800px;">
            <thead class="table-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Documento</th>
                    <th scope="col">Nombres</th>
                    <th scope="col">Apellido</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach( $vPersonas as $index => $persona ): ?>
                    <tr>
                        <th scope='row'><?php echo ($index + 1); ?></th>
                        <td><?php echo htmlspecialchars($persona['documento']); ?></td>
                        <td><?php echo htmlspecialchars($persona['nombres']); ?></td>
                        <td><?php echo htmlspecialchars($persona['apellido']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="text-center mt-3">
        <a href="FormPersonas.php" class="btn btn-primary">Agregar otra persona</a>
    </div>
</main>

<?php require_once("./Inlcudes/footer.php")?>
</body>
</html>
