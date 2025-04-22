<!DOCTYPE html>
<html lang="en">

<head>
  <title>Login - InvenPro</title>
  <style>
    body {
      margin: 0;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: Arial, sans-serif;
      background: #f0f2f5;
    }

    .login-container {
      background: white;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
      text-align: center;
    }

    .logo {
      width: 200px;
      margin-bottom: 2rem;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    input {
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 1rem;
    }

    input[type="submit"] {
      background: #1a73e8;
      color: white;
      border: none;
      cursor: pointer;
      font-weight: bold;
      transition: background 0.3s;
    }

    input[type="submit"]:hover {
      background: #1557b0;
    }

    .error-popup {
      position: fixed;
      top: 20px;
      right: 20px;
      background: #fff;
      padding: 1rem;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      transform: translateX(150%);
      transition: transform 0.3s ease;
      z-index: 1000;
    }

    .error-popup.show {
      transform: translateX(0);
    }

    .message-container {
      display: flex;
      align-items: center;
      gap: 10px;
      color: #d32f2f;
    }

    .icon {
      font-family: 'Material Symbols Rounded';
      font-size: 24px;
    }

    .overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.3s ease;
    }

    .overlay.show {
      opacity: 1;
      pointer-events: auto;
    }
  </style>
</head>

<body>
<?php
  var_dump($_SESSION)
  ?>
  <div class="login-container">
    <img src="/images/logo-light.png" alt="InvenPro Logo" class="logo">
    <form action="/" method="POST">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="submit" value="Login">
    </form>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const errorPopup = document.querySelector('.error-popup');
      const overlay = document.querySelector('.overlay');

      if (errorPopup && errorPopup.classList.contains('show')) {
        setTimeout(() => {
          errorPopup.classList.remove('show');
          overlay.classList.remove('show');
        }, 3000);
      }

      if (overlay) {
        overlay.addEventListener('click', () => {
          errorPopup.classList.remove('show');
          overlay.classList.remove('show');
        });
      }
    });
  </script>
</body>

</html>
