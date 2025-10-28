<?php
session_start();

require_once 'Model/ConexionBD.php'; 

// 1. Verificación de método POST y CSRF Token
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['token']) && isset($_SESSION['token']) && hash_equals($_SESSION['token'], $_POST['token'])) {

    // 2. Verificación de CAPTCHA
    $captcha_ok = false;
    if (isset($_POST['rand_code'], $_SESSION['rand_code'])) {
        // CORRECCIÓN CLAVE: Usamos strtoupper para comparar el código, ignorando mayúsculas/minúsculas.
        if (strtoupper($_POST['rand_code']) == strtoupper($_SESSION['rand_code'])) {
            $captcha_ok = true;
        }
    }
    
    // Si el CAPTCHA es correcto, continuamos con el login
    if ($captcha_ok) {
        
        // Destruimos el CAPTCHA una vez usado, independientemente del resultado del login.
        unset($_SESSION['rand_code']); 
        
        // Saneamos los datos
        $email = trim($_POST['correo'] ?? ''); 
        $password = $_POST['cont'] ?? ''; 
        
        // 3. Consultar la Base de Datos
        $datosPersona = new DatosPersona(); 
        $idPersona = $datosPersona->verificarLogin($email, $password); 
        
        if ($idPersona != false) {            
            // ÉXITO en el LOGIN
            unset($_SESSION['token']); // Destruimos el token CSRF tras login exitoso

            $oPersona = new Persona();
            $datos_persona = $oPersona->getPersonaPorId($idPersona); // Obtiene nombres y rol
            
            // Establecer variables de Sesión
            $_SESSION['usuario'] = $datos_persona['nombres'] ?? $email; 
            $_SESSION['DatosPersona'] = [
                'id' => $idPersona,
                'correo' => $email,
                'rol' => $datos_persona['rol'] ?? 'user' // Asignar 'user' por defecto si el rol no se encuentra
            ];

            session_regenerate_id(true); 
            
            // Redirección basada en el Rol
            // Redirigimos a todos los usuarios a la gestión de libros después del login.
            header("Location: abmProductos.php");
            exit;  

        } else {
            // Login Fallido (credenciales incorrectas o fallo de BD)
            header("Location: index.php?error=1");
            exit;
        }
    } else {
        // CAPTCHA INCORRECTO
        unset($_SESSION['rand_code']);
        header("Location: index.php?error=1");
        exit;
    }
} else {
    // Token CSRF inválido o método incorrecto
    header("Location: index.php?error=1");
    exit;
}
?>
