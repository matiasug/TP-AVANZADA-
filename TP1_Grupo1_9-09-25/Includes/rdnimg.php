<?php
// ARCHIVO: Includes/rdnimg.php (Generador de CAPTCHA)

session_start();

// Generar un NUEVO código en cada petición y guardarlo en la sesión.
// Esto asegura que al refrescar la página se muestre un captcha distinto.
$str = "";
$a = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
for ($i = 0; $i < 5; $i++) {
    $str .= $a[rand(0, 61)];
}
// Guardamos el código generado en la sesión para su posterior verificación.
$_SESSION['rand_code'] = $str;

// Evitar que el navegador o proxies cacheen la imagen del captcha.
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

header ('Content-Type: image/png');
$im = imagecreatetruecolor(120, 30);
$color_fondo = imagecolorallocate($im, 240, 240, 240);
$color_texto = imagecolorallocate($im, 50, 50, 50);
imagefilledrectangle($im, 0, 0, 120, 30, $color_fondo);
imagestring($im, 5, 25, 7, $str, $color_texto); 
imagepng($im);
imagedestroy($im);
?>