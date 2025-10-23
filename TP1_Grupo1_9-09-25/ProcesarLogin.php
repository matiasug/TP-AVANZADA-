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
/* DESDE ACA HASTA LA LINEA 44 ES LO QUE NOS DEJO CANDE (CORREGIDO DEL TP 1 XD)

if (isset($_POST['nombre']) && isset($_POST['cont']) && isset($_POST['correo'])) {
    //aca saneas los datos
    //cuando sanees la contraseña, tenes que buscar el user de la BD, sacar la contra y ahi recien dejarlo pasar si funciona
    //cuando le aplicas el agortimo, tiene que tener cierta cantidad de caracteres, asi que eso pdoria ser un filtro inicial y lueeego buscar la contraseña de la BD para terminar de controlar.

    //cuando creen un usuario, tienen que ponerle un "salt" para hashearlo ademas del algortimo sha512.

    if ($nombre == $_POST['nombre'] && $cont == $_POST['cont'] && $correo == $_POST['correo']) {
        echo "<h2>Bienvenido, " . htmlspecialchars($nombre) . "!</h2>";
        echo "<p>Has iniciado sesión con el correo: " . htmlspecialchars($correo) . "</p>";
        
       // Mostrar tabla con nombre y correo
        echo "<table border='1' style='margin-top:20px;'>";
        echo "<thead><tr><th>Nombre</th><th>Correo</th></tr></thead>";
        echo "<tbody>";
        echo "<tr><td>" . htmlspecialchars($nombre) . "</td><td>" . htmlspecialchars($correo) . "</td></tr>";
        echo "</tbody></table>";
    }
    else{

   header("Location: index.php?error=1");
        exit;
 

}
    
    
}
?>
*/
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