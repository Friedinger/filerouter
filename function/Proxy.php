<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

*/

namespace FileRouter;

/**
 * Class Proxy
 *
 * Handles route file processing.
 */
class Proxy
{
	/**
	 * Loads and processes route file.
	 * Determines based on route file response if request handling should continue or not.
	 *
	 * @return bool True if request handling should continue, false if not.
	 */
	public function handle(string $path): bool
	{
		$routeFile = $this->getRouteFile($path); // Get route file
		if (!$routeFile) return false; // No handling if no route file

		ob_start();
		$routeFileResponse = include $routeFile; // Process route file
		ob_get_clean();

		if (!$routeFileResponse) return false; // No handling if no route file response

		if (is_bool($routeFileResponse)) {
			return $routeFileResponse; // Return route file response if boolean to continue handling request or not
		}

		if (is_callable($routeFileResponse)) {
			$routeFileResponse(); // Call route file response if callable
			return false;
		}

		return false; // Default continue handling request
	}

	private function getRouteFile(string $path): string|null
	{
		// Find route file in directory structure up to 100 directories deep
		for ($iteration = 0; $iteration < 100; $iteration++) {
			$file = "{$path}/_route.php"; // Construct route file path
			if (file_exists($file)) return $file; // Check if route file exists
			if ($path == $_SERVER["DOCUMENT_ROOT"] . Config::PATH_PUBLIC) break; // Stop if public path reached
			$path = dirname($path) . "/"; // Move up one directory
		}
		return null; // Return null if no route file found
	}
}
