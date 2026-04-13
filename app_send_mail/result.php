<?php
// Start session for secure message display
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

// Set secure headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
header('Content-Security-Policy: default-src \'self\'; script-src \'self\' https://cdn.jsdelivr.net; style-src \'self\' https://cdn.jsdelivr.net');

// Get status from query parameter with validation
$status = isset($_GET['status']) && in_array($_GET['status'], ['success', 'error', 'invalid']) ? $_GET['status'] : 'error';

// Get message from session (server-side storage, more secure)
$message = '';
if ($status === 'success' && isset($_SESSION['success_message'])) {
	$message = $_SESSION['success_message'];
	unset($_SESSION['success_message']); // Clear after displaying
} elseif ($status === 'error' && isset($_SESSION['error_message'])) {
	$message = $_SESSION['error_message'];
	unset($_SESSION['error_message']); // Clear after displaying
} elseif ($status === 'invalid') {
	$message = 'The message data was invalid. Please check your input and try again.';
}
?><!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Email Result</title>

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

				<?php if ($status === 'success') { ?>
					<div class="container mt-5">
						<h1 class="display-4 text-success">✓ Success</h1>
						<p class="lead"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
						<a href="index.php" class="btn btn-success btn-lg mt-5">Send Another Email</a>
					</div>
				<?php } elseif ($status === 'error') { ?>
					<div class="container mt-5">
						<h1 class="display-4 text-danger">✗ Error</h1>
						<p class="lead"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
						<a href="index.php" class="btn btn-danger btn-lg mt-5">Try Again</a>
					</div>
				<?php } else { ?>
					<div class="container mt-5">
						<h1 class="display-4 text-warning">⚠ Invalid Input</h1>
						<p class="lead"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
						<a href="index.php" class="btn btn-warning btn-lg mt-5">Go Back</a>
					</div>
				<?php } ?>

			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP28nf+0dXQwlrqXuXvQQAwKBHnGfBc" crossorigin="anonymous"></script>
</body>

</html>
