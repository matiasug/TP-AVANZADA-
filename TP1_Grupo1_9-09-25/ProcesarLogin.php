<?php

/* DESDE ACA HASTA LA LINEA 44 ES LO QUE NOS DEJO CANDE (CORREGIDO DEL TP 1 XD)


// 1. Incluimos el archivo que contiene nuestras clases para la base de datos.
require_once '../Model/ConexionBD.php';

// verificamos que la petición sea POST y que el token CSRF sea válido.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token']) && hash_equals($_SESSION['token'], $_POST['token'])) {

    // El token es válido, ahora procesamos el login.
    // Invalidamos el token después de usarlo para mayor seguridad.
    unset($_SESSION['token']);

    // 2. Verificamos el CAPTCHA y que los datos del formulario no estén vacíos.
    if (isset($_POST['nombre'], $_POST['cont'], $_POST['correo'], $_POST['rand_code']) && $_POST['rand_code'] == $_SESSION['rand_code']) {
        unset($_SESSION['rand_code']); // Invalidamos el CAPTCHA después de usarlo.

        // 3. Saneamos los datos recibidos del formulario.
        $nombre = trim($_POST['nombre']);
        $email = trim($_POST['correo']);
        $password = $_POST['cont']; // No usamos trim en la contraseña.

        // 4. Usamos la clase DatosPersona para validar el login contra la BD.
        $datosPersona = new DatosPersona();
        $idPersona = $datosPersona->verificarLogin($email, $password);

        if ($idPersona !== false) {
            // ¡Login exitoso!
            // Guardamos los datos del usuario en la sesión.
            $_SESSION['usuario'] = $nombre; // Guardamos el nombre para mostrarlo.
            $_SESSION['datos_usuario'] = [
                'id' => $idPersona,
                'correo' => $email
            ];

            // Regeneramos el ID de sesión para mayor seguridad.
            session_regenerate_id(true);
            // Se redirige al usuario a la página de inicio.
            header("Location: inicio.php");
            exit;  
        } else {
            // Si los datos del usuario son incorrectos (login fallido).
            header("Location: index.php?error=1");
            exit;
        }
    } else {
        // Si el CAPTCHA es incorrecto o faltan datos.
        header("Location: index.php?error=1");
        exit;
    }
} else {exit
    // Token inválido o no presente
    header("Location: index.php?error=1");
    exit;
}
*/
?><?php 
// ARCHIVO: procesarLogin.php

session_start();

// 🛑 CLAVE: Incluye el archivo que contiene las clases Persona y DatosPersona.
// Si este archivo falla, la clase no se encuentra. ¡VERIFICA LA RUTA!
require_once '../Model/ConexionBD.php';

// 1. Verificación de seguridad CSRF
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['token']) && hash_equals($_SESSION['token'], $_POST['token'])) {

    unset($_SESSION['token']); 
    
    // 2. Verificación de CAPTCHA y datos mínimos
    // 🛑 Corregido: Correo debe estar en el isset y ser usado.
    if (isset($_POST['cont'], $_POST['correo'], $_POST['rand_code']) && $_POST['rand_code'] == $_SESSION['rand_code']) {
        unset($_SESSION['rand_code']); 

        // Saneamos los datos
        $email = trim($_POST['correo']); 
        $password = $_POST['cont']; 
        
        // 3. Consultar la Base de Datos
        $datosPersona = new DatosPersona(); 
        $idPersona = $datosPersona->verificarLogin($email, $password); 
        
        if ($idPersona != false) {            
            // Login Exitoso
            $oPersona = new Persona();
            $datos_persona = $oPersona->getPersonaPorId($idPersona);
            
            // Usamos el operador ?? si el nombre no se encontró
            $_SESSION['usuario'] = $datos_persona['nombres'] ?? $email; 
            $_SESSION['datos_usuario'] = [
                'id' => $idPersona,
                'correo' => $email
            ];

            session_regenerate_id(true); 
            header("Location: inicio.php");
            exit;  
        } else {
            // Login Fallido (credenciales incorrectas)
            header("Location: index.php?error=1");
            exit;
        }
    } else {
        // CAPTCHA incorrecto o faltan datos
        header("Location: index.php?error=1");
        exit;
    }
} else {
    // Token CSRF inválido
    header("Location: index.php?error=1");
    exit;
}
?>



