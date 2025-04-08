<?php
class User {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function getAllUsers() {
        $query = "SELECT id, dni, nombre, correo, rol, fecha_registro, telefono 
                  FROM usuario 
                  ORDER BY id ASC";
        return $this->conexion->query($query);
    }
    
    public function deleteUser($id) {
        $query = "DELETE FROM usuario WHERE id = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function updateUser($id, $nombre, $dni, $correo, $telefono, $rol) {
        $query = "UPDATE usuario SET nombre = ?, dni = ?, correo = ?, telefono = ?, rol = ? WHERE id = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("sssssi", $nombre, $dni, $correo, $telefono, $rol, $id);
        return $stmt->execute();
    }

    public function getUserById($id) {
        $query = "SELECT id, dni, nombre, correo, telefono, rol, fecha_registro FROM usuario WHERE id = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();  
        } else {
            return null;  
        }
    }
}
?>
