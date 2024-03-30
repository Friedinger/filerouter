<!DOCTYPE html>
<html lang="de">

<head>
	<?php FileRouter\Modules::head("") ?>

<body>
	<?php FileRouter\Modules::load("header") ?>
	<main>
		<p>Everything inside this folder is protected</p>
	</main>
	<?php FileRouter\Modules::load("footer") ?>
</body>

</html>