<?php
session_start();

class Persona{
	
    private $idPersona;
    private $documento;
	private $apellido;
    private $nombres;
    private $telefono;

    private $mysqli;

	
    public function __construct()
    {
        $this->mysqli = new mysqli('127.0.0.1', 'root', '', 'Booksy_BD');
		// Es crucial verificar si la conexión falló.
		if ($this->mysqli->connect_error) {
			// En un entorno de producción, registrarías el error en lugar de mostrarlo.
			die('Error de Conexión (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error);
		}
	}

	public function __destruct()
	{
		$this->mysqli->close();
	}

    public function setidPersona($idPersona)
    {

        if ( ctype_digit($idPersona)==true )
        {
            $this->idPersona = $idPersona;
        }

    }

    public function setDocumento($documento)
    {

        if ( ctype_digit($documento)==true )
        {
            $this->documento = $documento;
        }

    }

    
    public function setApellido($apellido)
    {

        if ( ctype_alpha($apellido)==true )
        {
            $this->apellido = $apellido;
        }

    }


    public function setNombres($nombres)
    {

        if ( ctype_alpha($nombres)==true )
        {
            $this->nombres = $nombres;
        }

    }

    public function setTelefono($telefono)
    {

        if ( ctype_alnum($telefono)==true )
        {
            $this->telefono = $telefono;
        }

    }

    public function toArray()
    {
        $vPersona=array(
            'documento'=>$this->documento,
            'apellido'=>$this->apellido,
            'nombres'=>$this->nombres,
            'telefono'=>$this->telefono,
        );

        return $vPersona;

    }


    public function getall()
    {

        $sql = "SELECT * FROM personas";

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

    public function getPersonaPorId($idPersona)
    {
        $sql = "SELECT * FROM personas WHERE idPersona = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $idPersona);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado && $resultado->num_rows > 0) {
            $persona = $resultado->fetch_assoc();
            $resultado->free();
            $stmt->close();
            return $persona;
        }
        
        $stmt->close();
        return false;
    }





    public function save()
    {
        // Asumo que idPersona es AUTO_INCREMENT
        $sql = "INSERT INTO personas (documento, apellido, nombres, telefono) VALUES (?, ?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        // 'isss' indica los tipos: integer, string, string, string
        $stmt->bind_param('isss', $this->documento, $this->apellido, $this->nombres, $this->telefono);
        $stmt->execute();
        $stmt->close();
    }
    

    public function update()
    {
        $sql = "UPDATE personas SET documento = ?, apellido = ?, nombres = ?, telefono = ? WHERE idPersona = ?";
        $stmt = $this->mysqli->prepare($sql);
        // 'isssi' indica los tipos: integer, string, string, string, integer
        $stmt->bind_param('isssi', $this->documento, $this->apellido, $this->nombres, $this->telefono, $this->idPersona);
        $stmt->execute();
        $stmt->close();
    }

    
    public function deletePersonaPorId($idPersona)
    {
        $sql = "DELETE FROM personas WHERE idPersona = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $idPersona);
        $stmt->execute();
        $stmt->close();
    }

    

}


class Libro{
	
    private $idlibro;
    private $titulo;
	private $autor;
    private $editorial; 

    private $mysqli;

    public function __construct()
    {
        $this->mysqli = new mysqli('127.0.0.1', 'root', '', 'Booksy_BD');
		if ($this->mysqli->connect_error) {
			die('Error de Conexión (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error);
		}
    }

    public function __destruct()
	{
		$this->mysqli->close();
	}

    public function setIdLibro($idlibro)
    {
        if (ctype_digit($idlibro)) {
            $this->idlibro = $idlibro;
        }
    }

    public function setTitulo($titulo)
    {
        // Se podría agregar una validación más específica si es necesario
        $this->titulo = $titulo;
    }

    public function setAutor($autor)
    {
        if (ctype_alpha(str_replace(' ', '', $autor))) { // Permite letras y espacios
            $this->autor = $autor;
        }
    }

    public function setEditorial($editorial)
    {
        if (ctype_alpha(str_replace(' ', '', $editorial))) { // Permite letras y espacios
            $this->editorial = $editorial;
        }
    }

    // Métodos de Base de Datos

    public function toArray()
    {
        return [
            'titulo' => $this->titulo,
            'autor' => $this->autor,
            'editorial' => $this->editorial,
        ];
    }

    public function getall()
    {
        $sql = "SELECT * FROM libros";
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

    public function getLibroPorId($idLibro)
    {
        $sql = "SELECT * FROM libros WHERE idlibro = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $idLibro);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado && $resultado->num_rows > 0) {
            $libro = $resultado->fetch_assoc();
            $resultado->free();
            $stmt->close();
            return $libro;
        }
        $stmt->close();
        return false;
    }

    public function save()
    {
        // Asumo que idlibro es AUTO_INCREMENT
        $sql = "INSERT INTO libros (titulo, autor, editorial) VALUES (?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('sss', $this->titulo, $this->autor, $this->editorial);
        $stmt->execute();
        $stmt->close();
    }

    public function update()
    {
        $sql = "UPDATE libros SET titulo = ?, autor = ?, editorial = ? WHERE idlibro = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('sssi', $this->titulo, $this->autor, $this->editorial, $this->idlibro);
        $stmt->execute();
        $stmt->close();
    }

    public function deleteLibroPorId($idLibro)
    {
        $sql = "DELETE FROM libros WHERE idlibro = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $idLibro);
        $stmt->execute();
        $stmt->close();
    }
}

class DatosPersona{
	
    private $idPersona;
    private $pass; // Cambiado de 'password' a 'pass' para evitar conflictos con palabras reservadas de SQL
	private $email;

    private $mysqli;

    public function __construct()
    {
        $this->mysqli = new mysqli('127.0.0.1', 'root', '', 'Booksy_BD');
		if ($this->mysqli->connect_error) {
			die('Error de Conexión (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error);
		}
    }

    public function __destruct()
	{
		$this->mysqli->close();
	}

    // Setters
    public function setIdPersona($idPersona)
    {
        if (ctype_digit($idPersona)) {
            $this->idPersona = $idPersona;
        }
    }

    public function setPass($pass)
    {
        // ¡Importante! Las contraseñas NUNCA deben guardarse como texto plano.
        // Se usa password_hash() para crear un hash seguro.
        $this->pass = password_hash($pass, PASSWORD_DEFAULT);
    }

    public function setEmail($email)
    {
        // Validar que sea un formato de email
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email = $email;
        }
    }

    // Métodos de Base de Datos

    public function save()
    {
        // Asumo una tabla 'datos_persona' con idPersona, email, pass
        $sql = "INSERT INTO datos_persona (idPersona, email, pass) VALUES (?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('iss', $this->idPersona, $this->email, $this->pass);
        $stmt->execute();
        $stmt->close();
    }

    public function verificarLogin($email, $pass)
    {
        $sql = "SELECT idPersona, pass FROM datos_persona WHERE email = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado && $resultado->num_rows == 1) {
            $fila = $resultado->fetch_assoc();
            // password_verify() compara la contraseña ingresada con el hash guardado
            if (password_verify($pass, $fila['pass'])) {
                $stmt->close();
                return $fila['idPersona']; // Login exitoso, devuelve el ID
            }
        }
        $stmt->close();
        return false; // Login fallido
    }

    public function deleteDatosPorId($idPersona)
    {
        $sql = "DELETE FROM datos_persona WHERE idPersona = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $idPersona);
        $stmt->execute();
        $stmt->close();
    }
}