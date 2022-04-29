<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//print_r($_POST);

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

	public function messageValida()
	{
		if (empty($this->to) || empty($this->subject) || empty($this->message)) {
			return false;
		}

		return true;
	}
}

$message = new Message();

$message->__set('to', $_POST['to']);
$message->__set('subject', $_POST['subject']);
$message->__set('message', $_POST['message']);


if (!$message->messageValida()) {
	echo 'Mensagem não é válida';
	//die();
}

$mail = new PHPMailer(true);
try {
	//Server settings
	$mail->SMTPDebug = false;                      //Enable verbose debug output
	$mail->isSMTP();                                            //Send using SMTP
	$mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
	$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
	$mail->Username   = 'youremail@gmail.com';                     //SMTP username
	$mail->Password   = 'yourpassword';                               //SMTP password
	$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
	$mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

	//Recipients
	$mail->setFrom('youremail@gmail.com', 'Your name');
	$mail->addAddress($message->__get('to'));     //Add a recipient
	//$mail->addReplyTo('info@example.com', 'Information');
	//$mail->addCC('cc@example.com');
	//$mail->addBCC('bcc@example.com');

	//Attachments
	//$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
	//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

	//Content
	$mail->isHTML(true);                                  //Set email format to HTML
	$mail->Subject = $message->__get('subject');
	$mail->Body    = $message->__get('message');
	$mail->AltBody = 'É necessario utilizar um client que suporte HTML para ter acesso total ao conteúdo dessa mensagem';

	$mail->send();

	$message->status['codigo_status'] = 1;
	$message->status['descricao_status'] = 'E-mail enviado com sucesso';
} catch (Exception $e) {

	$message->status['codigo_status'] = 2;
	$message->status['descricao_status'] = 'Não foi possivel enviar este e-mail! Por favor tente novamente mais tarde. Detalhes do erro: ' . $mail->ErrorInfo;
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