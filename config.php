<?php
// Datos de conexión a MySQL en XAMPP
$host = "localhost";
$user = "root";
$pass = "";
$db   = "umbralhome";

// Crear conexión
$conn = new mysqli($host, $user, $pass, $db);

// Verificar error de conexión
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}
?>