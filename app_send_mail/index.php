<?php
// Start session for CSRF protection
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

// Generate CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
	$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Set secure headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
header('Content-Security-Policy: default-src \'self\'; script-src \'self\' https://cdn.jsdelivr.net; style-src \'self\' https://cdn.jsdelivr.net');
?><!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>App Send Mail</title>

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

</head>

<body>

	<div class="container">

		<div class="py-3 text-center">
			<img class="d-block mx-auto mb-2" src="logo.png" alt="Logo" width="72" height="72">
			<h2>Send Mail</h2>
			<p class="lead">Secure Mail Service</p>
		</div>

		<div class="row">
			<div class="col-md-12">

				<div class="card-body font-weight-bold">
					<form action="processa_envio.php" method="post" novalidate>
						<!-- CSRF Token for security -->
						<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
						
						<div class="form-group mb-2">
							<label for="to">To</label>
							<input name="to" type="email" class="form-control" id="to" placeholder="joao@dominio.com" required maxlength="255">
							<small class="form-text text-muted">Enter a valid email address</small>
						</div>

						<div class="form-group mb-2">
							<label for="subject">Subject</label>
							<input name="subject" type="text" class="form-control" id="subject" placeholder="Subject of e-mail" required maxlength="255">
						</div>

						<div class="form-group mb-5">
							<label for="message">Message</label>
							<textarea name="message" class="form-control" id="message" rows="8" required maxlength="10000"></textarea>
							<small class="form-text text-muted">Maximum 10,000 characters</small>
						</div>

						<button type="submit" class="btn btn-primary btn-lg">Submit Message</button>
					</form>
				</div>
			</div>
		</div>
	</div>

</body>

</html>