<?php
session_start();

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: form_servicio_paso1.php');
    exit;
}

// Recoger datos del paso 1
// Si no marcan ningún checkbox, 'tipo_servicio' no existirá → usamos array vacío
$tipo_servicio = isset($_POST['tipo_servicio']) && is_array($_POST['tipo_servicio'])
    ? $_POST['tipo_servicio']
    : [];

// Si por alguna razón no llega 'tipo_espacio', asumimos 'personal'
$tipo_espacio = $_POST['tipo_espacio'] ?? 'personal';

// Guardar en sesión
$_SESSION['tipo_servicio'] = $tipo_servicio;
$_SESSION['tipo_espacio']  = $tipo_espacio;

// Redirigir según el tipo de espacio
if ($tipo_espacio === 'trabajo') {
    header('Location: form_trabajo.php');      
} else {
    header('Location: form_personal.php');
}
exit;