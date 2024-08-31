<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

*/

namespace FileRouter;

/**
 * Class Router
 *
 * Contains functions to redirect request to correct file and controller.
 */
class Router
{
	/**
	 * Redirects request to correct file and controller.
	 *
	 * @param string $path Path of requested file.
	 * @return bool True if request was handled, false if not.
	 */
	public static function handle(string $uri = null): bool
	{
		if ($uri == null) {
			$uri = Request::uri(); // Get request uri if not set
		} else {
			$uri = Misc::prepareUri($uri); // Prepare uri
		}

		$path = self::searchPath($uri); // Search for path in public directory

		// Get mime type and set it as header content type
		$mime = Misc::getMime($path);
		header("Content-Type: {$mime}; charset=utf-8");

		// Handle different mime types
		if ($mime == "text/html") {
			$handled = ControllerHtml::handle($path);
			if ($handled) return true;
		}
		if (str_starts_with($mime, "image/")) {
			$handled = ControllerImage::handle($path);
			if ($handled) return true;
		}
		return ControllerDefault::handle($path);
	}

	/**
	 * Redirects to specified URI.
	 *
	 * @param string $uri URI to redirect to.
	 */
	public static function redirect(string $uri): void
	{
		$uri = Misc::prepareUri($uri); // Prepare uri
		header("Location: /$uri/"); // Set location header
		exit(); // Stop further execution
	}

	private static function searchPath(string $uri): string
	{
		$path = $_SERVER["DOCUMENT_ROOT"] . Config::PATH_PUBLIC . $uri; // Combine document root, public path and URI to get file path

		if (is_dir($path)) $path = rtrim($path, "/") . "/index.php"; // If path is a directory, add index.php

		$directory = dirname($path); // Get directory of path
		$directoryContent = glob("$directory/*"); // Get content of directory as array
		$directoryPosition = array_search(strtolower($path), array_map("strtolower", $directoryContent)); // Search for path in directory content (case insensitive)
		if ($directoryPosition === false) {
			// If path is not in directory content, return 404 error
			throw new ErrorPage(404);
		}

		return $directoryContent[$directoryPosition]; // Get path from directory content
	}
}
