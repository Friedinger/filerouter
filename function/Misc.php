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
	 * Generates a CSRF token and stores it in the session.
	 * The token is a 64-character hexadecimal string.
	 *
	 * @return string The generated CSRF token.
	 */
	public static function generateCSRFToken(): string
	{
		$token = bin2hex(random_bytes(32));
		Request::setSession($token, "token");
		return $token;
	}

	/**
	 * Verifies a CSRF token by comparing it to the token stored in the session.
	 *
	 * @param string $token The token to verify.
	 * @return bool True if the token is valid, false otherwise.
	 */
	public static function verifyCSRFToken(string $token): bool
	{
		$verify = hash_equals(Request::session("token"), $token);
		self::generateCSRFToken();
		return $verify;
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

	/**
	 * Prepare URI by removing special characters, parameters, trailing slash, index.php and start slash.
	 *
	 * @param string $uri The URI to prepare.
	 * @return string The prepared URI.
	 */
	public static function prepareUri(string $uri): string
	{
		$uri = htmlspecialchars(strtolower(urldecode($uri))); // Remove special chars from request
		$uri = parse_url($uri, PHP_URL_PATH); // Remove parameters
		$uri = rtrim($uri, "/") . "/"; // Force trailing slash
		$uri = "/" . ltrim($uri, "/"); // Force start slash
		$uri = str_replace("/index.php/", "", $uri); // Remove index.php
		$uri = rtrim($uri, "/"); // Remove trailing slash
		$uri = ltrim($uri, "/"); // Remove start slash
		return htmlspecialchars($uri); // Return cleaned URI
	}
}
