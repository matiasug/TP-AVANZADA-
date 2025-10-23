<?php 
session_start();

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
} else {
    // Token inválido o no presente
    header("Location: index.php?error=1");
    exit;
}
?>


hash_equals() es la herramienta fundamental y correcta en PHP 
para comparar cualquier tipo de "string secreto" (tokens, claves
 de API, etc.) para evitar que puedan ser adivinados mediante ataques de temporización