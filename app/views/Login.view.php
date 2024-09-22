<?php
$errorMessage = isset($errorMessage) ? $errorMessage : '';
?>

<div class="overlay <?php echo !empty($errorMessage) ? 'show' : ''; ?>"></div>

<div class="error-popup <?php echo !empty($errorMessage) ? 'show' : ''; ?>">
    <div class="message-container">
        <span class="icon material-symbols-outlined">error</span>
        <div><?php echo htmlspecialchars($errorMessage); ?></div>
    </div>
</div>

<div class="login-container">
    <img class="logo" src="/images/logo-light.png" alt="logo">
    
    <form method="post" action="/login">
        <input type="text" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <input type="submit" value="Login" />
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', (event) => {
    var errorPopup = document.querySelector('.error-popup');
    var overlay = document.querySelector('.overlay');

    if (errorPopup) {
        errorPopup.classList.add('show');
        if (errorPopup && errorPopup.classList.contains('show')) {
            overlay.classList.add('show'); // Show the overlay
            setTimeout(() => {
                errorPopup.classList.remove('show'); // Hide the popup
                overlay.classList.remove('show'); // Hide the overlay
            }, 3000); // Adjust the timeout as needed
        }
    }

});
</script>

