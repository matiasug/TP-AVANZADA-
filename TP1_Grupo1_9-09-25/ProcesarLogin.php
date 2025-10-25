<?php 
// ARCHIVO: ProcesarLogin.php (Controlador)

session_start();


require_once './Model/ConexionBD.php';

// 1. Verificación de seguridad CSRF y método POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['token']) && hash_equals($_SESSION['token'], $_POST['token'])) {

    unset($_SESSION['token']); 
    
    // 2. Verificación de CAPTCHA y datos mínimos
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
            // Traemos ['nombres' y 'rol']
            $datos_persona = $oPersona->getPersonaPorId($idPersona);
            
            $_SESSION['usuario'] = $datos_persona['nombres'] ?? $email; 
            $_SESSION['datos_usuario'] = [
                'id' => $idPersona,
                'correo' => $email,
                'rol' => $datos_persona['rol'] 
            ];

            session_regenerate_id(true); 
            
            // LÓGICA DE REDIRECCIÓN POR ROL
            if ($_SESSION['datos_personas']['rol'] == 'admin') {
                header ("Location: Controller/ListadoPersonas.php"); // Al panel de gestión ABM
            } else {
                header("Location: inicio.php"); // Al panel de usuario normal
            }
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