<?php
if (!session_id()) session_start();

// --- CONTROL DE ACCESO ---
// Si no hay un usuario logueado, se redirige al login.
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

require_once 'Model/ConexionBD.php';

// Variable para verificar si el usuario es administrador.
$es_admin = (isset($_SESSION['DatosPersona']['rol']) && $_SESSION['DatosPersona']['rol'] == 'admin');
$msg = '';
$error = '';

// --- GENERACIÓN DE TOKEN CSRF ---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['csrf_token'];

// --- MANEJO DE ACCIONES POST (Crear, Actualizar, Eliminar) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- VALIDACIÓN DE TOKEN CSRF ---
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Error de validación CSRF.');
    }

    $action = $_POST['action'] ?? '';

    // Datos comunes
    $titulo = trim($_POST['titulo'] ?? '');
    $autor = trim($_POST['autor'] ?? '');
    $editorial = trim($_POST['editorial'] ?? '');

    try {
        if ($action === 'save' || $action === 'update') {
            if (empty($titulo) || empty($autor) || empty($editorial)) {
                throw new Exception('Completar todos los campos (Título, Autor, Editorial).');
            }

            $libro = new Libro();
            $libro->setTitulo($titulo);
            $libro->setAutor($autor);
            $libro->setEditorial($editorial);

            // --- LÓGICA PARA MANEJAR EL ARCHIVO (PDF/TXT) ---
            $filePath = null;
            if (isset($_FILES['archivo_libro']) && $_FILES['archivo_libro']['error'] == 0) {
                $target_dir = __DIR__ . "/uploads/"; // Usamos una ruta absoluta
                // Crear un nombre de archivo único para evitar sobreescrituras
                $fileType = strtolower(pathinfo($_FILES["archivo_libro"]["name"], PATHINFO_EXTENSION));
                $unique_name = uniqid('libro_', true) . '.' . $fileType;
                $target_file = $target_dir . $unique_name; 

                // Validar tipo de archivo (opcional pero recomendado)
                $allowed_types = ['pdf', 'txt'];
                if (!in_array($fileType, $allowed_types)) {
                    throw new Exception('Solo se permiten archivos PDF y TXT.');
                }

                if (move_uploaded_file($_FILES["archivo_libro"]["tmp_name"], $target_file)) {
                    $filePath = $unique_name; // Guardamos solo el nombre del archivo
                } else {
                    throw new Exception('Hubo un error al subir el archivo.');
                }
            }

            if ($action === 'save') {
                // Obtenemos el ID del usuario de la sesión
                $idPersona = $_SESSION['DatosPersona']['id'];
                if ($libro->save($idPersona, $filePath)) {
                    $msg = 'Libro guardado correctamente.';
                } else {
                    throw new Exception('Error al guardar el libro.');
                }
            } elseif ($action === 'update') {
                // Solo los administradores pueden actualizar
                if (!$es_admin) {
                    throw new Exception('No tiene permisos para actualizar libros.');
                }
                $id = isset($_POST['idlibro']) ? (int)$_POST['idlibro'] : 0;
                if ($id > 0) {
                    $libro->setIdLibro($id);
                    if ($libro->update($filePath)) { // Pasamos el nuevo archivo si existe
                        $msg = 'Libro actualizado correctamente.';
                    } else {
                        throw new Exception('Error al actualizar el libro.');
                    }
                } else {
                    throw new Exception('ID inválido para actualizar.');
                }
            }
        } elseif ($action === 'delete') {
            // Solo los administradores pueden eliminar
            if (!$es_admin) {
                throw new Exception('No tiene permisos para eliminar libros.');
            }
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            if ($id > 0) {
                $libro = new Libro();
                if ($libro->delete($id)) {
                    $msg = 'Libro eliminado correctamente.';
                } else {
                    throw new Exception('Error al eliminar el libro.');
                }
            } else {
                throw new Exception('ID inválido para eliminar.');
            }
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }

    // --- PRG Pattern (Post/Redirect/Get) ---
    $_SESSION['abm_prod_msg'] = $msg;
    $_SESSION['abm_prod_error'] = $error;
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// --- OBTENER DATOS PARA LA VISTA ---
$flashMsg = $_SESSION['abm_prod_msg'] ?? '';
$flashError = $_SESSION['abm_prod_error'] ?? '';
unset($_SESSION['abm_prod_msg'], $_SESSION['abm_prod_error']);

$listaLibros = [];
try {
    $libroModel = new Libro();
    $listaLibros = $libroModel->getall(); // Este método ahora debería traer la columna 'imagen'
    if ($listaLibros === false) $listaLibros = [];
} catch (Exception $e) {
    $flashError = 'Error al obtener la lista de libros: ' . $e->getMessage();
}

if (file_exists(__DIR__ . '/Includes/header.php')) include __DIR__ . '/Includes/header.php';
?>
<body class="d-flex flex-column min-vh-100"
      style="background: url('imgs/fondo1.jpg') no-repeat center center fixed; background-size: cover;">

<main class="flex-fill container py-4">
    <div class="card shadow-lg rounded-4 p-4" style="background: #f6fff9; border: 2px solid #856133;">
        <div class="card-body">
            <h2 class="fw-bold text-center mb-4" style="color: #856133; -webkit-text-stroke: 1.5px #6e5215ff; font-size: 2rem;">
                ABM Libros
            </h2>

            <?php if ($flashMsg): ?><div class="alert alert-success"><?php echo htmlspecialchars($flashMsg); ?></div><?php endif; ?>
            <?php if ($flashError): ?><div class="alert alert-danger"><?php echo htmlspecialchars($flashError); ?></div><?php endif; ?>

            <div class="row">
                <!-- Formulario de Alta/Edición -->
                <div class="col-md-5">
                    <h4><?php echo $es_admin ? 'Nuevo / Editar Libro' : 'Agregar Nuevo Libro'; ?></h4>
                    <form method="post" id="form-libro" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="save" id="form-action">
                        <input type="hidden" name="idlibro" id="idlibro" value="">
                        <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">

                        <div class="mb-3"><label class="form-label">Título</label><input type="text" name="titulo" id="titulo" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Autor</label><input type="text" name="autor" id="autor" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Editorial</label><input type="text" name="editorial" id="editorial" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Archivo del Libro (PDF/TXT)</label><input type="file" name="archivo_libro" id="archivo_libro" class="form-control" accept=".pdf,.txt"></div>

                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <?php if ($es_admin): // El botón de limpiar solo es útil para el admin que edita ?>
                        <button type="button" id="btn-cancel" class="btn btn-secondary">Limpiar</button>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Listado de Libros -->
                <div class="col-md-7">
                    <h4>Listado de Libros</h4>
                    <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                        <table id="tabla-libros" class="table table-striped align-middle">
                            <thead><tr><th>Archivo</th><th>Título</th><th>Autor</th><th>Editorial</th><th>Acciones</th></tr></thead>
                            <tbody>
                                <?php if (!empty($listaLibros)): foreach ($listaLibros as $libro): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($libro['archivo'])): ?>
                                            <a href="uploads/<?php echo htmlspecialchars($libro['archivo']); ?>" target="_blank" class="btn btn-sm btn-outline-secondary">Ver</a>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($libro['Titulo']); ?></td>
                                    <td><?php echo htmlspecialchars($libro['Autor']); ?></td>
                                    <td><?php echo htmlspecialchars($libro['Editorial']); ?></td>
                                    <td>
                                        <?php if ($es_admin): // Los botones de editar y eliminar solo para admin ?>
                                        <button class="btn btn-sm btn-info btn-edit" data-id="<?php echo htmlspecialchars($libro['idlibro']); ?>" data-titulo="<?php echo htmlspecialchars($libro['Titulo']); ?>" data-autor="<?php echo htmlspecialchars($libro['Autor']); ?>" data-editorial="<?php echo htmlspecialchars($libro['Editorial']); ?>">Editar</button>
                                        <form method="post" style="display:inline-block;" onsubmit="return confirm('¿Confirma eliminar este libro?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($libro['idlibro']); ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr><td colspan="5">No hay libros registrados.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="inicio.php" class="btn btn-outline-secondary fw-bold mt-3">Volver al Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function(){
    <?php if ($es_admin): // El script para editar solo se necesita si es admin ?>
    document.querySelectorAll('.btn-edit').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.getElementById('idlibro').value = this.dataset.id;
            document.getElementById('titulo').value = this.dataset.titulo;
            document.getElementById('autor').value = this.dataset.autor;
            document.getElementById('editorial').value = this.dataset.editorial;
            document.getElementById('form-action').value = 'update';
            document.querySelector('h4').scrollIntoView({ behavior: 'smooth' });
        });
    });

    document.getElementById('btn-cancel').addEventListener('click', function(){
        document.getElementById('form-libro').reset();
        document.getElementById('idlibro').value = '';
        document.getElementById('form-action').value = 'save';
    });
    <?php endif; ?>
});
</script>

<script>
$(document).ready(function() {
    $('#tabla-libros').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json"
        }
    });
});
</script>

<?php if (file_exists(__DIR__ . '/Includes/footer.php')) include __DIR__ . '/Includes/footer.php'; ?>
