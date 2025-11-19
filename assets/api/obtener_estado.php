<?php
header("Content-Type: application/json");

// Ruta del archivo JSON donde se guarda el estado
$archivo = "estado.json";

// Si no existe, devolver estado 1 (solicitud recibida)
if (!file_exists($archivo)) {
    echo json_encode(["estado" => 1]);
    exit;
}

// Leer contenido JSON
$contenido = file_get_contents($archivo);
$data = json_decode($contenido, true);

// Asegurar que existe la clave "estado"
if (!isset($data["estado"])) {
    $data["estado"] = 1;
}

// Devolver en formato JSON
echo json_encode($data);
?>
