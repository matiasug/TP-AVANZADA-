<?php
if (!session_id()) session_start();

require_once 'Model/ConexionBD.php';

// --- VERIFICACIÓN DE ROL ---
// Comprobamos si el usuario es un administrador. Si no ha iniciado sesión, $es_admin será false.
$es_admin = (isset($_SESSION['DatosPersona']) && $_SESSION['DatosPersona']['rol'] == 'admin');

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
    // Solo validamos el token si el usuario está logueado (es admin) o si el token existe en la sesión.
    // Esto permite que usuarios no logueados (sin token de sesión aún) puedan registrarse.
    if (isset($_SESSION['csrf_token']) && (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']))) {
        die('Error de validación CSRF.');
    }

    $action = $_POST['action'] ?? '';

    // Datos comunes para save y update
    $documento = trim($_POST['documento'] ?? '');
    $nombres = trim($_POST['nombres'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $idPersona = isset($_POST['idPersona']) ? (int)$_POST['idPersona'] : 0;

    try {
        if ($action === 'save' || $action === 'update') {
            // Si es un registro nuevo (no admin), la contraseña es obligatoria.
            if (!$es_admin && empty($password)) {
                 throw new Exception('La contraseña es obligatoria.');
            }
            if (empty($documento) || empty($nombres) || empty($apellido) || empty($email)) {
                throw new Exception('Completar todos los campos personales.');
            }

            $oPersona = new Persona();
            $oPersona->setDocumento($documento);
            $oPersona->setNombres($nombres);
            $oPersona->setApellido($apellido);
            // Por ahora, cualquier usuario nuevo se crea con rol 'user'.
            // En un futuro, se podría añadir un campo en el formulario para que el admin elija el rol.

            if ($action === 'save') {
                if (empty($password)) {
                    throw new Exception('La contraseña es obligatoria para nuevos usuarios.');
                }
                // Guardar Persona y obtener ID
                $newId = $oPersona->save();
                if (!$newId) throw new Exception('Error al guardar los datos personales.');

                // Guardar DatosPersona (login)
                $oDatosPersona = new DatosPersona();
                $oDatosPersona->setIdPersona($newId);
                $oDatosPersona->setEmail($email);
                $oDatosPersona->setPass($password);
                if (!$oDatosPersona->save()) throw new Exception('Error al guardar los datos de login.');
                
                // Si el que se registra no es un admin, lo mandamos al login para que ingrese.
                if (!$es_admin) {
                    // Usamos una variable de sesión para el mensaje porque vamos a redirigir.
                    $_SESSION['registro_exitoso'] = '¡Cuenta creada con éxito! Ahora puedes iniciar sesión.';
                    header("Location: index.php");
                    exit;
                } else {
                    $msg = 'Usuario creado correctamente.';
                }
            } elseif ($action === 'update') {
                if ($idPersona <= 0) throw new Exception('ID de persona inválido para actualizar.');
                
                $oPersona->setidPersona($idPersona);
                if (!$oPersona->update()) throw new Exception('Error al actualizar los datos personales.');
                
                // Lógica de actualización de contraseña (si se proporcionó una nueva)
                if (!empty($password)) {
                    $oDatosPersona = new DatosPersona();
                    $oDatosPersona->setIdPersona($idPersona);
                    $oDatosPersona->setPass($password);
                    // Necesitaríamos un método updatePassword en la clase DatosPersona
                    if (!$oDatosPersona->updatePassword()) {
                        throw new Exception('Error al actualizar la contraseña.');
                    }
                }
                // Nota: Deliberadamente no permitimos cambiar el email desde este formulario para mayor seguridad.
                
                $msg = 'Usuario actualizado correctamente.';
            }
        } elseif ($action === 'delete') {
            if (!$es_admin) throw new Exception('No tienes permisos para eliminar.');
            $idToDelete = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            if ($idToDelete <= 0) throw new Exception('ID inválido para eliminar.');

            $oPersona = new Persona();
            if ($oPersona->delete($idToDelete)) {
                $msg = 'Usuario eliminado correctamente.';
            } else {
                throw new Exception('Error al eliminar el usuario.');
            }
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }

    // --- PRG Pattern ---
    $_SESSION['abm_person_msg'] = $msg;
    $_SESSION['abm_person_error'] = $error;
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// --- OBTENER DATOS PARA LA VISTA ---
$flashMsg = $_SESSION['abm_person_msg'] ?? '';
$flashError = $_SESSION['abm_person_error'] ?? '';
unset($_SESSION['abm_person_msg'], $_SESSION['abm_person_error']);

$listaPersonas = [];
try {
    // Solo obtenemos la lista de usuarios si es un administrador.
    if ($es_admin) {
        $personaModel = new Persona();
        $listaPersonas = $personaModel->getall();
        if ($listaPersonas === false) $listaPersonas = [];
    }
} catch (Exception $e) {
    $flashError = 'Error al obtener la lista de usuarios: ' . $e->getMessage();
}

if (file_exists(__DIR__ . '/Includes/header.php')) include __DIR__ . '/Includes/header.php';
?>
<body class="d-flex flex-column min-vh-100"
      style="background: url('imgs/fondo1.jpg') no-repeat center center fixed; background-size: cover;">

<main class="flex-fill container py-4">
    <div class="card shadow-lg rounded-4 p-4" style="background: #f6fff9; border: 2px solid #856133;">
        <div class="card-body">
            <h2 class="fw-bold text-center mb-4" style="color: #856133; -webkit-text-stroke: 1.5px #6e5215ff; font-size: 2rem;">
                <?php echo $es_admin ? 'Gestión de Usuarios' : 'Crear una Cuenta'; ?>
            </h2>

            <!-- Botón para navegar a la gestión de libros -->
            <?php if ($es_admin): ?>
            <div class="mb-3">
                <a href="abmProductos.php" class="btn btn-success fw-bold">Gestionar Libros</a>
            </div>
            <?php endif; ?>

            <?php if ($flashMsg): ?><div class="alert alert-success"><?php echo htmlspecialchars($flashMsg); ?></div><?php endif; ?>
            <?php if ($flashError): ?><div class="alert alert-danger"><?php echo htmlspecialchars($flashError); ?></div><?php endif; ?>

            <div class="row">
                <!-- Formulario de Alta/Edición -->
                <div class="col-md-5">
                    <h4><?php echo $es_admin ? 'Nuevo / Editar Usuario' : 'Completa tus datos'; ?></h4>
                    <form method="post" id="form-persona">
                        <input type="hidden" name="action" value="save" id="form-action-persona">
                        <input type="hidden" name="idPersona" id="idPersona" value="">
                        <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">

                        <div class="mb-3"><label class="form-label">Documento</label><input type="text" name="documento" id="documento" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Nombres</label><input type="text" name="nombres" id="nombres" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Apellido</label><input type="text" name="apellido" id="apellido" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" id="email" class="form-control" required></div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" id="password" class="form-control" 
                                   placeholder="<?php echo $es_admin ? 'Dejar en blanco para no cambiar' : ''; ?>" 
                                   <?php echo !$es_admin ? 'required' : ''; ?>>
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <?php if ($es_admin): ?>
                        <button type="button" id="btn-cancel-persona" class="btn btn-secondary">Limpiar</button>
                        <?php else: ?>
                        <a href="index.php" class="btn btn-secondary">Volver al Login</a>
                        <?php endif; ?>
                    </form>
                </div>

                <?php if ($es_admin): // --- INICIO: Contenido solo para Administradores --- ?>
                <div class="col-md-7">
                    <h4>Listado de Usuarios</h4>
                    <div class="table-responsive">
                        <table id="tabla-personas" class="table table-striped">
                            <thead><tr><th>ID</th><th>Documento</th><th>Nombre Completo</th><th>Rol</th><th>Acciones</th></tr></thead>
                            <tbody>
                                <?php if (!empty($listaPersonas)): foreach ($listaPersonas as $p): ?>
                                <tr class="<?php echo ($p['rol'] == 'admin') ? 'table-info' : ''; ?>">
                                    <td><?php echo htmlspecialchars($p['idPersona']); ?></td>
                                    <td><?php echo htmlspecialchars($p['documento']); ?></td>
                                    <td><?php echo htmlspecialchars($p['apellido'] . ', ' . $p['nombres']); ?></td>
                                    <td><?php echo htmlspecialchars($p['rol']); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info btn-edit-persona" 
                                            data-id="<?php echo htmlspecialchars($p['idPersona']); ?>"
                                            data-doc="<?php echo htmlspecialchars($p['documento']); ?>"
                                            data-nombres="<?php echo htmlspecialchars($p['nombres']); ?>"
                                            data-apellido="<?php echo htmlspecialchars($p['apellido']); ?>" data-email="<?php echo htmlspecialchars($p['email']); ?>">Editar</button>

                                        <form method="post" style="display:inline-block;" onsubmit="return confirm('¿Confirma eliminar este usuario?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($p['idPersona']); ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr><td colspan="5">No hay usuarios registrados.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                     <a href="inicio.php" class="btn btn-outline-secondary fw-bold mt-3">Volver al Dashboard</a>
                </div>
                <?php endif; // --- FIN: Contenido solo para Administradores --- ?>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function(){
    <?php if ($es_admin): // El script de edición solo se necesita para el admin ?>
    document.querySelectorAll('.btn-edit-persona').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.getElementById('idPersona').value = this.dataset.id;
            document.getElementById('documento').value = this.dataset.doc;
            document.getElementById('nombres').value = this.dataset.nombres;
            document.getElementById('apellido').value = this.dataset.apellido;
            document.getElementById('email').value = this.dataset.email;
            document.getElementById('form-action-persona').value = 'update';
            // Al editar, la contraseña no es obligatoria
            document.getElementById('password').required = false;
            document.querySelector('h4').scrollIntoView({ behavior: 'smooth' });
        });
    });

    document.getElementById('btn-cancel-persona').addEventListener('click', function(){
        document.getElementById('form-persona').reset();
        document.getElementById('idPersona').value = '';
        document.getElementById('form-action-persona').value = 'save';
        // Al limpiar, la contraseña vuelve a ser no-obligatoria (el admin decide si la pone)
        document.getElementById('password').required = false;
    });
    <?php endif; ?>
});
</script>

<script>
$(document).ready(function() {
    <?php if ($es_admin): // La tabla con paginación solo se muestra al admin ?>
    $('#tabla-personas').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json"
        }
    });
    <?php endif; ?>
});
</script>

<?php if (file_exists(__DIR__ . '/Includes/footer.php')) include __DIR__ . '/Includes/footer.php'; ?>
