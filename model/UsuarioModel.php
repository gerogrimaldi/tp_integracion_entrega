<?php

/* Variables dentro de $_SESSION 
Se debe mantener EXCLUSIVA para Login, datos de usuario y errores de login.

USUARIO
- user_id: ID del usuario autenticado
- user_email: Email del usuario autenticado
- tipoUsuario: Tipo de usuario (dueno, encargado)
- user_name: Nombre del usuario autenticado

LOGIN PROCESS
- captcha_error: Mensaje de error del captcha si no se completa correctamente
- captcha: Resultado del captcha
- login_error: Mensaje de error de login si las credenciales son incorrectas
- token: Token de sesión para seguridad adicional */

class Usuario{
    private $idUsuario;
    private $password;
	private $email;
    private $nombre;
    private $telefono;
    private $direccion;
    private $fechaNac;
    private $tipoUsuario; // dueno, encargado
    private $mysqli;
	
    public function __construct()
    {
        $this->mysqli = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
        if ($this->mysqli->connect_error) {
            die("Error de conexión a la base de datos: " . $this->mysqli->connect_error);
        }
    }

    public function setidUsuario($idUsuario)
    {
        $idUsuario = trim($idUsuario);
        if (is_numeric($idUsuario)) {
            $this->idUsuario = (int)$idUsuario;
        }
    }

    public function setPassword($password)
    { 
        $this->password = $password;
    }

    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    }
    
    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setNombre($nombre)
    {
        $nombre = trim($nombre);
        // Verifica si el nombre no está vacío
        if (!empty($nombre)) {
            $this->nombre = $nombre;
        }
    }

    public function setDate($fecha)
    {
        $this->fechaNac = new DateTime($fecha);
        $this->fechaNac = $this->fechaNac->format('Y-m-d H:i:s');
    }

    public function setTelefono($telefono)
    {
        if ( ctype_alnum($telefono)==true )
        {
            $this->telefono = $telefono;
        }
    }

    public function setTipoUsuario($tipoUsuario)
    {
        if (ctype_alnum($tipoUsuario)==true ){
            $this->tipoUsuario = $tipoUsuario;
        }
    }

    public function toArray()
    {
        $vUsuario=array(
            'password'=>$this->password,
            'email'=>$this->email,
            'nombre'=>$this->nombre,
            'telefono'=>$this->telefono,
            'direccion'=>$this->direccion,
            'fechaNac'=>$this->fechaNac
        );
        return $vUsuario;
    }

    public function getall()
    {
        $sql = "SELECT * FROM Usuarios";
        if ( $result = $this->mysqli->query($sql) )
		{
            $data = []; 
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            return $data;
			}else{
                return [];
            }
        }
        unset($result);
    }

    public function getUsuarioPorId($idUsuario)
    {
        $sql = "SELECT * FROM usuarios WHERE idUsuario=".$idUsuario;
        if ( $result = $this->mysqli->query($sql) ){
			$data = []; 
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            return $data;
			}else{
                return [];
            }
        }
        unset($result);
    }

    public function setMaxIDUsuario()
    {
        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            $sql = "SELECT MAX(idUsuario) AS maxID FROM usuarios";
            $result = $this->mysqli->query($sql);
            if (!$result) {
                throw new RuntimeException('Error al consultar el máximo ID: ' . $this->mysqli->error);
            }
            if ($result && $row = $result->fetch_assoc()) {
                $maxID = $row['maxID'] ?? 0;
                $this->idUsuario = $maxID + 1;
                return true;
            } else {
                throw new RuntimeException('Error al obtener el máximo ID: ' . $this->mysqli->error);
            }
        } catch (RuntimeException $e) {
            throw $e; 
        }
    }

    public function validar()
    {
        require_once("captcha_process.php");
        if (!isset($_SESSION['captcha']) || !$_SESSION['captcha']) {
            $_SESSION["captcha_error"] = "Por favor complete el captcha";
            return false;
        }
        try {
            // Prepare the statement to prevent SQL injection
            $sql = "SELECT idUsuario, email, password, tipoUsuario, nombre FROM usuarios WHERE email = ? LIMIT 1";
            $stmt = $this->mysqli->prepare($sql);

            if (!$stmt) {
             error_log("Error preparing statement: " . $this->mysqli->error);
                $_SESSION["login_error"] = "Error en el sistema: Validación SQL User.";
                return false;
            }
            // Bind the username parameter
            $stmt->bind_param("s", $this->email);
            
            // Execute the query
            if (!$stmt->execute()) {
                error_log("Error executing statement: " . $stmt->error);
                $_SESSION["login_error"] = "Error en el sistema. Ejecución SQL User.";
                return false;
            }
            // Get the result
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $usuario = $result->fetch_assoc();
                if ($this->verifyPassword($this->password, $usuario['password']))
                {
                    $this->setidUsuario($usuario['idUsuario']);
                    $this->setTipoUsuario($usuario['tipoUsuario']);
                    $this->setNombre($usuario['nombre']);
                    return true;
                }
            }
            $_SESSION["login_error"] = "Usuario y/o contraseña incorrectos";
            return false;
    
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            $_SESSION["login_error"] = $e->getMessage();
            return false;
        } finally {
            if (isset($stmt)) {
                $stmt->close();
            }
        }
    }

    public function validarPorID()
    {
        try {
            // Prepare the statement to prevent SQL injection
            $sql = "SELECT idUsuario, password FROM usuarios WHERE idUsuario = ? LIMIT 1";
            $stmt = $this->mysqli->prepare($sql);

            if (!$stmt) {
             error_log("Error preparing statement: " . $this->mysqli->error);
                $_SESSION["login_error"] = "Error en el sistema: Validación SQL User.";
                return false;
            }
            // Bind the username parameter
            $stmt->bind_param("s", $this->idUsuario);
            
            // Execute the query
            if (!$stmt->execute()) {
                error_log("Error executing statement: " . $stmt->error);
                $_SESSION["login_error"] = "Error en el sistema. Ejecución SQL User.";
                return false;
            }
            // Get the result
            $result = $stmt->get_result();
            if ($result->num_rows === 1) {
                $usuario = $result->fetch_assoc();
                if ($this->verifyPassword($this->password, $usuario['password']))
                {
                    return true;
                }
            }
            return false;
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        } finally {
            if (isset($stmt)) {
                $stmt->close();
            }
        }
    }

    // Valida el token de sesión y su expiración
    public function validarToken($token)
    {
        // Verificar que idUsuario sea un número válido (incluido 0)
        if (!is_int($this->idUsuario) || $this->idUsuario < 0) {
            return false;
        }
        // Verificar que el token no esté vacío
        if (!is_string($token) || $token === '') {
            return false;
        }
        $sql = "SELECT user_token, user_token_expir 
                FROM usuarios 
                WHERE idUsuario = ? 
                LIMIT 1";
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("i", $this->idUsuario);
        $stmt->execute();
        $stmt->bind_result($db_token, $db_token_expir);
        $valido = false;
        if ($stmt->fetch()) {
            if ($db_token === $token && strtotime($db_token_expir) > time()) {
                $valido = true;
            }
        }
        $stmt->close();
        return $valido;
    }

// CARGA; UPDATE, DELTE
    public function save()
    {
        // Verificar si el usuario ya existe
        $sqlCheck = "SELECT email FROM usuarios WHERE email = ?";
        $stmtCheck = $this->mysqli->prepare($sqlCheck);
        if (!$stmtCheck) {
            die("Error en la preparación de la consulta de verificación: " . $this->mysqli->error);
        }
        $stmtCheck->bind_param("s", $this->email);
        $stmtCheck->execute();
        $stmtCheck->store_result();
        if ($stmtCheck->num_rows > 0) {
            $stmtCheck->close();
            $this->mysqli->close();
            return false; 
        }
        $stmtCheck->close();
        // Hashear la contraseña antes de insertar
        $hashedPassword = $this->encryptPassword($this->password);
        $sql = "INSERT INTO usuarios (idUsuario, nombre, email, direccion, telefono, password, fechaNac, tipoUsuario) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt) {
            die("Error en la preparación de la consulta de inserción: " . $this->mysqli->error);
        }
        $stmt->bind_param(
            "isssssss",
            $this->idUsuario,
            $this->nombre,
            $this->email,
            $this->direccion,
            $this->telefono,
            $hashedPassword,
            $this->fechaNac,
            $this->tipoUsuario
        );
        $stmt->execute();
        $stmt->close();
        $this->mysqli->close();
        return true;
    }

    public function updateCampo($campo, $valor)
    {
        if (in_array($campo, ['nombre', 'email', 'direccion', 'telefono', 'fechaNac', 'password', 'tipoUsuario']) && !empty($valor)) {
            $sql = "UPDATE usuarios SET $campo = ? WHERE idUsuario = ?";
            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) {
                die("Error en la preparación de la consulta de actualización: " . $this->mysqli->error);
            }
            if ($campo === 'password') {
                $valor = $this->encryptPassword($valor); // Hashear la contraseña antes de guardarla
            }
            $stmt->bind_param("si", $valor, $this->idUsuario);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

public function deleteUsuarioPorId($idUsuario)
{
    try {
        $sql = "DELETE FROM usuarios WHERE idUsuario = ?";
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt) {
            throw new RuntimeException("Error en preparación: " . $this->mysqli->error);
        }
        $stmt->bind_param("i", $idUsuario);
        if ($stmt->execute()) {
            return true;
        } else {
            throw new RuntimeException("Error al ejecutar: " . $stmt->error);
        }
    } catch (Exception $e) {
        error_log("Error al eliminar usuario: " . $e->getMessage());
        return false;
    }
}

    private function encryptPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    private function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    public Function iniciarSesion()
    {
        $_SESSION['user_id'] = $this->idUsuario;
        $_SESSION['token'] = bin2hex(random_bytes(32)); // Genera un token de sesión seguro
        $_SESSION['tipoUsuario'] = $this->tipoUsuario;
        $_SESSION['user_name'] = $this->nombre;
        $_SESSION['user_email'] = $this->email;
        // Inserción del token en el usuario, para validaciones
        $sql = "UPDATE usuarios SET user_token = ?, user_token_expir = DATE_ADD(NOW(), INTERVAL 12 HOUR) WHERE idUsuario = ?"; 
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt) {
            die("Error en la preparación de la consulta de inserción: " . $this->mysqli->error);
        }
        // Enlaza los parámetros y ejecuta la consulta
        $stmt->bind_param("si", $_SESSION['token'], $_SESSION['user_id']);
        $stmt->execute();
    }

    public function cerrarSesion()
    {
        // Elimina el token de sesión del usuario
        $sql = "UPDATE usuarios SET user_token = NULL, user_token_expir = NULL WHERE idUsuario = ?";
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt) { 
            throw new RuntimeException("Error en la preparación de la consulta de cierre de sesión: " . $this->mysqli->error); 
        }
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        //Elimina la sesión
        session_unset(); // Elimina todas las variables de sesión
        session_destroy(); // Destruye la sesión
        // No hacer header ni exit aquí, dejar que el controlador maneje la respuesta
        return true;
    }

}