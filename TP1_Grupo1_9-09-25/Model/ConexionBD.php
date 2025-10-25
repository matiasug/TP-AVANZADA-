<?php


// =======================================================================
// CLASE CONEXIÓN
// =======================================================================

class Conexion {
    private static $mysqli;
    private static $host = '127.0.0.1';
    private static $user = 'root';
    private static $pass = ''; // Por defecto, vacío en XAMPP/LAMPP
    private static $db = '2025_grupo1'; 

    public static function obtenerConexion() {
        if (!isset(self::$mysqli)) {
            self::$mysqli = new mysqli(self::$host, self::$user, self::$pass, self::$db);
           
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
    private $rol; 
    private $mysqli;

    public function __construct()
    {
        $this->mysqli = Conexion::obtenerConexion();
    }
    
    public function setidPersona($idPersona)
    {
        // Se asume que $idPersona viene sanitizado
        if ( ctype_digit((string)$idPersona) ) $this->idPersona = (int)$idPersona;
    }

    public function setDocumento($documento)
    {
        // Se asume que el documento es una cadena de dígitos
        if ( ctype_digit((string)$documento) ) $this->documento = $documento; // Lo dejamos como string/entero flexible
    }

    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
        

    }

    public function setNombres($nombres)
    {
        $this->nombres = $nombres;
        

    }
    
    // Método para guardar y devolver el ID (Registro)
    public function save()
    {
        $sql = "INSERT INTO Persona (documento, apellido, nombres) VALUES (?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        
        // El 'sss' es el binding más flexible, adecuado para documento (si es VARCHAR), apellido y nombres.
        if (!$stmt || !$stmt->bind_param('sss', $this->documento, $this->apellido, $this->nombres)) {
            error_log("Error de preparación/bind (Persona): " . $this->mysqli->error);
            return false;
        }

        if ($stmt->execute()) {
            $last_id = $this->mysqli->insert_id;
            $stmt->close();
            return $last_id; // Devuelve el ID generado
        }

        error_log("Error de ejecución (Persona): " . $stmt->error);
        $stmt->close();
        return false;
    }
    
    public function getPersonaPorId($idPersona)
    {
        $sql = "SELECT nombres, rol FROM Persona WHERE idPersona = ?";
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt) {
             error_log("Error de preparación SQL (getPersonaPorId): " . $this->mysqli->error);
             return false;
        }
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
    
    public function toArray()
    {
        return [
            'idPersona' => $this->idPersona ?? null,
            'documento' => $this->documento ?? null,
            'apellido'  => $this->apellido ?? null,
            'nombres'   => $this->nombres ?? null
        ];
    }
}

// =======================================================================
// CLASE LIBRO (Correcta)
// =======================================================================
class Libro{
    
    private $idlibro;
    private $titulo;
    private $autor;
    private $editorial; 
    private $mysqli;

    public function __construct()
    {
        $this->mysqli = Conexion::obtenerConexion();
    }
    
    // ... (Setters y demás métodos se mantienen sin cambios mayores)
    public function setIdLibro($idlibro)
    {
        if (ctype_digit($idlibro)) {
            $this->idlibro = $idlibro;
        }
    }

    public function setTitulo($titulo)
    {
        $this->titulo = trim($titulo);
    }

    public function setAutor($autor)
    {
        $this->autor = trim($autor);
    }

    public function setEditorial($editorial)
    {
        $this->editorial = trim($editorial);
    }
    
    public function getall()
    {
        $sql = "SELECT * FROM Libro"; 
        if ($resultado = $this->mysqli->query($sql)) {
            $libros = [];
            while ($fila = $resultado->fetch_assoc()) {
                $libros[] = $fila;
            }
            $resultado->free();
            return $libros;
        }
        return false;
    }

    public function save()
    {
        $sql = "INSERT INTO Libro (Titulo, Autor, Editorial) VALUES (?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('sss', $this->titulo, $this->autor, $this->editorial);
        $stmt->execute();
        $stmt->close();
    }

    public function update()
    {
        $sql = "UPDATE Libro SET Titulo = ?, Autor = ?, Editorial = ? WHERE idlibro = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('sssi', $this->titulo, $this->autor, $this->editorial, $this->idlibro);
        $stmt->execute();
        $stmt->close();
    }
    
    public function deleteLibroPorId($idLibro)
    {
        $sql = "DELETE FROM Libro WHERE idlibro = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $idLibro);
        $stmt->execute();
        $stmt->close();
    }
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
        // Contraseña hasheada
        $this->pass = password_hash($pass, PASSWORD_DEFAULT);
    }

    public function setEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) $this->email = $email;
    }
    
    // Método para guardar email y hash de contraseña (Registro)
    public function save()
    {
        // Se asume que idPersona, pass y email son las columnas correctas.
        $sql = "INSERT INTO DatosPersona (idPersona, pass, email) VALUES (?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        
        // Se utiliza 'iss' para (Integer, String, String)
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

    // Método para verificar Login (Correcto)
    public function verificarLogin($email, $pass)
    {
        $sql = "SELECT idPersona, pass FROM DatosPersona WHERE email = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado && $resultado->num_rows == 1) {
            $fila = $resultado->fetch_assoc();
            if (password_verify($pass, $fila['pass'])) {
                $stmt->close();
                return $fila['idPersona']; 
            }
        }
        $stmt->close();
        return false; 
    }
}
?>
