<html>

<head>
	<meta charset="utf-8" />
	<title>App Send Mail</title>

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

</head>

<body>

	<div class="container">

		<div class="py-3 text-center">
			<img class="d-block mx-auto mb-2" src="logo.png" alt="" width="72" height="72">
			<h2>Send Mail</h2>
			<p class="lead">Private Mail Service</p>
		</div>

		<div class="row">
			<div class="col-md-12">

				<div class="card-body font-weight-bold">
					<form action="processa_envio.php" method="post">
						<div class="form-group mb-2">
							<label for="to">To</label>
							<input name="to" type="text" class="form-control" id="to" placeholder="joao@dominio.com">
						</div>

						<div class="form-group mb-2">
							<label for="subject">Subject</label>
							<input name="subject" type="text" class="form-control" id="subject" placeholder="Subject of e-mail">
						</div>

						<div class="form-group mb-5">
							<label for="message">Message</label>
							<textarea name="message" class="form-control" id="message"></textarea>
						</div>

						<button type="submit" class="btn btn-primary btn-lg">Submit Message</button>
					</form>
				</div>
			</div>
		</div>
	</div>

</body>

</html>