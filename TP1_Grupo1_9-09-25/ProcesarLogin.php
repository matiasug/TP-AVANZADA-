<?php 
$nombre = "fcytuader";
$cont = "programacionavanzada";
$correo = "fcytuader@correo.com";

if (isset($_POST['nombre']) && isset($_POST['cont']) && isset($_POST['correo'])) {

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

