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

// Flag para el popup de fecha enviada (viene por sesión)
$mostrarModalFecha = !empty($_SESSION['fecha_enviada']);
if ($mostrarModalFecha) {
    // Lo usamos una sola vez
    unset($_SESSION['fecha_enviada']);
}

// Encargos aceptados → estado = 'en_proceso'
$sql = "SELECT id, nombre, tipo_servicio, tipo_espacio, paso_proceso, fecha_creacion
        FROM encargos
        WHERE estado = 'en_proceso'
        ORDER BY fecha_creacion DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>UmbralHome - Procesos</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>

  <!-- HEADER ADMIN -->
  <header class="admin-navbar">
    <div class="admin-logo">
      <img src="assets/images/imagesInicio/logo.png" alt="UmbralHome" />
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

  <!-- CONTENIDO PRINCIPAL -->
  <main class="procesos">

    <!-- Título -->
    <section class="procesos-header">
      <div class="procesos-header__inner">
        <h1>Tus encargos en proceso</h1>
      </div>
    </section>

    <!-- Lista de procesos -->
    <section class="procesos-lista">
      <div class="procesos-lista__inner">

        <?php if ($result && $result->num_rows > 0): ?>

          <?php while ($row = $result->fetch_assoc()): ?>
            <?php
              $paso = (int)($row['paso_proceso'] ?? 1);
              if ($paso < 1) $paso = 1;
              if ($paso > 4) $paso = 4;
            ?>

            <!-- Tarjeta clicable que lleva al detalle -->
            <a class="proceso-card-link"
               href="Admin_Proceso_Detalle.php?id=<?php echo (int)$row['id']; ?>">

              <article class="proceso-card">

                <div class="proceso-card__nombre">
                  <?php echo htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                </div>

                <!-- Info: servicio + espacio (arriba) y fecha centrada debajo -->
                <div class="proceso-card__info-extra">
                  <div class="proceso-card__tags">
                    <span class="proceso-card__tag">
                      <?php echo htmlspecialchars($row['tipo_servicio'], ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                    <span class="proceso-card__tag">
                      <?php echo htmlspecialchars($row['tipo_espacio'], ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                  </div>
                  <div class="proceso-card__fecha">
                    <?php echo htmlspecialchars($row['fecha_creacion'], ENT_QUOTES, 'UTF-8'); ?>
                  </div>
                </div>

                <!-- Barra de progreso -->
                <div class="proceso-track">
                  <?php
                    for ($i = 1; $i <= 4; $i++):
                      $stepClasses = 'track-step';
                      $lineClasses = 'track-line';

                      if ($paso >= $i) {
                        $stepClasses .= ' done';
                      } else {
                        $stepClasses .= ' pending';
                      }

                      if ($i < 4) {
                        if ($paso > $i) {
                          $lineClasses .= ' done';
                        } else {
                          $lineClasses .= ' pending';
                        }
                      }
                  ?>
                    <div class="<?php echo $stepClasses; ?>"></div>
                    <?php if ($i < 4): ?>
                      <div class="<?php echo $lineClasses; ?>"></div>
                    <?php endif; ?>
                  <?php endfor; ?>
                </div>

              </article>

            </a>

          <?php endwhile; ?>

        <?php else: ?>

          <article class="proceso-card proceso-card--vacio">
            <span>No hay encargos en proceso por el momento.</span>
          </article>

        <?php endif; ?>

      </div>
    </section>

    <!-- FOOTER -->
    <footer class="projects-footer">
      <img src="assets/images/imagesInicio/logo.png" alt="UmbralHome" />
      <p>
        Creamos espacios únicos y funcionales, diseñados para reflejar tu estilo
        y transformar tu hogar o negocio en un lugar armonioso y lleno de personalidad.
      </p>
    </footer>

  </main>

  <?php if ($mostrarModalFecha): ?>
    <!-- MODAL DE ÉXITO POR FECHA ENVIADA -->
    <div class="proceso-modal-backdrop" id="modalFechaEnviada">
      <div class="proceso-modal-card">
        <h3>Fecha enviada</h3>
        <p>Se ha enviado la fecha potencial al cliente.</p>
      </div>
    </div>
  <?php endif; ?>

  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalFechaEnviada');
    if (!modal) return;

    const card = modal.querySelector('.proceso-modal-card');

    // Clic fuera del recuadro → cerrar modal
    modal.addEventListener('click', function () {
      modal.style.display = 'none';
    });

    // Evitar cierre al hacer clic dentro del recuadro
    if (card) {
      card.addEventListener('click', function (e) {
        e.stopPropagation();
      });
    }
  });
  </script>

</body>
</html>