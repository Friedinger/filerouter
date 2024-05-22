<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

Version: 2.0

*/

namespace FileRouter;

spl_autoload_register(function ($class) {
	if (str_starts_with($class, __NAMESPACE__ . "\\")) {
		$class = str_replace(__NAMESPACE__ . "\\", "", $class);
		require_once("{$class}.php");
	}
});

if (Config::SESSION) {
	Misc::session();
}

$proxy = new Proxy(Request::filePath());
$proxy->handle();
if ($proxy->handled) {
	return;
}

$router = new Router();
$router->handle(Request::filePath());
if ($router->handled) {
	return;
}

(new Error(404))->handle();
