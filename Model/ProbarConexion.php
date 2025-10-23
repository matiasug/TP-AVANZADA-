<?php

// Incluimos tu archivo de clases (asume que se llama "ClasesBD.php")
require_once 'ConexionBD.php';

echo "<h2>Probando Conexiones...</h2>";

try {
    // Intentamos crear un objeto Persona
    $oPersona = new Persona();
    echo "<p style='color: green;'>✅ Conexión a 'Persona' exitosa.</p>";
    
    // Intentamos crear un objeto Libro
    $oLibro = new Libro();
    echo "<p style='color: green;'>✅ Conexión a 'Libro' exitosa.</p>";

    // Intentamos crear un objeto DatosPersona
    $oDatos = new DatosPersona();
    echo "<p style='color: green;'>✅ Conexión a 'DatosPersona' exitosa.</p>";
    
    // Si llegamos hasta acá, ¡todo perfecto!
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ ERROR FATAL: " . $e->getMessage() . "</p>";
}

// Los destructores (__destruct) se llaman automáticamente y cierran la conexión.
echo "Todas las conexiones se cerraron.";

?>