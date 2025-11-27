<?php
session_start(); // Necesario para leer datos guardados en pasos anteriores

// Para depuración 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
require_once __DIR__ . '/config.php';   // 👈 OJO: _DIR_ con DOS guiones bajos

// 1. Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Acceso no permitido: este script solo acepta POST.');
}

/* =====================================================
   2. Datos de contacto (nombre / apellido / correo)
   ===================================================== */

// Datos guardados en sesión desde contacto.php (si existen)
$nombreSesion   = $_SESSION['nombre']   ?? '';
$apellidoSesion = $_SESSION['apellido'] ?? '';
$correoSesion   = $_SESSION['correo']   ?? '';

// En caso que en algun momento también se envíen por POST
$nombrePost   = $_POST['nombre']   ?? '';
$apellidoPost = $_POST['apellido'] ?? '';
$correoPost   = $_POST['correo']   ?? '';

// Nombre completo: priorizamos lo que venga por POST y si no, lo de sesión
$nombreCompleto = trim(
    ($nombrePost ?: $nombreSesion) . ' ' . ($apellidoPost ?: $apellidoSesion)
);
if ($nombreCompleto === '') {
    $nombreCompleto = 'Sin nombre';
}

// Correo: POST > sesión > valor por defecto
$correo = $correoPost ?: $correoSesion ?: 'sin-correo@ejemplo.com';

/* =====================================================
   3. Datos del servicio (tipo de espacio, servicios, etc.)
   ===================================================== */

// Tipo de espacio: POST > sesión > "personal"
$tipoEspacioSesion = $_SESSION['tipo_espacio'] ?? null;
$tipo_espacio = $_POST['tipo_espacio'] ?? $tipoEspacioSesion ?? 'personal';

// Tipo de servicio (arreglo tipo_servicio[])
$tipo_servicio_arr = (isset($_POST['tipo_servicio']) && is_array($_POST['tipo_servicio']))
    ? $_POST['tipo_servicio']
    : [];

$tipo_servicio = !empty($tipo_servicio_arr)
    ? implode(', ', $tipo_servicio_arr)
    : 'sin definir';

// Detalle del espacio: puede venir de espacio_trabajo[] o espacio_personal[]
$espacio_trabajo_arr = (isset($_POST['espacio_trabajo']) && is_array($_POST['espacio_trabajo']))
    ? $_POST['espacio_trabajo']
    : [];

$espacio_personal_arr = (isset($_POST['espacio_personal']) && is_array($_POST['espacio_personal']))
    ? $_POST['espacio_personal']
    : [];

$espacio_detalle_arr = !empty($espacio_trabajo_arr)
    ? $espacio_trabajo_arr
    : $espacio_personal_arr;

$espacio_detalle = !empty($espacio_detalle_arr)
    ? implode(', ', $espacio_detalle_arr)
    : 'sin especificar';

// Tamaño y descripción: primero trabajo, luego personal
$tamano = $_POST['tamano_trabajo']
    ?? ($_POST['tamano_personal'] ?? '');

$descripcion = $_POST['descripcion_trabajo']
    ?? ($_POST['descripcion_personal'] ?? '');

/* =====================================================
   4. Manejar imagen 
   ===================================================== */

$imagen_ruta = null;

// Verificamos si llegó imagen de trabajo o personal
$tieneImagenTrabajo  = isset($_FILES['imagen_trabajo'])  && $_FILES['imagen_trabajo']['error'] === UPLOAD_ERR_OK;
$tieneImagenPersonal = isset($_FILES['imagen_personal']) && $_FILES['imagen_personal']['error'] === UPLOAD_ERR_OK;

if ($tieneImagenTrabajo || $tieneImagenPersonal) {

    // Tomamos el archivo que exista y esté OK
    $archivo = $tieneImagenTrabajo
        ? $_FILES['imagen_trabajo']
        : $_FILES['imagen_personal'];

    $carpeta = __DIR__ . '/uploads/';   // 👈 Igual: _DIR_ con DOS guiones bajos

    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0755, true);
    }

    // Nombre único
    $nombreArchivo = time() . '_' . basename($archivo['name']);
    $rutaDestinoFS  = $carpeta . $nombreArchivo;    // ruta física en el servidor
    $rutaDestinoWEB = 'uploads/' . $nombreArchivo;  // ruta para guardar en BD

    if (move_uploaded_file($archivo['tmp_name'], $rutaDestinoFS)) {
        $imagen_ruta = $rutaDestinoWEB;
    }
}

/* =====================================================
   5. Insertar en la base de datos (tabla encargos)
   ===================================================== */

$stmt = $conn->prepare(
    "INSERT INTO encargos
    (nombre, correo, tipo_servicio, tipo_espacio, espacio_detalle, tamano, descripcion, imagen_ruta)
    VALUES (?,?,?,?,?,?,?,?)"
);

if (!$stmt) {
    die("Error al preparar la consulta: " . $conn->error);
}

$stmt->bind_param(
    "ssssssss",
    $nombreCompleto,
    $correo,
    $tipo_servicio,
    $tipo_espacio,
    $espacio_detalle,
    $tamano,
    $descripcion,
    $imagen_ruta
);

if (!$stmt->execute()) {
    die("Error al guardar el encargo: " . $stmt->error);
}

// ID insertado
$id_insertado = $conn->insert_id;

$stmt->close();

/* =====================================================
   6. Guardar también en archivo JSON 
   ===================================================== */

$jsonFile = __DIR__ . '/encargos.json';   // 

// Leer lo que haya
$encargos = [];

if (file_exists($jsonFile)) {
    $contenido = file_get_contents($jsonFile);
    $encargos = json_decode($contenido, true);
    if (!is_array($encargos)) {
        $encargos = [];
    }
}

// Añadimos el nuevo
$encargos[] = [
    'id'             => $id_insertado,
    'nombre'         => $nombreCompleto,
    'apellido'       => $apellidoPost ?: $apellidoSesion, // extra opcional
    'correo'         => $correo,
    'tipo_servicio'  => $tipo_servicio,
    'tipo_espacio'   => $tipo_espacio,
    'espacio_detalle'=> $espacio_detalle,
    'tamano'         => $tamano,
    'descripcion'    => $descripcion,
    'imagen_ruta'    => $imagen_ruta,
    'fecha_creacion' => date('Y-m-d H:i:s')
];

// Guardar el JSON actualizado
file_put_contents($jsonFile, json_encode($encargos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

/* =====================================================
   7. limpiar datos de contacto en sesión
   ===================================================== */
unset($_SESSION['nombre'], $_SESSION['apellido'], $_SESSION['correo']);

// 8. Redirigir a la pantalla de confirmación
header('Location: form_confirmacion.html');
exit;