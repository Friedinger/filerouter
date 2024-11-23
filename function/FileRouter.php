<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

https://github.com/Friedinger/FileRouter

by Friedinger (friedinger.org)

Version: 3.3.1

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
		} catch (ErrorPage) {
			// Error page was displayed in ErrorPage exception
		} catch (Redirect) {
			// Redirect was handled in Redirect exception
		} catch (\Throwable $exception) {
			$this->handleException($exception); // Handle unhandled exceptions
		}
	}

	private function handle(): void
	{
		// Handle route file as proxy of request
		$proxy = new Proxy();
		$proxyHandled = $proxy->handle();
		if ($proxyHandled) return; // Stop handling if request was handled by proxy

		// Handle request with router
		$router = new Router();
		$routerHandled = $router->handle();
		if ($routerHandled) return; // Stop handling if request was handled by router

		// Error 404 if request was not handled before
		throw new ErrorPage(404);
	}

	private function setup(): void
	{
		// Set error log file if enabled
		if (Config::LOG) {
			ini_set("log_errors", 1); // Enable error logging
			ini_set("error_log", Logger::logPath("error")); // Set error log file
		}

		// Set error reporting and display errors
		if (Config::DEBUG) {
			error_reporting(E_ALL); // Report all errors
			ini_set("display_errors", 1); // Display errors
			ini_set("log_errors", 0); // Disable error log
		} else {
			error_reporting(0); // Report no errors
			ini_set("display_errors", 0); // Hide errors
		}

		// Start session if enabled in config
		if (Config::SESSION) {
			Misc::session();
		}
	}

	private function handleException(\Throwable $exception): void
	{
		if (CONFIG::DEBUG) {
			throw $exception; // Rethrow exception if debug mode is enabled
		}
		try {
			ob_end_clean(); // Clear output buffer
			Logger::logError("{$exception->getMessage()} in {$exception->getFile()}({$exception->getLine()})", E_USER_ERROR); // Log unhandled exceptions
			throw new ErrorPage(500); // Internal server error if unhandled exception occurred
		} catch (\Throwable $e) {
			if ($e instanceof ErrorPage) return;
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
