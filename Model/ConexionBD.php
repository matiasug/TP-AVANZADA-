<?php
// ARCHIVO: Model/ConexionBD.php

// =======================================================================
// CLASE CONEXIÓN
// =======================================================================

class Conexion {
    private static $mysqli;
    // Configuración local: Asegúrate que tu BD 2025_grupo1 exista en phpMyAdmin
    private static $host = '127.0.0.1';
    private static $user = 'root';
    private static $pass = '';
    private static $db = '2025_grupo1'; 

    public static function obtenerConexion() {
        if (!isset(self::$mysqli)) {
            self::$mysqli = new mysqli(self::$host, self::$user, self::$pass, self::$db);
            if (self::$mysqli->connect_error) {
                die('Error de Conexión a la BD: ' . self::$mysqli->connect_error);
            }
            self::$mysqli->set_charset("utf8mb4");
        }
        return self::$mysqli;
    }

    private function __construct() {}
}

// =======================================================================
// CLASE PERSONA
// =======================================================================

class Persona{
    
    private $idPersona;
    private $documento;
    private $apellido;
    private $nombres;
    private $mysqli;

    public function __construct()
    {
        $this->mysqli = Conexion::obtenerConexion();
    }

    public function setidPersona($idPersona)
    {
        if ( ctype_digit($idPersona) ) $this->idPersona = $idPersona;
    }

    public function setDocumento($documento)
    {
        if ( ctype_digit($documento) ) $this->documento = (int)$documento;
    }

    public function setApellido($apellido)
    {
        if (preg_match('/^[a-zA-Z\s]+$/', $apellido)) $this->apellido = $apellido;
    }

    public function setNombres($nombres)
    {
        if (preg_match('/^[a-zA-Z\s]+$/', $nombres)) $this->nombres = $nombres;
    }
    
    // Método para guardar y devolver el ID (Registro)
    public function save()
    {
        // Usamos Persona (mayúscula) para coincidir con la tabla SQL
        $sql = "INSERT INTO Persona (documento, apellido, nombres) VALUES (?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        
        // 🛑 CORRECCIÓN: 'iss' (Integer, String, String) para documento, apellido, nombres
        if (!$stmt || !$stmt->bind_param('iss', $this->documento, $this->apellido, $this->nombres)) {
            error_log("Error de preparación/bind (Persona): " . $this->mysqli->error);
            return false;
        }

        if ($stmt->execute()) {
            $last_id = $this->mysqli->insert_id;
            $stmt->close();
            return $last_id; 
        }

        error_log("Error de ejecución (Persona): " . $stmt->error);
        $stmt->close();
        return false;
    }
    
    // Método para obtener el nombre de la Persona (Para la sesión)
    public function getPersonaPorId($idPersona)
    {
        // Solo traemos el nombre que necesitamos para el saludo
        $sql = "SELECT nombres FROM Persona WHERE idPersona = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $idPersona);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado && $resultado->num_rows > 0) {
            $persona = $resultado->fetch_assoc();
            $stmt->close();
            return $persona;
        }
        
        $stmt->close();
        return false;
    }
    
    public function getall()
    {
        $sql = "SELECT * FROM Persona";

        if ( $resultado = $this->mysqli->query($sql) )
        {
            $personas = [];
            while ($fila = $resultado->fetch_assoc()) {
                $personas[] = $fila;
            }
            $resultado->free();
            return $personas;
        }
        return false;
    }
    
    // ... (Mantén el resto de los métodos update y delete)
}

// =======================================================================
// CLASE DATOSPERSONA
// =======================================================================

class DatosPersona{
    
    private $idPersona;
    private $pass; 
    private $email;
    private $mysqli;

    public function __construct()
    {
        $this->mysqli = Conexion::obtenerConexion();
    }

    public function setIdPersona($idPersona)
    {
        if (ctype_digit((string)$idPersona)) $this->idPersona = (int)$idPersona;
    }

    public function setPass($pass)
    {
        // El hasheo de contraseña es correcto y obligatorio [cite: 955, 1021]
        $this->pass = password_hash($pass, PASSWORD_DEFAULT);
    }

    public function setEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) $this->email = $email;
    }
    
    // Método para guardar email y hash de contraseña (Registro)
    public function save()
    {
        // Usamos DatosPersona (Mayúscula) para coincidir con la tabla SQL
        $sql = "INSERT INTO DatosPersona (idPersona, pass, email) VALUES (?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        
        if (!$stmt || !$stmt->bind_param('iss', $this->idPersona, $this->pass, $this->email)) {
             error_log("Error de preparación/bind (DatosPersona): " . $this->mysqli->error);
             return false;
        }
        
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }
        
        error_log("Error de ejecución (DatosPersona): " . $stmt->error);
        $stmt->close();
        return false;
    }

    // MÉTODO CLAVE PARA EL LOGIN
    public function verificarLogin($email, $pass)
    {
        // Usamos DatosPersona (Mayúscula)
        $sql = "SELECT idPersona, pass FROM DatosPersona WHERE email = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado && $resultado->num_rows == 1) {
            $fila = $resultado->fetch_assoc();
            // password_verify compara la contraseña plana con el hash guardado.
            if (password_verify($pass, $fila['pass'])) {
                $stmt->close();
                return $fila['idPersona']; // Login exitoso, devuelve el ID
            }
        }
        $stmt->close();
        return false; // Login fallido
    }
    // ... (Mantén el resto de los métodos delete)
}

// =======================================================================
// CLASE LIBRO
// =======================================================================

class libro{
    
    private $idPersona;
    private $idlibro;
    private $titulo;
    private $editorial;
    private $autor;
    private $mysqli;

    public function __construct()
    {
        $this->mysqli = Conexion::obtenerConexion();
    }

    public function setIdPersona($idPersona)
    {
        if (ctype_digit((string)$idPersona)) $this->idPersona = (int)$idPersona;
    }

    public function setidLibro($idlibro)
    {
        if (ctype_digit((string)$idlibro)) $this->idlibro = (int)$idlibro;
    }

    public function setTitulo($titulo)
    {
        $this->titulo = trim($titulo);
    }

     public function setTitulo($editorial)
    {
        $this->editorial = trim($editorial);
    }

     public function setAutor($autor)
    {
        $this->autor = trim($autor);
    }
    
    // Método para guardar libros (Registro) NO SE SI GUARDARIA CON EL IDPERSONA... puede ser igual XD
    public function save()
    {
        // Usamos Libro (Mayúscula) para coincidir con la tabla SQL
        $sql = "INSERT INTO Libro (idPersona, titulo, editorial, autor) VALUES (?, ?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        
        if (!$stmt || !$stmt->bind_param('isss', $this->idPersona, $this->titulo, $this->editorial, $this->autor)) {
            error_log("Error de preparación/bind (Libro): " . $this->mysqli->error);
            return false;
    }
        
        if ($stmt->execute()) {
            $this->idlibro = $this->mysqli->insert_id; 
            $stmt->close();
            return true;
    }
        
        error_log("Error de ejecución (Libro): " . $stmt->error);
        $stmt->close();
        return false;
    }

}


?>