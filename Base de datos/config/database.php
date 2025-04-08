<?php
$host = 'localhost';  
$user = 'root'; 
$pass = '';
$db = 'botica';     
$conn = new mysqli($host, $user, $pass, $db);
// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}else {
    echo "Conexión exitosa"; 
}
?>
