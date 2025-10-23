<?php
session_start();
require_once(CLASSES_PATH . "UsuarioManager.class.php");

try {
    $usuarioManager = new UsuarioManager();

    $email = $_POST['usuario'] ?? '';
    $clave = $_POST['clave'] ?? ''; // ya viene hasheada con sha512 desde JS

    $user = $usuarioManager->validarUsuario($email, $clave);

    if ($user) {
        $_SESSION['usuario_id'] = $user['id_usuario'];
        $_SESSION['usuario_nombre'] = $user['nombre'];
        header("Location: admin.php");
        exit();
    } else {
        echo "Usuario o contraseña incorrectos";
    }
} catch (Exception $e) {
    echo "Error en login: " . $e->getMessage();
}
