<?php
require_once __DIR__ . '/User.php';

class UserController {
    private $userModel;
    
    public function __construct($conexion) {
        $this->userModel = new User($conexion);
    }
    
    public function getUsers() {
        $result = $this->userModel->getAllUsers();
        $users = [];
        
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }
        
        return $users;
    }
    
    public function deleteUser($id) {
        return $this->userModel->deleteUser($id);
    }

    public function updateUser($id, $nombre, $dni, $correo, $telefono, $rol) {
        return $this->userModel->updateUser($id, $nombre, $dni, $correo, $telefono, $rol);
    }

    public function getUserDetails($id) {
        return $this->userModel->getUserById($id);  
    }
}
?>
