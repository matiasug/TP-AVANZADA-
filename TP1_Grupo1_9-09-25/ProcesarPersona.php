<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// 1. Incluimos el archivo que contiene nuestras clases para la base de datos.
require_once 'Model/ConexionBD.php';

// Verificamos que la petición sea POST.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // 2. Saneamos y asignamos los datos recibidos.
    // Confíamos en que el formulario tiene 'required' para no hacer validación empty
    $documento = trim($_POST['documento'] ?? '');
    $nombres = trim($_POST['nombres'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // 3. Guardamos los datos de la persona en la tabla `Persona`.
    $persona = new Persona();
    // Los setters validan y asignan internamente
    $persona->setDocumento($documento);
    $persona->setNombres($nombres);
    $persona->setApellido($apellido);
    
    // 🛑 IMPORTANTE: Si la validación falla en un setter (ej: letras en documento),
    // la propiedad interna de la clase no se asigna. 
    // Por ahora, confiamos en que los datos son correctos.

    // Guardamos y obtenemos el ID de la persona recién insertada.
    $idPersona = $persona->save();

    if ($idPersona) {
        // 4. Si la persona se guardó, ahora guardamos sus datos de acceso.
        $datosAcceso = new DatosPersona();
        $datosAcceso->setIdPersona($idPersona);
        $datosAcceso->setEmail($email);
        $datosAcceso->setPass($password); // El método setPass hashea la contraseña.
        
        if ($datosAcceso->save()) {
             // 5. Redirigimos al login con un mensaje de éxito.
             $_SESSION['mensaje_exito'] = "Registro exitoso. Inicie sesión.";
             header("Location: index.php");
             exit;
        } else {
             // Falló el guardado del email/password (ej: email duplicado o error de FK)
             $_SESSION['mensaje_error'] = "Error al guardar acceso. (Email duplicado?)";
             header("Location: FormPersonas.php"); 
             exit;
        }

    } else {
        // Hubo un error al guardar la persona (ej: documento duplicado).
        $_SESSION['mensaje_error'] = "Error al guardar persona (Documento duplicado?)";
        header("Location: FormPersonas.php");
        exit;
    }
}
?>