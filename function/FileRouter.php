<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

Version: 2.2.2

*/

namespace FileRouter;

class FileRouter
{
	public function __construct()
	{
		try {
			$this->autoload(); // Autoload classes
			$this->setup(); // Setup environment
			$this->handle(); // Handle request
		} catch (ErrorPage $errorPage) {
			throw $errorPage; // Rethrow ErrorPage exceptions to display error page
		} catch (\Throwable $exception) {
			$this->handleException($exception); // Handle unhandled exceptions
		}
	}

	private function handle()
	{
		// Handle route file as proxy of request
		$proxy = new Proxy();
		$proxyHandled = $proxy->handle();
		if ($proxyHandled) exit; // Stop handling if request was handled by proxy

		// Handle request with router
		$router = new Router();
		$routerHandled = $router->handle();
		if ($routerHandled) exit; // Stop handling if request was handled by router

		// Error 404 if request was not handled before
		throw new ErrorPage(404);
	}

	private function setup()
	{
		// Start session if enabled in config
		if (Config::SESSION) {
			Misc::session();
		}

		// Set error log file if enabled in config
		if (Config::LOG) {
			$logFile = str_replace("{date}", date("Y-m-d"), Config::LOG_FILE);
			ini_set("error_log", $_SERVER["DOCUMENT_ROOT"] . Config::LOG_PATH . $logFile);
		}
	}

	private function handleException(\Throwable $exception)
	{
		try {
			ob_end_clean(); // Clear output buffer
			Misc::log("{$exception->getMessage()} in {$exception->getFile()}({$exception->getLine()})", E_USER_ERROR); // Log unhandled exceptions
			throw new ErrorPage(500); // Internal server error if unhandled exception occurred
		} catch (\Throwable $e) {
			if (Config::LOG) {
				error_log("ERROR {$e->getMessage()} in exception handling"); // Log error message
			}
			die(Config::ERROR_FATAL ?? "<h1>Error</h1><p>An error occurred in the request.</p><p>Please contact the webmaster</p>");
		}
	}

	private function autoload()
	{
		spl_autoload_register(function ($class) {
			if (str_starts_with($class, __NAMESPACE__ . "\\")) {
				$class = str_replace(__NAMESPACE__ . "\\", "", $class);
				require_once "{$class}.php";
			}
		});
	}
}
