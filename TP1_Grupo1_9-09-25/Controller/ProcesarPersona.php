    <?php


    require_once '../Model/ConexionBD.php';

    // Solo procesamos si la solicitud es POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../FormPersonas.php');
        exit;
    }

    // 1. Obtener y Sanear Datos
    $documento = trim($_POST['documento'] ?? '');
    $nombres = trim($_POST['nombres'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // 2. Validación Básica
    if (empty($documento) || empty($nombres) || empty($apellido) || empty($email) || empty($password)) {
        // Redirigir con error si faltan datos
        header('Location: ../FormPersonas.php?error=data_missing');
        exit;
    }

    // Inicializar objetos del modelo
    $oPersona = new Persona();
    $oDatosPersona = new DatosPersona();

    // 3. Crear la entrada en la tabla PERSONA
    try {
        // Sanear y asignar
        $oPersona->setDocumento($documento);
        $oPersona->setNombres($nombres);
        $oPersona->setApellido($apellido);

        // Guardar en la tabla Persona y capturar el nuevo ID
        // El método save() debe devolver el ID generado (last_id)
        $idPersona = $oPersona->save();

        if ($idPersona === false) {
            // Fallo en la inserción de Persona
            throw new Exception("Error al guardar datos personales.");
        }

        // 4. Crear la entrada en la tabla DATOSPERSONA (Login)

        // Asignar el ID de la nueva persona
        $oDatosPersona->setIdPersona($idPersona);
        // Asignar email y hashear/asignar la contraseña
        $oDatosPersona->setEmail($email);
        $oDatosPersona->setPass($password);

        // Guardar en la tabla DatosPersona
        $datosGuardados = $oDatosPersona->save();

        if ($datosGuardados === false) {
            // Fallo en la inserción de DatosPersona.
            // Opcional: Aquí se debería hacer un rollback y eliminar el registro de Persona.
            throw new Exception("Error al guardar credenciales de login.");
        }

        // Éxito: Redirigir al login
        header('Location: ../index.php?registro=success');
        exit;

    } catch (Exception $e) {
        // Si algo falla, redirigimos al formulario de registro con un error genérico
        error_log("Fallo en el registro de usuario: " . $e->getMessage());
        header('Location: ../FormPersonas.php?error=db_failure');
        exit;
    }

    ?>
