<?php


// =======================================================================
// CLASE CONEXIÓN
// =======================================================================

class Conexion {
    private static $mysqli;
    private static $host = '127.0.0.1';
    private static $user = 'root';
    private static $pass = ''; 
    private static $db = '2025_grupo1'; 

    public static function obtenerConexion() {
        if (!isset(self::$mysqli)) {
            self::$mysqli = new mysqli(self::$host, self::$user, self::$pass, self::$db);

            // --- VALIDACIÓN DE CONEXIÓN ---
            // Si la conexión falla, detenemos la ejecución y mostramos un error amigable.
            if (self::$mysqli->connect_error) {
                // En un entorno de producción, podrías mostrar una página de error más elaborada.
                die('Error de Conexión (' . self::$mysqli->connect_errno . ') ' . self::$mysqli->connect_error);
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

    public function update()
    {
        $sql = "UPDATE Persona SET documento = ?, apellido = ?, nombres = ? WHERE idPersona = ?";
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt) {
            error_log("Error de preparación SQL (Persona->update): " . $this->mysqli->error);
            return false;
        }
        $stmt->bind_param('sssi', $this->documento, $this->apellido, $this->nombres, $this->idPersona);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function delete($idPersona)
    {
        // Primero, eliminar los datos de login asociados
        $oDatosPersona = new DatosPersona();
        $oDatosPersona->deletePorIdPersona($idPersona);

        // Luego, eliminar la persona
        $sql = "DELETE FROM Persona WHERE idPersona = ?";
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param('i', $idPersona);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
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
        // Se une Persona con DatosPersona para obtener todos los datos relevantes
        $sql = "SELECT p.idPersona, p.documento, p.apellido, p.nombres, dp.email, p.rol 
                FROM Persona p
                LEFT JOIN DatosPersona dp ON p.idPersona = dp.idPersona
                ORDER BY p.apellido, p.nombres";
        if ( $resultado = $this->mysqli->query($sql) )
        {
            $personas = [];
            while ($fila = $resultado->fetch_assoc()) {
                $personas[] = $fila;
            }
            $resultado->free();
            return $personas; // Devolver el array de personas si la consulta fue exitosa
        }
        return false; // Devolver false solo si la consulta falla
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

    public function getById($idLibro)
    {
        $sql = "SELECT idlibro, Titulo, Autor, Editorial FROM Libro WHERE idlibro = ?";
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt) {
            error_log("Error de preparación SQL (getById): " . $this->mysqli->error);
            return false;
        }
        $stmt->bind_param('i', $idLibro);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado && $resultado->num_rows > 0) {
            $libro = $resultado->fetch_assoc();
            $stmt->close();
            return $libro;
        }
        $stmt->close();
        return false;
    }

    public function buscar($busqueda)
    {
        $busqueda = '%' . $busqueda . '%'; // Para búsqueda parcial
        $sql = "SELECT idlibro, Titulo, Autor, Editorial FROM Libro WHERE Titulo LIKE ? OR Autor LIKE ? OR Editorial LIKE ? ORDER BY Titulo ASC";
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt) {
            error_log("Error de preparación SQL (buscar): " . $this->mysqli->error);
            return false;
        }
        $stmt->bind_param('sss', $busqueda, $busqueda, $busqueda);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $libros = $resultado->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $libros;
    }

    public function save($idPersona, $filePath)
    {
        $sql = "INSERT INTO Libro (Titulo, Autor, Editorial, Persona_id, archivo) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt) {
            error_log("Error de preparación SQL (Libro->save): " . $this->mysqli->error);
            return false;
        }
        // Ajustamos el bind_param a 'sssis' (string, string, string, integer, string)
        if (!$stmt->bind_param('sssis', $this->titulo, $this->autor, $this->editorial, $idPersona, $filePath)) {
            error_log("Error en bind_param (Libro->save): " . $stmt->error);
            return false;
        }
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function update($filePath = null)
    {
        // Si se sube una nueva imagen, la actualizamos. Si no, mantenemos la existente.
        if ($filePath !== null) {
            $sql = "UPDATE Libro SET Titulo = ?, Autor = ?, Editorial = ?, archivo = ? WHERE idlibro = ?";
            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) {
                error_log("Error de preparación SQL (Libro->update con imagen): " . $this->mysqli->error);
                return false;
            }
            $stmt->bind_param('ssssi', $this->titulo, $this->autor, $this->editorial, $filePath, $this->idlibro);
        } else {
            // No se subió una nueva imagen, así que no actualizamos esa columna
            $sql = "UPDATE Libro SET Titulo = ?, Autor = ?, Editorial = ? WHERE idlibro = ?";
            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) {
                error_log("Error de preparación SQL (Libro->update sin imagen): " . $this->mysqli->error);
                return false;
            }
            $stmt->bind_param('sssi', $this->titulo, $this->autor, $this->editorial, $this->idlibro);
        }

        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
    
    // Renombrado para consistencia y mejor práctica
    public function delete($idLibro)
    {
        $sql = "DELETE FROM Libro WHERE idlibro = ?";
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt) {
            error_log("Error de preparación SQL (Libro->delete): " . $this->mysqli->error);
            return false;
        }
        $stmt->bind_param('i', $idLibro);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
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

    public function deletePorIdPersona($idPersona)
    {
        $sql = "DELETE FROM DatosPersona WHERE idPersona = ?";
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('i', $idPersona);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
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
