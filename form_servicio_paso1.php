<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>UmbralHome - Solicitud de servicio (Paso 1)</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>

  <!-- NAVBAR PÚBLICA -->
  <header class="navbar">
    <div class="logo">
      <img src="assets/images/imagesInicio/logo.png" alt="Umbral Home Logo">
    </div>

    <!-- Botón de menú para móvil -->
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
      <form class="form-servicio-paso1" action="paso1_redirigir.php" method="post">

        <!-- ¿Qué tipo de servicio requiere? -->
        <section class="bloque-pregunta">
          <h2>¿Qué tipo de servicio requiere?</h2>

          <div class="servicio-opciones">
            <label class="servicio-opcion">
              <input type="checkbox" name="tipo_servicio[]" value="diseno">
              <span class="servicio-opcion-box">
                <span class="checkbox-custom"></span>
                <span class="texto">Diseño</span>
              </span>
            </label>

            <label class="servicio-opcion">
              <input type="checkbox" name="tipo_servicio[]" value="asesoramiento">
              <span class="servicio-opcion-box">
                <span class="checkbox-custom"></span>
                <span class="texto">Asesoramiento</span>
              </span>
            </label>

            <label class="servicio-opcion">
              <input type="checkbox" name="tipo_servicio[]" value="decoracion">
              <span class="servicio-opcion-box">
                <span class="checkbox-custom"></span>
                <span class="texto">Decoración</span>
              </span>
            </label>
          </div>
        </section>

        <div class="form-servicio__separador"></div>

        <!-- ¿Qué tipo de espacio es? -->
        <section class="bloque-pregunta">
          <h2>¿Qué tipo de espacio es?</h2>

          <div class="botones-espacio">
            <label class="btn-espacio">
              <input type="radio" name="tipo_espacio" value="personal" required>
              <span>Personal</span>
            </label>

            <label class="btn-espacio">
              <input type="radio" name="tipo_espacio" value="trabajo" required>
              <span>Trabajo</span>
            </label>
          </div>
        </section>

        <!-- Botón siguiente -->
        <div class="finalizar-wrap">
          <button type="submit" class="btn-final">Siguiente</button>
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

</body>
</html>