<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Start session for CSRF protection and rate limiting
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

// Set secure headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

// Load configuration from environment or config file
$config = require 'config.php';

class Message
{
	private $to = null;
	private $subject = null;
	private $message = null;
	public $status = array('codigo_status' => null, 'descricao_status' => '');

	public function __get($atributo)
	{
		return $this->$atributo;
	}

	public function __set($atributo, $valor)
	{
		$this->$atributo = $valor;
	}

	/**
	 * Validate message with security checks
	 * @return bool
	 */
	public function messageValida()
	{
		if (empty($this->to) || empty($this->subject) || empty($this->message)) {
			return false;
		}

		// Validate email format
		if (!filter_var($this->to, FILTER_VALIDATE_EMAIL)) {
			return false;
		}

		// Check length limits to prevent DoS
		if (strlen($this->subject) > 255 || strlen($this->message) > 10000) {
			return false;
		}

		return true;
	}
}

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	die('Method Not Allowed');
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
	$_POST['csrf_token'] !== $_SESSION['csrf_token']) {
	http_response_code(403);
	die('Invalid CSRF token. Please try again.');
}

// Rate limiting: Check if user has exceeded email limit (5 emails per minute)
$rate_limit_key = 'email_sent_' . (getenv('REMOTE_ADDR') ?: $_SERVER['REMOTE_ADDR']);
if (!isset($_SESSION[$rate_limit_key])) {
	$_SESSION[$rate_limit_key] = ['count' => 0, 'timestamp' => time()];
}

$current_time = time();
$last_check = $_SESSION[$rate_limit_key]['timestamp'];

// Reset counter if more than 60 seconds have passed
if ($current_time - $last_check > 60) {
	$_SESSION[$rate_limit_key] = ['count' => 0, 'timestamp' => $current_time];
}

// Check rate limit
if ($_SESSION[$rate_limit_key]['count'] >= 5) {
	http_response_code(429);
	die('Too many requests. Please try again later.');
}

$_SESSION[$rate_limit_key]['count']++;

$message = new Message();

// Sanitize input data
$to = isset($_POST['to']) ? trim($_POST['to']) : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
$body = isset($_POST['message']) ? trim($_POST['message']) : '';

$message->__set('to', $to);
$message->__set('subject', $subject);
$message->__set('message', $body);

if (!$message->messageValida()) {
	$message->status['codigo_status'] = 3;
	$message->status['descricao_status'] = 'Mensagem não é válida. Verifique os dados e tente novamente.';
	// Redirect to prevent form resubmission
	header('Location: index.php?status=invalid');
	exit;
}

$mail = new PHPMailer(true);
try {
	// Server settings with credentials from config/environment
	$mail->SMTPDebug = 0; // Set to 2 for debugging (but only in development!)
	$mail->isSMTP();
	$mail->Host = $config['smtp_host'];
	$mail->SMTPAuth = true;
	$mail->Username = $config['smtp_username'];
	$mail->Password = $config['smtp_password'];
	
	// Use TLS by default, SMTPS for port 465
	if ($config['smtp_port'] === 465) {
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
	} else {
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
	}
	$mail->Port = $config['smtp_port'];

	// Set timeout to prevent hanging
	$mail->Timeout = 10;

	// Recipients
	$mail->setFrom($config['smtp_from_email'], $config['smtp_from_name']);
	$mail->addAddress(filter_var($message->__get('to'), FILTER_VALIDATE_EMAIL));

	// Content
	$mail->isHTML(true);
	$mail->Subject = $message->__get('subject');
	$mail->Body = $message->__get('message');
	$mail->AltBody = 'É necessario utilizar um client que suporte HTML para ter acesso total ao conteúdo dessa mensagem';

	$mail->send();

	$message->status['codigo_status'] = 1;
	$message->status['descricao_status'] = 'E-mail enviado com sucesso!';
	
	// Store success message in session and redirect to prevent resubmission
	$_SESSION['success_message'] = $message->status['descricao_status'];
	header('Location: result.php?status=success');
	exit;

} catch (Exception $e) {
	// Log detailed error server-side (not shown to user)
	error_log('Email sending failed: ' . $mail->ErrorInfo);
	
	$message->status['codigo_status'] = 2;
	// Generic error message - no detailed information exposed
	$message->status['descricao_status'] = 'Não foi possível enviar este e-mail. Por favor tente novamente mais tarde.';
	
	// Store error in session and redirect
	$_SESSION['error_message'] = $message->status['descricao_status'];
	header('Location: result.php?status=error');
	exit;
}
?>

<html>

<head>
	<meta charset="utf-8" />
	<title>App Mail Send</title>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>

	<div class="container">
		<div class="py-3 text-center">
			<img class="d-block mx-auto mb-2" src="logo.png" alt="" width="72" height="72">
			<h2>Send Mail</h2>
			<p class="lead">Seu app de envio de e-mails particular!</p>
		</div>

		<div class="row">
			<div class="col-md-12">

				<?php if ($message->status['codigo_status'] == 1) { ?>

					<div class="container">
						<h1 class="display-4 text-success">Sucesso</h1>
						<p><?= $message->status['descricao_status'] ?></p>
						<a href="index.php" class="btn btn-success btn-lg mt-5 text-white">Voltar</a>
					</div>

				<?php } ?>

				<?php if ($message->status['codigo_status'] == 2) { ?>

					<div class="container">
						<h1 class="display-4 text-danger">Ops!</h1>
						<p><?= $message->status['descricao_status'] ?></p>
						<a href="index.php" class="btn btn-success btn-lg mt-5 text-white">Voltar</a>
					</div>

				<?php } ?>

			</div>
		</div>
	</div>

</body>

</html>