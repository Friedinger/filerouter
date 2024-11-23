<main>
	<h1>CSRF</h1>
	<p>Token should be used inside hidden input, text input just used for demo purpose.</p>

	<form method="post">
		<input type="text" name="token" value="<csrf-token />">
		<input type="submit">
	</form>
	<br>

	<?php

	use FileRouter\Misc;
	use FileRouter\Request;

	if ($_SERVER["REQUEST_METHOD"] === "POST") {
		echo "Send token: " . Request::post("token") . "<br>";
		echo "Expected token: " . Request::session("csrf-token") . "<br>";
		echo Misc::verifyCsrfTokenPost() ? "Valid" : "Invalid";
	}
	?>
</main>