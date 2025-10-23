<?php
session_start();

// Verificamos que la petición sea POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Verificamos que los datos no estén vacíos.
    if (empty($_POST['documento']) || empty($_POST['nombres']) || empty($_POST['apellido'])) {
        header("Location: FormPersonas.php?error=1"); // Redirigir con error si faltan datos
        exit;
    }

    // 2. Creamos la nueva persona para el "vector".
    // Guardamos todos los datos del formulario.
    $nueva_persona = [
        'documento' => trim($_POST['documento']),
        'nombres' => trim($_POST['nombres']),
        'apellido' => trim($_POST['apellido']),
    ];

    // 3. Agregamos la nueva persona al "vector" (array) en la sesión.
    // Usaremos 'personas' para que sea más claro.
    $_SESSION['personas'][] = $nueva_persona;

    // 4. Redirigimos a la página del listado para ver el resultado.
    header("Location: ListadoPersonas.php");
    exit;
}
?>
