<?php
session_start();
require_once __DIR__ . '/config.php';

// Verificar que haya sesión de admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: Admin_Login.php');
    exit;
}

$adminNombre = isset($_SESSION['admin_nombre']) ? $_SESSION['admin_nombre'] : 'Administrador';

/* ===========================
   OBTENER ID DEL ENCARGO
   =========================== */

$id = 0;

// 1) Desde la URL (GET)
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
}

// 2) Desde el formulario (POST oculto)
if ($id <= 0 && isset($_POST['id_encargo'])) {
    $id = (int)$_POST['id_encargo'];
}

// 3) Si seguimos sin ID, volvemos a Procesos
if ($id <= 0) {
    header('Location: Admin_Procesos.php');
    exit;
}

/* ======================================
   SI SE ENVÍA EL FORMULARIO DE FECHA
   ====================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_fecha'])) {
    $fechaPropuesta = $_POST['fecha_propuesta'] ?? null;

    if ($fechaPropuesta) {
        // 1) Verificar que la fecha no esté ocupada por otro encargo
        $sqlCheck = "SELECT id 
                     FROM encargos
                     WHERE fecha_agendada = ?
                       AND id <> ?";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bind_param("si", $fechaPropuesta, $id);
        $stmtCheck->execute();
        $resCheck   = $stmtCheck->get_result();
        $yaOcupada  = ($resCheck && $resCheck->num_rows > 0);
        $stmtCheck->close();

        if ($yaOcupada) {
            echo "<script>
                    alert('La fecha ya está ocupada. Por favor elige otra.');
                    window.location.href='Admin_Proceso_Detalle.php?id={$id}';
                  </script>";
            exit;
        }

        // 2) Guardar fecha y avanzar paso_proceso a 2
        $sqlUpdate = "UPDATE encargos 
                      SET paso_proceso = 2,
                          fecha_agendada = ?
                      WHERE id = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("si", $fechaPropuesta, $id);
        $stmtUpdate->execute();
        $stmtUpdate->close();
    }

    // ✅ Popup y regreso a la lista de procesos
    echo "<script>
            alert('Se ha enviado la fecha potencial al cliente.');
            window.location.href = 'Admin_Procesos.php';
          </script>";
    exit;
}

/* ===========================
   CONSULTAR DATOS DEL ENCARGO
   =========================== */

$sql = "SELECT * FROM encargos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result  = $stmt->get_result();
$encargo = $result->fetch_assoc();
$stmt->close();

if (!$encargo) {
    die('No se encontró el encargo solicitado.');
}

$nombre        = htmlspecialchars($encargo['nombre'],        ENT_QUOTES, 'UTF-8');
$tipoServicio  = htmlspecialchars($encargo['tipo_servicio'], ENT_QUOTES, 'UTF-8');
$tipoEspacio   = htmlspecialchars($encargo['tipo_espacio'],  ENT_QUOTES, 'UTF-8');
$descripcion   = nl2br(htmlspecialchars($encargo['descripcion'], ENT_QUOTES, 'UTF-8'));
$fechaCreacion = htmlspecialchars($encargo['fecha_creacion'], ENT_QUOTES, 'UTF-8');
$imagenRuta    = $encargo['imagen_ruta'] ?? '';

$paso = (int)($encargo['paso_proceso'] ?? 1);
if ($paso < 1) $paso = 1;
if ($paso > 4) $paso = 4;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>UmbralHome - Proceso Detalle</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>

<header class="admin-navbar">
    <div class="admin-logo">
      <img src="assets/images/imagesInicio/logo.png" alt="UmbralHome">
    </div>

    <nav class="admin-nav">
      <a href="Admin_Bandeja.php">Bandeja de entrada</a>
      <a href="Admin_Procesos.php" class="active">Procesos</a>

      <span class="admin-user-tag">
        <?php echo htmlspecialchars($adminNombre, ENT_QUOTES, 'UTF-8'); ?>
      </span>

      <a href="logout.php" class="logout">Cerrar sesión</a>
    </nav>
</header>

<main class="proceso-detalle">

  <!-- TÍTULO -->
  <section class="proceso-detalle-header">
    <div class="proceso-detalle-header__inner">
      <h1>Agendamiento de:<br><?php echo $nombre; ?></h1>
    </div>
  </section>

  <!-- ESPECIFICACIONES -->
  <section class="proceso-detalle-spec">
    <div class="proceso-detalle-spec__inner">

      <p><strong>Servicio solicitado:</strong> <?php echo $tipoServicio; ?></p>
      <p><strong>Tipo de espacio:</strong> <?php echo $tipoEspacio; ?></p>
      <p><strong>Descripción enviada:</strong><br><?php echo $descripcion; ?></p>

      <?php if (!empty($imagenRuta)): ?>
          <p><strong>Imagen adjunta:</strong></p>
          <img src="<?php echo htmlspecialchars($imagenRuta, ENT_QUOTES, 'UTF-8'); ?>" 
               alt="Imagen adjunta"
               style="max-width:300px;border-radius:10px;display:block;margin:0 auto;">
      <?php endif; ?>

      <p><strong>Fecha de solicitud:</strong> <?php echo $fechaCreacion; ?></p>

    </div>
  </section>

  <!-- PROGRESO -->
  <section class="proceso-detalle-track">
      <div class="proceso-detalle-track__inner">
          <div class="proceso-track">
            <?php for ($i = 1; $i <= 4; $i++): ?>
              <div class="track-step <?php echo ($paso >= $i ? 'done' : 'pending'); ?>"></div>

              <?php if ($i < 4): ?>
                <div class="track-line <?php echo ($paso > $i ? 'done' : 'pending'); ?>"></div>
              <?php endif; ?>
            <?php endfor; ?>
          </div>
      </div>
  </section>

  <!-- CAMBIO DEL PROCEDIMIENTO -->
  <section class="proceso-detalle-box">
      <div class="proceso-detalle-box__inner">

          <button type="button"
                  id="btnToggleCalendario"
                  class="btn-primary"
                  style="display:block; margin:0 auto 16px auto; width:fit-content;">
            Cambio del procedimiento
          </button>

          <form id="formCambioFecha"
                action="Admin_Proceso_Detalle.php?id=<?php echo $id; ?>"
                method="post"
                style="display:none;margin-top:15px;">

              <!-- ID oculto para recuperar el encargo en el POST -->
              <input type="hidden" name="id_encargo" value="<?php echo $id; ?>">

              <label for="fecha_propuesta" 
                     style="display:block;text-align:center;margin-bottom:6px;color:#ffffff;font-size:1rem;font-weight:600;">
                Selecciona la fecha de agendamiento:
              </label>

              <input type="date"
                     id="fecha_propuesta"
                     name="fecha_propuesta"
                     required
                     style="
                       width:100%;max-width:230px;padding:6px 8px;border-radius:4px;border:none;
                       font-size:0.9rem;display:block;margin:0 auto 10px auto;
                     ">

              <button type="submit"
                      name="guardar_fecha"
                      class="btn-primary"
                      style="margin-top:10px;display:block;margin-left:auto;margin-right:auto;">
                Enviar
              </button>

          </form>

      </div>
  </section>

</main>

<footer class="projects-footer">
  <img src="assets/images/imagesInicio/logo.png" alt="UmbralHome">
  <p>
    Creamos espacios únicos y funcionales, diseñados para reflejar tu estilo
    y transformar tu hogar en un lugar armonioso y lleno de personalidad.
  </p>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const btn  = document.getElementById('btnToggleCalendario');
  const form = document.getElementById('formCambioFecha');

  // Mostrar / ocultar el formulario al pulsar el botón
  if (btn && form) {
    btn.addEventListener('click', function () {
        form.style.display =
          (form.style.display === 'none' || form.style.display === '')
            ? 'block'
            : 'none';
    });
  }
});
</script>

</body>
</html>