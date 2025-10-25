<?php

session_start();

require_once 'Model/ConexionBD.php';

// Verifica si el usuario NO está logueado O si su rol NO es 'admin'
if (!isset($_SESSION['DatosPersona']) || $_SESSION['DatosPersona']['rol'] != 'admin') {
    // Si no es administrador, lo redirigimos fuera
    header("Location: inicio.php"); 
    exit;
}

$oPersona = new Persona();
// Obtenemos todas las personas de la BD
$personas = $oPersona->getall(); 
?>

<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
   <?php require_once("./Includes/header.php")?>
      <link href="bootstrap/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
      <link href="css/estilos.css" rel="stylesheet">
    

<body class="d-flex flex-column min-vh-100"
      style="background: url('imgs/fondo1.jpg') no-repeat center center fixed; background-size: cover;">

  
  <main class="flex-fill d-flex justify-content-center align-items-center p-3">

    <div class="card shadow-lg rounded-4 p-4" style="background: #f6fff9; width: 100%; max-width: 1000px; border: 2px solid #856133;">
      <div class="card-body">

        <h3 class="fw-bold text-center mb-4" style="color: #856133; -webkit-text-stroke: 1.5px #6e5215ff; font-size: 2rem;">
          Gestión de Usuarios (ABM)
        </h3>

        <a href="FormPersona.php" class="btn btn-primary fw-bold mb-3">Agregar Nueva Persona</a>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead class="bg-dark text-white">
                    <tr>
                        <th>ID</th>
                        <th>Documento</th>
                        <th>Apellido</th>
                        <th>Nombres</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($personas) {
                        foreach ($personas as $persona) {
                            // Usamos el color de fila para distinguir al administrador
                            $clase_rol = ($persona['rol'] =='admin') ? 'table-info fw-bold' : '';
                            echo "<tr class='$clase_rol'>";
                            echo "<td>" . htmlspecialchars($persona['idPersona']) . "</td>";
                            echo "<td>" . htmlspecialchars($persona['documento']) . "</td>";
                            echo "<td>" . htmlspecialchars($persona['apellido']) . "</td>";
                            echo "<td>" . htmlspecialchars($persona['nombres']) . "</td>";
                            echo "<td>" . htmlspecialchars($persona['rol']) . "</td>";
                            
                            // Botones de Modificación y Eliminación
                            echo "<td>";
                            
                            // Botón de MODIFICACIÓN (Edición)
                            echo "<a href='modificar_persona.php?id=" . $persona['idPersona'] . "' class='btn btn-sm btn-warning me-2'>Modificar</a>";
                            
                            // Botón de ELIMINACIÓN (Baja)
                            echo "<a href='#' onclick='confirmarBaja(" . $persona['idPersona'] . ");' class='btn btn-sm btn-danger'>Eliminar</a>";
                            
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center'>No se encontraron personas registradas.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div> <a href="inicio.php" class="btn btn-outline-secondary fw-bold mt-3">Volver al Dashboard</a>

      </div>
    </div>

  </main>
  
    <?php require_once('./Includes/footer.php') ?>

    <script>
    function confirmarBaja(id) {
        // Pide confirmación del usuario antes de ejecutar la acción GEMINI 
        if (confirm("¿Estás seguro de que deseas eliminar a la persona con ID: " + id + "? Esta acción es irreversible y eliminará los datos de login asociados.")) {
            // Si confirma, redirige al script que hará la baja en la BD
            window.location.href = 'eliminar_persona.php?id=' + id;
        }
    }
    </script>
</body>
</html>