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
	public function handle(string $path): bool
	{
		$path = $this->searchPath($path);

		// Get mime type and set it as header content type
		$mime = Misc::getMime($path);
		header("Content-Type: {$mime}; charset=utf-8");

		// Handle different mime types
		if ($mime == "text/html") {
			$handled = ControllerHtml::redirect($path);
			if ($handled) return true;
		}
		if (str_starts_with($mime, "image/")) {
			$handled = ControllerImage::redirect($path);
			if ($handled) return true;
		}
		return ControllerDefault::redirect($path);
	}

	private function searchPath(string $path): string
	{
		if (is_dir($path)) $path = rtrim($path, "/") . "/index.php"; // If path is a directory, add index.php

		$directory = dirname($path); // Get directory of path
		$directoryContent = glob("$directory/*"); // Get content of directory as array
		$directoryPosition = array_search(strtolower($path), array_map("strtolower", $directoryContent)); // Search for path in directory content (case insensitive)
		if ($directoryPosition === false) {
			// If path is not in directory content, return 404 error
			(new Error(404))->handle();
			exit;
		}

		return $directoryContent[$directoryPosition]; // Get path from directory content
	}
}
