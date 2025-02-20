<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email
{
  public string $email;
  public string $nombre;
  public string $token;

  /**
   * Constructor de la clase Email.
   *
   * @param string $email El email del destinatario.
   * @param string $nombre El nombre del destinatario.
   * @param string $token El token para la confirmación o restablecimiento.
   */
  public function __construct($email, $nombre, $token)
  {
    $this->email = $email;
    $this->nombre = $nombre;
    $this->token = $token;
  }

  /**
   * Enviar email de confirmación de cuenta.
   *
   * @return void
   */
  public function enviarConfirmacion(): void
  {
    // Crear un nuevo objeto PHPMailer
    $mail = new PHPMailer();
    $mail->isSMTP(); // Usar SMTP
    $mail->Host = $_ENV['EMAIL_HOST']; // Servidor SMTP
    $mail->SMTPAuth = true; // Habilitar autenticación SMTP
    $mail->Port = $_ENV['EMAIL_PORT']; // Puerto SMTP
    $mail->Username = $_ENV['EMAIL_USER']; // Usuario SMTP
    $mail->Password = $_ENV['EMAIL_PASS']; // Contraseña SMTP

    // Configurar el remitente y el destinatario
    $mail->setFrom('cuentas@devwebcamp.com'); // Remitente
    $mail->addAddress($this->email, $this->nombre); // Destinatario
    $mail->Subject = 'Confirma tu cuenta'; // Asunto del email

    // Configurar el contenido del email
    $mail->isHTML(true); // Establecer el formato del email a HTML
    $mail->CharSet = 'UTF-8'; // Establecer el juego de caracteres

    // Crear el contenido del email
    $contenido = '<html>';
    $contenido .= '<p><strong>Hola ' . $this->nombre . '</strong> Has creado tu cuenta en DevWebCamp, solo debes confirmarla presionando el siguiente enlace.</p>';
    $contenido .= '<p>Presiona aquí: <a href="' . $_ENV['HOST'] . '/confirmar?token=' . $this->token . '">Confirmar Cuenta</a></p>';
    $contenido .= '<p>Si tú no solicitaste este cambio, puedes ignorar este mensaje</p>';
    $contenido .= '</html>';
    $mail->Body = $contenido; // Establecer el cuerpo del email

    // Enviar el email
    $mail->send();
  }

  /**
   * Enviar email con instrucciones para restablecer la contraseña.
   *
   * @return void
   */
  public function enviarInstrucciones(): void
  {
    // Crear un nuevo objeto PHPMailer
    $mail = new PHPMailer();
    $mail->isSMTP(); // Usar SMTP
    $mail->Host = $_ENV['EMAIL_HOST']; // Servidor SMTP
    $mail->SMTPAuth = true; // Habilitar autenticación SMTP
    $mail->Port = $_ENV['EMAIL_PORT']; // Puerto SMTP
    $mail->Username = $_ENV['EMAIL_USER']; // Usuario SMTP
    $mail->Password = $_ENV['EMAIL_PASS']; // Contraseña SMTP

    // Configurar el remitente y el destinatario
    $mail->setFrom('cuentas@devwebcamp.com'); // Remitente
    $mail->addAddress($this->email, $this->nombre); // Destinatario
    $mail->Subject = 'Reestablecer tu contraseña'; // Asunto del email

    // Configurar el contenido del email
    $mail->isHTML(true); // Establecer el formato del email a HTML
    $mail->CharSet = 'UTF-8'; // Establecer el juego de caracteres

    // Crear el contenido del email
    $contenido = '<html>';
    $contenido .= '<p><strong>Hola ' . $this->nombre . '</strong> Has solicitado reestablecer tu contraseña, sigue el siguiente enlace para hacerlo.</p>';
    $contenido .= '<p>Presiona aquí: <a href="' . $_ENV['HOST'] . '/recuperar?token=' . $this->token . '">Reestablecer Contraseña</a></p>';
    $contenido .= '<p>Si tú no solicitaste este cambio, puedes ignorar este mensaje</p>';
    $contenido .= '</html>';
    $mail->Body = $contenido; // Establecer el cuerpo del email

    // Enviar el email
    $mail->send();
  }
}