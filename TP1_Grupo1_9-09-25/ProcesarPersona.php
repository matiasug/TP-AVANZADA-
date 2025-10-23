<?php
session_start();

// 1. Incluimos el archivo que contiene nuestras clases para la base de datos.
require_once '../Model/ConexionBD.php';

// Verificamos que la petición sea POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 2. Verificamos que todos los datos del formulario no estén vacíos.
    if (empty($_POST['documento']) || empty($_POST['nombres']) || empty($_POST['apellido']) || empty($_POST['email']) || empty($_POST['password'])) {
        header("Location: FormPersonas.php?error=1"); // Redirigir con error si faltan datos
        exit;
    }

    // 3. Saneamos y asignamos los datos recibidos.
    $documento = trim($_POST['documento']);
    $nombres = trim($_POST['nombres']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $password = $_POST['password']; // La contraseña no se trimea.

    // 4. Guardamos los datos de la persona en la tabla `personas`.
    $persona = new Persona();
    $persona->setDocumento($documento);
    $persona->setNombres($nombres);
    $persona->setApellido($apellido);
    // El teléfono es opcional, así que no lo incluimos por ahora.

    // Verificamos si los datos fueron validados y asignados correctamente por los setters.
    if (empty($persona->toArray()['nombres']) || empty($persona->toArray()['apellido'])) {
        header("Location: FormPersonas.php?error=3"); // Error: datos inválidos (ej: números en nombre)
        exit;
    }
    
    // Guardamos y obtenemos el ID de la persona recién insertada.
    $idPersona = $persona->save();

    if ($idPersona) {
        // 5. Si la persona se guardó, ahora guardamos sus datos de acceso.
        $datosAcceso = new DatosPersona();
        $datosAcceso->setIdPersona($idPersona);
        $datosAcceso->setEmail($email);
        $datosAcceso->setPass($password); // El método setPass hashea la contraseña automáticamente.
        $datosAcceso->save();

        // 6. Redirigimos al login con un mensaje de éxito.
        header("Location: index.php?registro=exitoso");
        exit;
    } else {
        // Hubo un error al guardar la persona.
        header("Location: FormPersonas.php?error=2"); // Error de base de datos
        exit;
    }
}
?>
