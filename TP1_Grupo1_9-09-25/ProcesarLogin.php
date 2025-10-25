<?php 
// ARCHIVO: ProcesarLogin.php (Controlador)

session_start();

require_once 'Model/ConexionBD.php'; // Asegúrate que esta ruta sea correcta.

// 1. Verificación de seguridad CSRF
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['token']) && hash_equals($_SESSION['token'], $_POST['token'])) {

    unset($_SESSION['token']); 
    
    // 2. Verificación de CAPTCHA y datos mínimos
    if (isset($_POST['cont'], $_POST['correo'], $_POST['rand_code']) && $_POST['rand_code'] == $_SESSION['rand_code']) {
        
        // 🛑 El CAPTCHA es correcto, lo destruimos y seguimos:
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
            
            $_SESSION['usuario'] = $datos_persona['nombres'] ?? $email; 
            $_SESSION['DatosPersona'] = [
                'id' => $idPersona,
                'correo' => $email,
                'rol' => $datos_persona['rol'] 
            ];

            session_regenerate_id(true); 
            
            if ($_SESSION['DatosPersona']['rol'] == 'admin') {
                header ("Location: ListadoPersonas.php"); // Al panel de gestión ABM
            } else {
                header("Location: inicio.php"); // Al panel de usuario normal
            }
            exit;  

        } else {
            // Login Fallido (credenciales incorrectas)
            unset($_SESSION['rand_code']); 
            header("Location: index.php?error=1");
            exit;
        }
    } else {
        // CAPTCHA INCORRECTO: Destruye el código para que se genere uno nuevo
        unset($_SESSION['rand_code']);
        header("Location: index.php?error=1");
        exit;
    }
} else {
    // Token CSRF inválido
    header("Location: index.php?error=1");
    exit;
}
?>

