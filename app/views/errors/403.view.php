<style>
  body {
    font-family: Arial, sans-serif;
    background-color: #f5f5f5;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
  }

  .error-container {
    text-align: center;
    padding: 2rem;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    max-width: 500px;
  }

  h1 {
    color: #e74c3c;
    font-size: 3rem;
    margin-bottom: 1rem;
  }

  .error-message {
    color: #555;
    margin-bottom: 1.5rem;
  }

  .home-link {
    display: inline-block;
    padding: 10px 20px;
    background-color: #3498db;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    transition: background-color 0.3s;
  }

  .home-link:hover {
    background-color: #2980b9;
  }
</style>

<div class="error-container">
  <h1>403</h1>
  <div class="error-message">
    <?php if (isset($message)): ?>
      <p><?php echo $message; ?></p>
    <?php else: ?>
      <p>Access Forbidden. You don't have permission to access this resource.</p>
    <?php endif; ?>
  </div>
  <a href="/" class="home-link">Return to Home</a>
</div>
