<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * EmailService handles email sending functionality using PHPMailer
 */
class EmailService
{
  private PHPMailer $mailer;
  private static ?EmailService $instance = null;

  /**
   * Initialize PHPMailer with Gmail SMTP settings
   */
  private function __construct()
  {
    $this->mailer = new PHPMailer(true); // true enables exceptions

    // Configure for Gmail
    $this->mailer->isSMTP();
    $this->mailer->Host = 'smtp.gmail.com';
    $this->mailer->SMTPAuth = true;
    $this->mailer->Username = $_ENV['EMAIL_USERNAME'];
    $this->mailer->Password = $_ENV['EMAIL_PASSWORD'];
    $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $this->mailer->Port = 587; // Gmail's SMTP port

    // Default sender
    $this->mailer->setFrom($_ENV['EMAIL_USERNAME'], 'InvenPro System');
    $this->mailer->isHTML(true);
  }

  /**
   * Get singleton instance
   *
   * @return EmailService
   */
  public static function getInstance(): EmailService
  {
    if (self::$instance === null) {
      self::$instance = new EmailService();
    }
    return self::$instance;
  }

  /**
   * Send an email using a PHP template file
   *
   * @param string|array $to Recipient email(s)
   * @param string $subject Email subject
   * @param string $templatePath Path to the PHP template file
   * @param array $data Data to be available in the template
   * @return bool Whether the email was sent successfully
   * @throws Exception
   */
  public function sendPhpTemplate($to, string $subject, string $templatePath, array $data = []): bool
  {
    try {
      $this->mailer->clearAddresses();

      // Handle single email or array of emails
      if (is_array($to)) {
        foreach ($to as $recipient) {
          $this->mailer->addAddress($recipient);
        }
      } else {
        $this->mailer->addAddress($to);
      }

      // Render the PHP template with the provided data
      $body = $this->renderPhpTemplate($templatePath, $data);

      $this->mailer->Subject = $subject;
      $this->mailer->Body = $body;
      $this->mailer->AltBody = strip_tags($body);

      return $this->mailer->send();
    } catch (Exception $e) {
      error_log('Email could not be sent. Mailer Error: ' . $this->mailer->ErrorInfo);
      throw $e;
    }
  }

  /**
   * Render a PHP template file with provided data
   *
   * @param string $templatePath Path to the PHP template file
   * @param array $data Data to be extracted for use in the template
   * @return string The rendered HTML
   */
  private function renderPhpTemplate(string $templatePath, array $data = []): string
  {
    // Extract the data to make variables available in the template
    extract($data);

    // Start output buffering
    ob_start();

    // Include the template file
    include $templatePath;

    // Get the buffer contents and clean the buffer
    return ob_get_clean();
  }

  /**
   * Enable SMTP debugging (only use during development)
   *
   * @param int $level Debug level (0-4)
   * @return EmailService
   */
  public function setDebug(int $level): EmailService
  {
    $this->mailer->SMTPDebug = $level;
    return $this;
  }
}
