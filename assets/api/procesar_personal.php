<?php
// Crear carpeta de subida si no existe (solo XAMPP)
if (!file_exists("uploads")) {
    mkdir("uploads", 0777, true);
}

// Obtener datos del formulario
$espacios = isset($_POST['espacio']) ? $_POST['espacio'] : [];
$tamano = $_POST['tamano'] ?? "";
$descripcion = $_POST['descripcion'] ?? "";

// Manejo de la imagen
$imagenNombre = "";
if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === 0) {
    $ext = pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
    $imagenNombre = "personal_" . time() . "." . $ext;
    move_uploaded_file($_FILES["imagen"]["tmp_name"], "uploads/" . $imagenNombre);
}

// Construir los datos
$nuevoRegistro = [
    "tipo" => "personal",
    "espacios" => $espacios,
    "tamano" => $tamano,
    "descripcion" => $descripcion,
    "imagen" => $imagenNombre,
    "fecha" => date("Y-m-d H:i:s")
];

// Guardar en JSON
$archivo = "solicitudes.json";
$datos = [];

if (file_exists($archivo)) {
    $contenido = file_get_contents($archivo);
    $datos = json_decode($contenido, true) ?? [];
}

$datos[] = $nuevoRegistro;
file_put_contents($archivo, json_encode($datos, JSON_PRETTY_PRINT));

echo "Solicitud enviada correctamente.";
