<?php
header("Content-Type: application/json");

// Verificar si se envió por POST el nuevo estado
if (isset($_POST["estado"])) {
    
    $estado = intval($_POST["estado"]);

    // Validar que esté entre 1 y 4
    if ($estado < 1 || $estado > 4) {
        echo json_encode(["success" => false, "mensaje" => "Estado inválido"]);
        exit;
    }

    // Ruta del archivo JSON
    $archivo = "estado.json";

    // Crear estructura
    $data = ["estado" => $estado];

    // Guardar el estado en el archivo JSON
    file_put_contents($archivo, json_encode($data, JSON_PRETTY_PRINT));

    echo json_encode(["success" => true, "mensaje" => "Estado actualizado"]);

} else {
    echo json_encode(["success" => false, "mensaje" => "No se recibió el parámetro 'estado'"]);
}
?>
