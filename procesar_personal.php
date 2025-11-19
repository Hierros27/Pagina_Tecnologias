<?php

// Crear carpeta si no existe
$carpeta = "uploads/";
if (!file_exists($carpeta)) {
    mkdir($carpeta, 0777, true);
}

// Procesar imagen
$nombreFinal = "No se cargó ninguna imagen.";

if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === 0) {

    $nombreTemp = $_FILES["imagen"]["tmp_name"];
    $nombreFinal = $carpeta . time() . "_" . basename($_FILES["imagen"]["name"]);

    if (!move_uploaded_file($nombreTemp, $nombreFinal)) {
        $nombreFinal = "Error al guardar la imagen.";
    }
}

// Procesar checkboxes
$espacios = isset($_POST["espacios"]) ? $_POST["espacios"] : [];

// Respuesta
echo "<h2>Datos recibidos exitosamente</h2>";

echo "<p><strong>Espacios seleccionados:</strong> " . 
     (!empty($espacios) ? implode(", ", $espacios) : "Ninguno") . 
     "</p>";

echo "<p><strong>Resultado de carga de imagen:</strong> $nombreFinal</p>";
