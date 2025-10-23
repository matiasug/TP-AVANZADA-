<?php 
$nombre = "fcytuader";
$cont = "programacionavanzada";
$correo = "fcytuader@correo.com";

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

