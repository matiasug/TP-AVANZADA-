<?php 
session_start();

// Array de usuarios permitidos. En un futuro, esto vendría de una base de datos.
$usuarios = [
    [
        'nombre_usuario' => 'fcytuader',
        'cont' => 'programacionavanzada',
        'correo' => 'fcytuader@correo.com',
    ],

];


// verificamos que la petición sea POST y que el token CSRF sea válido.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token']) && hash_equals($_SESSION['token'], $_POST['token'])) {

    // El token es válido, ahora procesamos el login.
    // invalidar el token después de usarlo para mayor seguridad.
    unset($_SESSION['token']);

    // verificamos los datos del usuario.
    // verificamos el CAPTCHA y los datos del usuario.
    if (isset($_POST['nombre'], $_POST['cont'], $_POST['correo'], $_POST['rand_code']) && $_POST['rand_code'] == $_SESSION['rand_code']) {
        unset($_SESSION['rand_code']); // Invalidamos el CAPTCHA después de usarlo (si no pongo esto no me deja acceder a inico).

        $usuario_encontrado = null;
        // Se busca el usuario en el array
        foreach ($usuarios as $usuario) {
            if ($usuario['nombre_usuario'] === $_POST['nombre'] && $usuario['cont'] === $_POST['cont'] && $usuario['correo'] === $_POST['correo']) {
                $usuario_encontrado = $usuario;
                break;
            }
        }

        if ($usuario_encontrado) {
            // Guardamos los datos del usuario encontrado en la sesión.
            $_SESSION['usuario'] = $usuario_encontrado['nombre_usuario'];
            $_SESSION['datos_usuario'] = [
                'correo' => $usuario_encontrado['correo']
            ];

            // Regeneramos el ID de sesión para mayor seguridad (IA) 
            session_regenerate_id(true);
            //Se redirige al usuario a la página de inicio. 
            header("Location: inicio.php");
            exit;  
        } else {
            // Si los datos del usuario o el CAPTCHA son incorrectos
            header("Location: index.php?error=1");
            exit;
        }
    } else {
        // Si el CAPTCHA es incorrecto o faltan datos
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