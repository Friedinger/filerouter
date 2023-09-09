<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="de">

<?php FileRouter\Modules::head("Error " . FileRouter\Output::$status) ?>

<body>
	<?php FileRouter\Modules::header() ?>
	<main>
		<?php
		print match (FileRouter\Output::$status) {
			"403" => "<h1>Zugriff verweigert</h1><p>Error 403</p>",
			"404" => "<h1>Die Seite wurde nicht gefunden</h1><p>Error 404</p>",
			default => "<h1>Error " . FileRouter\Output::$status . "</h1>",
		};
		?>
	</main>
	<?php FileRouter\Modules::footer() ?>
</body>

</html>