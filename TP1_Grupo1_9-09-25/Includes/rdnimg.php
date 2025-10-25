<?php
// ARCHIVO: Includes/rdnimg.php (Generador de CAPTCHA)

session_start();

if (!isset($_SESSION['rand_code'])) { 
    $str = "";
    $a = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    for ($i = 0; $i < 5; $i++) {
        $str .= $a[rand(0, 61)];
    }
    
    // Guardamos el NUEVO código
    $_SESSION['rand_code'] = $str;
} else {
    // Si ya existe, usamos el código que ya está en la sesión.
    $str = $_SESSION['rand_code'];
}


header ('Content-Type: image/png');
$im = imagecreatetruecolor(120, 30);
$color_fondo = imagecolorallocate($im, 240, 240, 240);
$color_texto = imagecolorallocate($im, 50, 50, 50);
imagefilledrectangle($im, 0, 0, 120, 30, $color_fondo);
imagestring($im, 5, 25, 7, $str, $color_texto); 
imagepng($im);
imagedestroy($im);
?>