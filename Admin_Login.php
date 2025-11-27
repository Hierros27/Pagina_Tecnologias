<?php
session_start();
require_once __DIR__ . '/config.php';

$error = "";

// Si ya está logueado, lo mandamos directo al panel
if (isset($_SESSION['admin_id'])) {
    header('Location: Admin_Bandeja.php');
    exit;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario  = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($usuario === '' || $password === '') {
        $error = "Por favor ingresa usuario y contraseña.";
    } else {
        // Buscar al admin en la BD
        $sql  = "SELECT id, username, nombre, password_hash 
                 FROM admins 
                 WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin  = $result->fetch_assoc();
        $stmt->close();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            // Login correcto → guardamos datos en sesión
            $_SESSION['admin_id']      = $admin['id'];
            $_SESSION['admin_usuario'] = $admin['username'];
            $_SESSION['admin_nombre']  = $admin['nombre'];

            header('Location: Admin_Bandeja.php');
            exit;
        } else {
            $error = "Usuario o contraseña incorrectos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>UmbralHome - Iniciar sesión</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="login">

    <div class="login-content">

      <!-- Título -->
      <section class="login-header">
        <div class="login-header__inner">
          <h1>Iniciar sesión</h1>
        </div>
      </section>

      <!-- Caja del formulario -->
      <section class="login-box">
        <div class="login-box__inner">

          <?php if ($error): ?>
            <p style="color:#ffdddd;background:#a94442;padding:8px;border-radius:4px;font-size:0.9rem;text-align:center;margin-bottom:10px;">
              <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </p>
          <?php endif; ?>

          <form id="loginForm" method="post" action="Admin_Login.php">
            <label for="usuario">Usuario</label>
            <input type="text" id="usuario" name="usuario" placeholder="Ingresa tu usuario">

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña">

            <button type="submit" class="btn-login">Iniciar sesión</button>
          </form>

        </div>
      </section>

    </div> <!-- Cierra login-content -->

    <!-- FOOTER -->
    <footer class="projects-footer">
      <img src="assets/images/imagesInicio/logo.png" alt="UmbralHome" />
      <p>
        Creamos espacios únicos y funcionales, diseñados para reflejar tu estilo
        y transformar tu hogar o negocio en un lugar armonioso y lleno de personalidad.
      </p>
    </footer>

  </main>

</body>
</html>