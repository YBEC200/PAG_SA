<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . 'database.php';
require_once __DIR__ . 'UserController.php';

$userController = new UserController($conexion);

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
        $action = $_GET['action'];
        
        if ($action === 'edit' && isset($_GET['id'])) {
            $user = $userController->getUserDetails($_GET['id']);
            if ($user) {
                echo json_encode(['success' => true, 'user' => $user]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
            }
            exit;
        }
        
        if ($action === 'delete' && isset($_GET['id'])) {
            $result = $userController->deleteUser($_GET['id']);
            echo json_encode(['success' => $result]);
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['action']) && $data['action'] === 'update') {
            $result = $userController->updateUser(
                $data['id'],
                $data['nombre'],
                $data['dni'],
                $data['correo'],
                $data['telefono'],
                $data['rol']
            );
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Usuario actualizado correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar el usuario']);
            }
            exit;
        }
    }

    echo json_encode(['success' => false, 'message' => 'Acción no válida']);

} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error en el servidor: ' . $e->getMessage()
    ]);
}
?>
