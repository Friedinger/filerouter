<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

*/

namespace FileRouter;

/**
 * Class Misc
 *
 * Contains miscellaneous utility functions.
 */
final class Misc
{
	/**
	 * Starts a session if one is not already started and returns the session ID.
	 * Session name and cookie parameters are set in the Config class.
	 *
	 * @return string|false The session ID if a session is started, false otherwise.
	 */
	public static function session(): string|false
	{
		if (session_id() == "") {
			session_set_cookie_params(Config::SESSION_COOKIE_PARAMS);
			session_name(Config::SESSION_NAME);
			session_start();
		}
		return session_id();
	}

	/**
	 * Retrieves the MIME type of a file.
	 * Uses mime_content_type as default, but also provides a custom list of MIME types based on file extension.
	 *
	 * @param string $filePath The path to the file.
	 * @return string|false The MIME type of the file if it can be determined, false otherwise.
	 */
	public static function getMime(string $filePath): string|false
	{
		$mimeTypes = [ // List of mime types depending on file extension
			"php" => "text/html",
			"html" => "text/html",
			"css" => "text/css",
			"js" => "application/x-javascript",
			"ico" => "image/x-icon",
			"vbs" => "application/x-vbs",
		];
		$extension = pathinfo($filePath, PATHINFO_EXTENSION); // Get file extension
		return $mimeTypes[$extension] ?? mime_content_type($filePath); // Choose mime type depending on file extension, php mime function as fallback
	}
}
