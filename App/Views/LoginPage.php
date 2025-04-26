<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Login - InvenPro</title>
    <link rel="stylesheet" href="/css/main.css">
    <?php if (isset($stylesheets)) {
        foreach ($stylesheets as $filename): ?>
            <link rel="stylesheet" href="/css/<?= htmlspecialchars($filename) ?>.css">
        <?php endforeach;
    } ?>
  </head>

  <?php
    $message = $_SESSION['message'] ?? null;
    $messageType = $_SESSION['message_type'] ?? 'error';
    switch ($messageType) {
        case 'success':
            $popupIcon = 'check_circle';
            break;
        case 'warning':
            $popupIcon = 'warning';
            break;
        default:
            $popupIcon = 'error';
    }
unset($_SESSION['message'], $_SESSION['message_type']);
  ?>

  <body class="login-body">
    <div class="login-wrapper">
      <div class="login-form-container">
        <div class="login-header">
          <img src="/images/logo-light.png" alt="InvenPro Logo" class="logo">
          <p class="login-subtitle">Sales & Inventory Management System</p>
        </div>
        
        <form action="/" method="POST">
          <div class="form-field">
            <input type="email" name="email" placeholder="Email" required>
          </div>
          
          <div class="form-field">
            <input type="password" name="password" placeholder="Password" required>
          </div>
          
          <button type="submit" class="btn btn-primary btn-login">
            <span class="icon">login</span>
            Login
          </button>
        </form>
      </div>
    </div>
    
    <dialog id="messagePopup" class="popup <?= $messageType ?>">
        <span class="icon"><?= $popupIcon ?></span>
        <span class="popup-message"><?= htmlspecialchars($message ?? "") ?></span>
        <button class="popup-close" onclick="closePopup()">
            <span class="icon">close</span>
        </button>
    </dialog>
        
    <?php if (isset($scripts)) {
        foreach ($scripts as $filename): ?>
            <script src="/js/<?= $filename ?>.js"></script>
    <?php endforeach;
    } ?>
    <script src="/js/main.js"></script>

  </body>
</html>