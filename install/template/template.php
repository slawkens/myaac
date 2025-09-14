<?php defined('MYAAC') or die('Direct access not allowed!'); ?>
<!DOCTYPE html>
<html dir="<?php echo $locale['direction']; ?>" lang="<?php echo $locale['lang']; ?>" xml:lang="<?php echo $locale['lang']; ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $locale['encoding']; ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>MyAAC - <?php echo $locale['installation']; ?></title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="template/style.css" />
	<script type="text/javascript" src="<?php echo BASE_URL; ?>tools/ext/jquery/jquery.min.js"></script>
</head>
<body>

	<div id="body" class="container">

		<header id="header" class="pt-5 pb-4 pb-sm-5">
			<h1>MyAAC <?php echo $locale['installation']; ?></h1>
		</header>

		<div class="row">

			<div id="sidebar" class="col-md-3">
				<h3><?php echo $locale['steps']; ?></h3>
				<ul class="list-group mt-4">
					<?php
						$i = 0;
						foreach($steps as $key => $value){

							if ($step == $value) {
								$progress = ($i == 6) ? 100 : $i * 16;
							}

							echo '<li class="list-group-item' . ($step == $value ? ' active' : '') . '">' . ++$i . '. ' . $locale['step_' . $value] . '</li>';
						}

					?>
				</ul>
			</div>

			<div id="content" class="col-md-9">

				<?php
					if(isset($locale['step_' . $step . '_title']))
						echo '<h3 class="mb-4 mt-4 mt-md-0">' . $locale['step_' . $step . '_title'] . '</h3>';
					else
						echo '<h3 class="mb-4 mt-4 mt-md-0">' . $locale['step_' . $step] . '</h3>';
				?>

				<?php
				if(!isset($config['installed'])):
				?>
				<div class="row">
					<div class="col-md-12">
						<div class="progress mb-2">
							<div class="progress-bar progress-bar-striped progress-bar-animated" style="width: <?php echo $progress; ?>%" role="progressbar" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
					</div>
				</div>
				<?php endif; ?>

				<?php echo $content; ?>

			</div>

		</div>

		<hr />

	</div>

	<footer id="footer" class="p-4">
		<p style="text-align: center;"><?php echo base64_decode('UG93ZXJlZCBieSA8YSBocmVmPSJodHRwOi8vbXktYWFjLm9yZyIgdGFyZ2V0PSJfYmxhbmsiPk15QUFDLjwvYT4='); ?></p>
	</footer>
</body>
</html>
