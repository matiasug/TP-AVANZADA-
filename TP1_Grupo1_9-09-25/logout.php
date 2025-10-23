<?php
session_start();

// 1. Elimina todas las variables de sesión.
$_SESSION = array();

// Si se desea destruir la sesión completamente, borre también la cookie de sesión.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}

// 2. Finalmente, destruye la sesión.
session_destroy();

// 3. Redirigir al formulario de login.
header("Location: index.php");
exit;
?>
