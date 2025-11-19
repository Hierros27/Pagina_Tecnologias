<?php

if (!file_exists("uploads")) {
    mkdir("uploads", 0777, true);
}

$espacios = isset($_POST['espacio']) ? $_POST['espacio'] : [];
$tamano = $_POST['tamano'] ?? "";
$descripcion = $_POST['descripcion'] ?? "";

$imagenNombre = "";
if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === 0) {
    $ext = pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
    $imagenNombre = "trabajo_" . time() . "." . $ext;
    move_uploaded_file($_FILES["imagen"]["tmp_name"], "uploads/" . $imagenNombre);
}

$nuevoRegistro = [
    "tipo" => "trabajo",
    "espacios" => $espacios,
    "tamano" => $tamano,
    "descripcion" => $descripcion,
    "imagen" => $imagenNombre,
    "fecha" => date("Y-m-d H:i:s")
];

$archivo = "solicitudes.json";
$datos = [];

if (file_exists($archivo)) {
    $contenido = file_get_contents($archivo);
    $datos = json_decode($contenido, true) ?? [];
}

$datos[] = $nuevoRegistro;
file_put_contents($archivo, json_encode($datos, JSON_PRETTY_PRINT));

echo "Solicitud enviada correctamente.";
