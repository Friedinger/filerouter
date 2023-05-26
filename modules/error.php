<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="de">

<?php Modules::head("Error " . Output::$status) ?>

<body>
	<?php Modules::header() ?>
	<main>
		<?php
		switch (Output::$status) {
			case "403":
				print("<h1>Zugriff verweigert</h1>");
				print("<p>Error 403</p>");
				break;
			case "404":
				print("<h1>Die Seite wurde nicht gefunden</h1>");
				print("<p>Error 404</p>");
				break;
			default:
				print("<h1>Error " . Output::$status . "</h1>");
				break;
		}
		?>
	</main>
	<?php Modules::footer() ?>
</body>

</html>