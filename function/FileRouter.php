<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

Version: 2.1

*/

namespace FileRouter;

// Autoload classes
spl_autoload_register(function ($class) {
	if (str_starts_with($class, __NAMESPACE__ . "\\")) {
		$class = str_replace(__NAMESPACE__ . "\\", "", $class);
		require_once "{$class}.php";
	}
});

// Start session if enabled in config
if (Config::SESSION) {
	Misc::session();
}

// Handle route file as proxy of request
$proxy = new Proxy();
$proxyHandled = $proxy->handle(Request::filePath());
if ($proxyHandled) exit; // Stop handling if request was handled by proxy

// Handle request with router
$router = new Router();
$routerHandled = $router->handle(Request::filePath());
if ($routerHandled) exit; // Stop handling if request was handled by router

// Error 404 if request was not handled before
(new Error(404))->handle();
