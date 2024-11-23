<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

https://github.com/Friedinger/FileRouter

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
	private static mixed $handleCustom;

	/**
	 * Loads and processes route file.
	 * Determines based on route file response if request handling should continue or not.
	 *
	 * @return bool True if request handling should continue, false if not.
	 */
	public function handle(string $uri = null): bool
	{
		if ($uri == null) {
			$uri = Request::uri(); // Get request uri if not set
		} else {
			$uri = Misc::prepareUri($uri); // Prepare uri
		}

		$routeFile = $this->getRouteFile($uri); // Get route file
		if (!$routeFile) return false; // No handling if no route file

		$routeFileResponse = include $routeFile; // Process route file

		if (!isset($routeFileResponse)) return false; // No handling if no route file response

		if (is_bool($routeFileResponse)) {
			return $routeFileResponse; // Return route file response if boolean to continue handling request or not
		}

		if (is_callable($routeFileResponse)) {
			self::$handleCustom = $routeFileResponse; // Set custom route file callable and continue handling request
			return false;
		}

		return false; // Default continue handling request
	}

	/**
	 * Handles custom route file callable.
	 *
	 * @param Output $content Content to handle.
	 * @return Output Handled content.
	 */
	public static function handleCustom(Output $content, Output $settings): Output
	{
		// No handling if no custom route file callable set
		if (!isset(self::$handleCustom)) return $content;

		// Get parameters of custom route file callable
		$reflection = new \ReflectionFunction(self::$handleCustom);
		$parameters = array_map(function ($parameter) use ($content, $settings) {
			return match ($parameter->getName()) {
				"content" => $content,
				"settings" => $settings,
				default => null,
			};
		}, $reflection->getParameters());

		$return = call_user_func_array(self::$handleCustom, $parameters); // Call custom route file callable with parameters
		if ($return instanceof Output) $content = $return; // Set content to return value if output
		return $content; // Return handled content
	}

	private function getRouteFile(string $path): string|null
	{
		// Combine document root, public path and URI to get full path
		$path = rtrim($_SERVER["DOCUMENT_ROOT"] . Config::PATH_PUBLIC . $path, '/');

		while ($path != rtrim($_SERVER["DOCUMENT_ROOT"] . Config::PATH_PUBLIC, '/')) {
			$file = "{$path}/_route.php"; // Construct route file path
			if (file_exists($file)) {
				return $file; // Check if route file exists
			}
			$path = dirname($path); // Move up one directory
		}

		return null; // Return null if no route file found
	}
}
