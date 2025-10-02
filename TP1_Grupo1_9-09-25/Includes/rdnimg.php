<?php
session_start();

// Generamos un nuevo código CAPTCHA.
$str = "";
$a = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
for ($i = 0; $i < 5; $i++) { // Aumenté a 5 caracteres para más seguridad
    $str .= $a[rand(0, 61)];
}
 
// Guardamos el código en la sesión para poder validarlo después.
$_SESSION['rand_code'] = $str;
 
// --- Creación de la imagen ---
header ('Content-Type: image/png');
$im = imagecreatetruecolor(120, 30);
$color_fondo = imagecolorallocate($im, 240, 240, 240); // Fondo más claro
$color_texto = imagecolorallocate($im, 50, 50, 50); // Texto oscuro
imagefilledrectangle($im, 0, 0, 120, 30, $color_fondo);
imagestring($im, 5, 25, 7, $str, $color_texto); // Centramos un poco el texto
imagepng($im);
imagedestroy($im);
?>
