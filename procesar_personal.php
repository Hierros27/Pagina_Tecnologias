<?php

// Crear carpeta si no existe
$carpeta = "uploads/";
if (!file_exists($carpeta)) {
    mkdir($carpeta, 0777, true);
}

// Procesar imagen
if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === 0) {

    $nombreTemp = $_FILES["imagen"]["tmp_name"];
    $nombreFinal = $carpeta . time() . "_" . basename($_FILES["imagen"]["name"]);

    move_uploaded_file($nombreTemp, $nombreFinal);
}

// Procesar checkboxes
$espacios = isset($_POST["espacios"]) ? $_POST["espacios"] : [];

// Aquí puedes guardar todo en un JSON, BD o enviarlo por correo.
// Te dejo un mensaje simple:
echo "<h2>Datos recibidos exitosamente</h2>";
echo "<p>Espacios seleccionados: " . implode(", ", $espacios) . "</p>";
echo "<p>Imagen guardada en: $nombreFinal</p>";
