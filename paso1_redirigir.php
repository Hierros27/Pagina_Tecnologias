<?php
session_start();

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: form_servicio_paso1.php');
    exit;
}

// Recoger datos del paso 1
$tipo_servicio = $_POST['tipo_servicio'] ?? [];
$tipo_espacio  = $_POST['tipo_espacio'] ?? null;

// Guardar en sesión
$_SESSION['tipo_servicio'] = $tipo_servicio;
$_SESSION['tipo_espacio']  = $tipo_espacio ?: 'personal';

// Redirigir según el tipo de espacio
if ($tipo_espacio === 'trabajo') {
    header('Location: form_trabajo.php');
} else {
    header('Location: form_personal.php');
}
exit;