<?php
session_start();
require_once __DIR__ . '/config.php';

// Verificar que haya sesión de admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: Admin_Login.php');
    exit;
}

// (Opcional) Nombre del admin logueado
$adminNombre = isset($_SESSION['admin_nombre']) ? $_SESSION['admin_nombre'] : 'Administrador';

// Obtener los encargos pendientes
$sql = "SELECT id, nombre, correo, tipo_servicio, tipo_espacio, espacio_detalle,
               tamano, descripcion, imagen_ruta, estado, fecha_creacion
        FROM encargos
        WHERE estado = 'pendiente'
        ORDER BY fecha_creacion DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UmbralHome - Bandeja de entrada</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<header class="admin-navbar">
  <div class="admin-logo">
    <img src="assets/images/imagesInicio/logo.png" alt="UmbralHome">
  </div>

  <nav class="admin-nav">
    <a href="Admin_Bandeja.php" class="active">Bandeja de entrada</a>
    <a href="Admin_Procesos.php">Procesos</a>
    <span class="admin-user-tag">
      <?php echo htmlspecialchars($adminNombre, ENT_QUOTES, 'UTF-8'); ?>
    </span>
    <a href="logout.php" class="logout">Cerrar sesión</a>
  </nav>
</header>

<main class="encargos">

  <!-- Encabezado tipo tarjeta blanca -->
  <section class="encargos-header">
    <div class="encargos-header__inner">
      <h1>Encargos nuevos</h1>
    </div>
  </section>

  <!-- Lista de encargos -->
  <section class="encargos-lista">
    <div class="encargos-lista__inner">

      <?php if ($result && $result->num_rows > 0): ?>
        
        <?php while ($row = $result->fetch_assoc()): ?>
          <?php
            $id          = (int)$row['id'];
            $nombre      = htmlspecialchars($row['nombre'],        ENT_QUOTES, 'UTF-8');
            $servicio    = htmlspecialchars($row['tipo_servicio'], ENT_QUOTES, 'UTF-8');
            $espacio     = htmlspecialchars($row['tipo_espacio'],  ENT_QUOTES, 'UTF-8');
            $tamano      = htmlspecialchars($row['tamano'],        ENT_QUOTES, 'UTF-8');
            $descripcion = htmlspecialchars($row['descripcion'],   ENT_QUOTES, 'UTF-8');
            $fecha       = htmlspecialchars($row['fecha_creacion'],ENT_QUOTES, 'UTF-8');
            $imagen      = isset($row['imagen_ruta'])
                          ? htmlspecialchars($row['imagen_ruta'], ENT_QUOTES, 'UTF-8')
                          : '';
          ?>

          <!-- Tarjeta resumen -->
          <article class="encargo-card"
                   data-id="<?php echo $id; ?>"
                   data-nombre="<?php echo $nombre; ?>"
                   data-servicio="<?php echo $servicio; ?>"
                   data-espacio="<?php echo $espacio; ?>"
                   data-tamano="<?php echo $tamano; ?>"
                   data-descripcion="<?php echo $descripcion; ?>"
                   data-fecha="<?php echo $fecha; ?>"
                   data-imagen="<?php echo $imagen; ?>">

            <span>Agendamiento de:</span><br>
            <?php echo $nombre; ?><br>

            <small>
              <?php echo $servicio; ?>
              &nbsp;•&nbsp;
              <?php echo $espacio; ?>
            </small><br>

            <small>Fecha: <?php echo $fecha; ?></small>

          </article>

        <?php endwhile; ?>

      <?php else: ?>

        <!-- Tarjeta “vacía” -->
        <article class="encargo-card encargo-card--vacio">
          <span>No hay encargos pendientes.</span>
        </article>

      <?php endif; ?>

    </div>
  </section>

  <!-- Footer -->
  <footer class="projects-footer">
    <img src="assets/images/imagesInicio/logo.png" alt="UmbralHome">
    <p>
      Creamos espacios únicos y funcionales, diseñados para reflejar tu estilo
      y transformar tu hogar o negocio en un lugar armonioso y lleno de personalidad.
    </p>
  </footer>

</main>

<!-- ========== MODAL DETALLE ENCARGO ========== -->
<div class="encargo-detalle-backdrop" id="modalEncargo">
  <div class="encargo-detalle" id="modalEncargoCaja">
    <button class="encargo-detalle__cerrar" id="modalCerrar">&times;</button>

    <h2 id="modalTitulo">Agendamiento de:</h2>

    <div class="encargo-detalle__texto">
      <p><strong>Nombre:</strong> <span id="modalNombre"></span></p>
      <p><strong>Servicio solicitado:</strong> <span id="modalServicio"></span></p>
      <p><strong>Tipo de espacio:</strong> <span id="modalEspacio"></span></p>
      <p><strong>Tamaño aproximado:</strong> <span id="modalTamano"></span></p>
      <p><strong>Descripción:</strong><br><span id="modalDescripcion"></span></p>
      <p><strong>Fecha de solicitud:</strong> <span id="modalFecha"></span></p>
    </div>

    <div class="encargo-detalle__imagen" id="modalImagenWrapper" style="margin-top:12px;">
      <img id="modalImagen" src="" alt="Imagen del encargo">
    </div>

    <!-- Botones Aceptar / Rechazar -->
    <div class="encargo-detalle__acciones">
      <form action="actualizar_estado.php"
            method="post"
            id="modalForm"
            class="acciones-form"
            style="display:flex;
                   width:100%;
                   justify-content:space-between;
                   padding:0 20px;
                   box-sizing:border-box;
                   margin-top:16px;
                   gap:16px;">

        <input type="hidden" name="id" id="modalId">

        <!-- IZQUIERDA: ACEPTAR -->
        <button type="submit"
                name="accion"
                value="aceptar"
                class="btn-aceptar"
                style="background:#3ad28e;
                       color:#004446;
                       border:2px solid #3ad28e;
                       border-radius:6px;
                       padding:10px 18px;
                       font-weight:600;
                       cursor:pointer;
                       flex:1;">
          Aceptar
        </button>

        <!-- DERECHA: RECHAZAR -->
        <button type="submit"
                name="accion"
                value="rechazar"
                class="btn-rechazar"
                style="background:#ffffff;
                       color:#004446;
                       border:2px solid #ffffff;
                       border-radius:6px;
                       padding:10px 18px;
                       font-weight:600;
                       cursor:pointer;
                       flex:1;">
          Rechazar
        </button>

      </form>
    </div>

  </div>
</div>

<script>
// Abrir modal con los datos de la tarjeta
document.querySelectorAll('.encargo-card:not(.encargo-card--vacio)').forEach(card => {
  card.addEventListener('click', () => {
    const modal    = document.getElementById('modalEncargo');
    const nombre   = card.dataset.nombre || '';
    const servicio = card.dataset.servicio || '';
    const espacio  = card.dataset.espacio || '';
    const tamano   = card.dataset.tamano || '';
    const desc     = card.dataset.descripcion || '';
    const fecha    = card.dataset.fecha || '';
    const imagen   = card.dataset.imagen || '';
    const id       = card.dataset.id || '';

    document.getElementById('modalTitulo').textContent      = 'Agendamiento de: ' + nombre;
    document.getElementById('modalNombre').textContent      = nombre;
    document.getElementById('modalServicio').textContent    = servicio;
    document.getElementById('modalEspacio').textContent     = espacio;
    document.getElementById('modalTamano').textContent      = tamano || 'No especificado';
    document.getElementById('modalDescripcion').textContent = desc || 'Sin descripción';
    document.getElementById('modalFecha').textContent       = fecha;
    document.getElementById('modalId').value                = id;

    const imgWrapper = document.getElementById('modalImagenWrapper');
    const imgTag     = document.getElementById('modalImagen');

    if (imagen) {
      imgTag.src = imagen;
      imgWrapper.style.display = 'block';
    } else {
      imgTag.src = '';
      imgWrapper.style.display = 'none';
    }

    modal.classList.add('is-open');
  });
});

// Cerrar modal
const modalBackdrop = document.getElementById('modalEncargo');
const modalCaja     = document.getElementById('modalEncargoCaja');
const btnCerrar     = document.getElementById('modalCerrar');

btnCerrar.addEventListener('click', () => {
  modalBackdrop.classList.remove('is-open');
});

modalBackdrop.addEventListener('click', () => {
  modalBackdrop.classList.remove('is-open');
});

// Evitar que un clic dentro de la caja cierre el modal
modalCaja.addEventListener('click', e => {
  e.stopPropagation();
});
</script>

</body>
</html>