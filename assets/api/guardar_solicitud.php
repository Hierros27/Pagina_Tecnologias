<?php
header("Content-Type: application/json");

// Verificar que el formulario llegó por POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "mensaje" => "Método no permitido"]);
    exit;
}

// Validaciones básicas
$nombre = $_POST["nombre"] ?? "";
$email = $_POST["email"] ?? "";
$descripcion = $_POST["descripcion"] ?? "";
$tipo = $_POST["tipo"] ?? "";  // personal o trabajo

if ($nombre === "" || $email === "" || $descripcion === "" || $tipo === "") {
    echo json_encode(["success" => false, "mensaje" => "Faltan datos obligatorios"]);
    exit;
}

// ----------- SUBIDA DE IMAGEN -----------

$imagenNombre = "";
$carpetaUploads = "../uploads/";

if (!empty($_FILES["imagen"]["name"])) {

    $extension = pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
    $imagenNombre = "img_" . time() . "." . $extension;

    $rutaDestino = $carpetaUploads . $imagenNombre;

    if (!move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino)) {
        echo json_encode(["success" => false, "mensaje" => "No se pudo subir la imagen"]);
        exit;
    }
}

// ----------- GUARDAR EN JSON -----------

$archivo = "estado.json";

$data = [
    "nombre" => $nombre,
    "email" => $email,
    "descripcion" => $descripcion,
    "tipo" => $tipo,
    "imagen" => $imagenNombre,
    "estado" => 1  // estado inicial: solicitud recibida
];

file_put_contents($archivo, json_encode($data, JSON_PRETTY_PRINT));

echo json_encode(["success" => true, "mensaje" => "Solicitud enviada correctamente"]);
?>
