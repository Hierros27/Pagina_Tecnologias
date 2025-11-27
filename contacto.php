<?php
session_start();

// Inicializamos variables para repoblar el formulario si hay error
$nombre   = $_POST['nombre']   ?? '';
$apellido = $_POST['apellido'] ?? '';
$correo   = $_POST['correo']   ?? '';
$errores  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validación simple del lado del servidor
    if (trim($nombre) === '') {
        $errores['nombre'] = 'Por favor ingresa tu nombre.';
    }

    if (trim($apellido) === '') {
        $errores['apellido'] = 'Por favor ingresa tu apellido.';
    }

    if (trim($correo) === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores['correo'] = 'Ingresa un correo electrónico válido.';
    }

    // Si no hay errores, guardamos en sesión y pasamos al paso 1 del servicio
    if (empty($errores)) {
        $_SESSION['nombre']   = trim($nombre);
        $_SESSION['apellido'] = trim($apellido);
        $_SESSION['correo']   = trim($correo);

        // Aquí NO insertamos todavía en la BD: lo hacemos en guardar_encargo.php,
        // junto con el resto de datos (tipo_servicio, espacio, etc.).
        header('Location: form_servicio_paso1.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UmbralHome - Contacto</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
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
    <li><a href="contacto.php">Contacto</a></li>
  </ul>
</header>

<main class="contact">

  <!-- HeaderC -->
  <section class="HeaderC">
    <div class="HeaderC__inner container">
      <h1>Contáctanos</h1>
      <p class="subtitle">Creamos ambientes que inspiran, cuidando cada detalle para reflejar tu esencia y estilo.</p>
    </div>
  </section>

  <!-- Intro -->
  <section class="contact-intro">
    <div class="container">
      <div class="intro-card">
        <p>
          Es para nosotros un placer conectar con usted y poder asesorarle en lo que necesite.
          Lo invitamos a completar la siguiente información.
        </p>
      </div>
    </div>
  </section>

  <!-- Formulario -->
  <section class="contact-form">
    <div class="container">
      <form id="contactForm" method="post">
        <div class="row two-cols">
          <div class="field">
            <label for="nombre">Nombre *</label>
            <input
              id="nombre"
              name="nombre"
              type="text"
              placeholder="Tu nombre"
              required
              value="<?php echo htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?>"
            >
            <?php if (!empty($errores['nombre'])): ?>
              <small class="error-msg"><?php echo $errores['nombre']; ?></small>
            <?php endif; ?>
          </div>
          <div class="field">
            <label for="apellido">Apellido *</label>
            <input
              id="apellido"
              name="apellido"
              type="text"
              placeholder="Tu apellido"
              required
              value="<?php echo htmlspecialchars($apellido, ENT_QUOTES, 'UTF-8'); ?>"
            >
            <?php if (!empty($errores['apellido'])): ?>
              <small class="error-msg"><?php echo $errores['apellido']; ?></small>
            <?php endif; ?>
          </div>
        </div>

        <div class="row">
          <div class="field">
            <label for="correo">Correo *</label>
            <input
              id="correo"
              name="correo"
              type="email"
              placeholder="tu@correo.com"
              required
              value="<?php echo htmlspecialchars($correo, ENT_QUOTES, 'UTF-8'); ?>"
            >
            <?php if (!empty($errores['correo'])): ?>
              <small class="error-msg"><?php echo $errores['correo']; ?></small>
            <?php endif; ?>
          </div>
        </div>

        <div class="row actions">
          <button type="submit" id="btn-agendar" class="btn-primary" disabled>
            Empezar agendamiento
          </button>
        </div>
      </form>
    </div>
  </section>

  <!-- Footer final (mismo estilo de Proyectos) -->
  <footer class="projects-footer">
    <img src="assets/images/imagesInicio/logo.png" alt="UmbralHome Logo">
    <p>
      Creamos espacios únicos y funcionales, diseñados para reflejar tu estilo y
      transformar tu hogar o negocio en un lugar armonioso y lleno de personalidad.
    </p>

    <!-- Botón secreto admin (ajusta a .php si lo cambiaste) -->
    <a href="Admin_Login.php" class="admin-secret-btn"></a>
  </footer>

</main>

<!-- Validación JS para habilitar/deshabilitar el botón -->
<script>
  const form = document.getElementById('contactForm');
  const inputs = form.querySelectorAll('input[required]');
  const btn    = document.getElementById('btn-agendar');

  function validarCampos() {
    let ok = true;

    inputs.forEach(input => {
      if (!input.value.trim()) {
        ok = false;
      }
    });

    btn.disabled = !ok;
  }

  inputs.forEach(input => {
    input.addEventListener('input', validarCampos);
  });

  // Chequeo inicial por si viene con valores (ej: después de error)
  validarCampos();
</script>

</body>
</html>