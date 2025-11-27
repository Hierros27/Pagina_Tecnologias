<?php
session_start();
$tipo_servicio = $_SESSION['tipo_servicio'] ?? [];
$tipo_espacio  = $_SESSION['tipo_espacio'] ?? 'trabajo';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UmbralHome - Formulario Trabajo</title>
  <link rel="stylesheet" href="assets/css/style.css">

  <style>
    /* === NUEVO: estilo para la vista previa de imagen === */
    .preview-container {
      margin-top: 12px;
      display: flex;
      justify-content: center;
    }

    .preview-container img {
      width: 160px;
      height: auto;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.25);
      display: none; /* oculta hasta cargar */
    }
  </style>
</head>

<body>

  <!-- NAVBAR -->
  <header class="navbar">
    <div class="logo">
      <img src="assets/images/imagesInicio/logo.png" alt="Umbral Home Logo">
    </div>

    <input type="checkbox" id="menu-toggle">
    <label for="menu-toggle" class="menu-icon">&#9776;</label>

    <ul class="nav-links">
      <li><a href="index.html">Inicio</a></li>
      <li><a href="proyectos.html">Proyectos</a></li>
      <li><a href="servicios.html">Servicios</a></li>
      <li><a href="contacto.html">Contacto</a></li>
    </ul>
  </header>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="form-servicio">

    <div class="form-servicio__panel">

      <form id="formTrabajo" action="guardar_encargo.php" method="post" enctype="multipart/form-data">
      
      <?php foreach ($tipo_servicio as $ts): ?>
        <input type="hidden" name="tipo_servicio[]" value="<?php echo htmlspecialchars($ts, ENT_QUOTES, 'UTF-8'); ?>">
      <?php endforeach; ?>

      <input type="hidden" name="tipo_espacio" value="<?php echo htmlspecialchars($tipo_espacio, ENT_QUOTES, 'UTF-8'); ?>">

      <!-- Identificador del tipo de espacio (IMPORTANTE) -->
      <input type="hidden" name="tipo_espacio" value="trabajo">

      <!-- ¿Cómo es el espacio? -->
      <section class="bloque-pregunta">
        <h2>¿Cómo es el espacio?</h2>

        <div class="servicio-opciones">
          <label class="servicio-opcion">
            <input type="checkbox" name="espacio_trabajo[]" value="oficinas">
            <span class="servicio-opcion-box">
              <span class="checkbox-custom"></span>
              <span class="texto">Oficinas</span>
            </span>
          </label>

          <label class="servicio-opcion">
            <input type="checkbox" name="espacio_trabajo[]" value="pasillos">
            <span class="servicio-opcion-box">
              <span class="checkbox-custom"></span>
              <span class="texto">Pasillos</span>
            </span>
          </label>

          <label class="servicio-opcion">
            <input type="checkbox" name="espacio_trabajo[]" value="sala_reuniones">
            <span class="servicio-opcion-box">
              <span class="checkbox-custom"></span>
              <span class="texto">Sala de reuniones</span>
            </span>
          </label>
        </div>
      </section>

      <!-- Separador -->
      <div class="form-servicio__separador"></div>

      <!-- Tamaño -->
      <section class="bloque-pregunta">
        <h2>¿Aproximadamente qué tan grande es?</h2>
        <input type="text" class="campo-medida" name="tamano_trabajo" placeholder="Ej: 30 m²">
      </section>

      <!-- Descripción -->
      <section class="bloque-pregunta">
        <h2>Describa su idea</h2>
        <textarea class="campo-descripcion" name="descripcion_trabajo" placeholder="Escriba aquí su idea..."></textarea>
      </section>

      <!-- Añadir imagen -->
      <div class="imagen-btn-wrap">
        <label class="imagen-btn">
          Añadir imagen
          <input type="file" name="imagen_trabajo" id="imagen_trabajo" accept="image/*">
        </label>
      </div>

      <!-- NUEVO: vista previa -->
      <div class="preview-container">
        <img id="preview-imagen" src="" alt="Vista previa">
      </div>

      <!-- Finalizar -->
      <div class="finalizar-wrap">
        <button type="submit" class="btn-final">Finalizar</button>
      </div>

      </form>

    </div>

    <!-- FOOTER -->
    <footer class="projects-footer">
      <img src="assets/images/imagesInicio/logo.png" alt="UmbralHome">
      <p>
        Creamos espacios únicos y funcionales, diseñados para reflejar tu estilo
        y transformar tu hogar o negocio en un lugar armonioso y lleno de personalidad.
      </p>
    </footer>

  </main>

  <!-- SCRIPT VISTA PREVIA === NUEVO === -->
  <script>
    const inputFile = document.getElementById("imagen_trabajo");
    const previewImg = document.getElementById("preview-imagen");

    inputFile.addEventListener("change", function () {
      const file = this.files[0];
      if (!file) return;

      const reader = new FileReader();
      reader.onload = function (e) {
        previewImg.src = e.target.result;
        previewImg.style.display = "block";
      }
      reader.readAsDataURL(file);
    });
  </script>

</body>
</html>