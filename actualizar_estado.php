<?php
session_start();
require_once __DIR__ . '/config.php';

// Verificar que haya sesión de admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: Admin_Login.php');
    exit;
}

// Asegurarnos de que llegue por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: Admin_Bandeja.php');
    exit;
}

$id     = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$accion = $_POST['accion'] ?? '';

// Validar datos mínimos
if ($id <= 0 || ($accion !== 'aceptar' && $accion !== 'rechazar')) {
    header('Location: Admin_Bandeja.php');
    exit;
}

// ==============================
// DEFINIR NUEVO ESTADO VÁLIDO
// ==============================
// ENUM en la BD es: 'pendiente','en_proceso','rechazado','finalizado'

if ($accion === 'aceptar') {
    $nuevoEstado = 'en_proceso';
} else {
    $nuevoEstado = 'rechazado';
}

// Actualizar estado en la BD
$stmt = $conn->prepare("UPDATE encargos SET estado = ? WHERE id = ?");
if (!$stmt) {
    die('Error al preparar la consulta: ' . $conn->error);
}

$stmt->bind_param('si', $nuevoEstado, $id);

if (!$stmt->execute()) {
    die('Error al actualizar el estado: ' . $stmt->error);
}
$stmt->close();

// Redireccionar según la acción
if ($accion === 'aceptar') {
    // Encargo aceptado → ahora está EN PROCESO
    header('Location: Admin_Procesos.php');
} else {
    // Encargo rechazado → seguimos viendo la bandeja
    header('Location: Admin_Bandeja.php');
}
exit;