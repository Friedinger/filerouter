<?php

spl_autoload_register(function ($class) {
	if (str_starts_with($class, __NAMESPACE__ . "\\")) {
		$class = str_replace(__NAMESPACE__ . "\\", "", $class);
		require_once("{$class}.php");
	} else require_once("{$class}.php");
});

session_start();
(new Router())->route($_SERVER["REQUEST_URI"]);
