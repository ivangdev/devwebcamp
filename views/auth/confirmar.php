<main class="auth">
  <h2 class="auth__heading"><?php echo $titulo; ?></h2>
  <p class="auth__texto">Tu Cuenta DevWebCamp</p>

  <?php
  require_once __DIR__ . '/../templates/alertas.php'; // Mostrar alertas de errores o mensajes
  ?>

  <?php if (isset($alertas['success'])): ?>
    <div class="acciones--centrar">
      <a href="/login" class="acciones__enlace">Iniciar Sesión</a>
    </div>
  <?php endif; ?>
</main>