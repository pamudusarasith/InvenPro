<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>500 - Internal Server Error</title>
  <style>
    :root {
      --primary-600: #4a5568;
      --primary-700: #2d3748;
      --surface-white: #ffffff;
      --text-primary: #1a202c;
      --text-secondary: #4a5568;
      --gradient-start: #cbd5e0;
      --gradient-end: #a0aec0;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
    }

    .error-container {
      text-align: center;
      padding: 3.5rem;
      background: var(--surface-white);
      border-radius: 24px;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1),
        0 8px 10px -6px rgba(0, 0, 0, 0.1);
      max-width: 550px;
      width: 90%;
      animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-30px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .error-code {
      font-size: 7rem;
      font-weight: 800;
      background: linear-gradient(135deg, var(--primary-600), var(--primary-700));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 1.5rem;
      line-height: 1;
    }

    .error-title {
      font-size: 1.75rem;
      color: var(--text-primary);
      margin-bottom: 1rem;
      font-weight: 600;
    }

    .error-message {
      color: var(--text-secondary);
      margin-bottom: 2.5rem;
      line-height: 1.7;
      font-size: 1.1rem;
    }

    .home-link {
      display: inline-block;
      padding: 1rem 2rem;
      background: linear-gradient(135deg, var(--primary-600), var(--primary-700));
      color: var(--surface-white);
      text-decoration: none;
      border-radius: 12px;
      font-weight: 500;
      transition: all 0.3s ease;
      font-size: 1.1rem;
    }

    .home-link:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(245, 158, 11, 0.4);
    }

    @media (max-width: 480px) {
      .error-container {
        padding: 2rem;
      }

      .error-code {
        font-size: 5rem;
      }

      .error-title {
        font-size: 1.5rem;
      }
    }
  </style>
</head>

<body>
  <div class="error-container">
    <h1 class="error-code">500</h1>
    <h2 class="error-title">Internal Server Error</h2>
    <p class="error-message">
      Oops! Something went wrong on our servers.<br>
      We're working to fix the issue. Please try again later.
    </p>
    <a href="/" class="home-link">Return to Home</a>
  </div>
</body>

</html>
