<?php
// ========== CONFIG PARA INFINITYFREE ==========

//Datos de "MySQL Databases" InfinityFree:
$host = "sql305.infinityfree.com";          
$user = "if0_40452500";               
$pass = "UmbralHome";               
$db   = "if0_40452500_umbralhome";    // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($host, $user, $pass, $db);

// Verificar errores
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

// Opcional: Forzar UTF-8
$conn->set_charset("utf8mb4");
?>