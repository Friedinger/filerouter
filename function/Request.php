<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

*/

namespace FileRouter;

/**
 * Class Request
 *
 * Contains functions to get information and data from the current request.
 */
class Request
{
	/**
	 * Returns the URI of the current request.
	 * Removes special characters, parameters, trailing slash, index.php, and start slash.
	 *
	 * @return string The URI of the current request.
	 */
	public static function uri(): string
	{
		$uri = $_SERVER["REQUEST_URI"]; // Get request URI from server
		return Misc::prepareUri($uri); // Prepare URI
	}

	/**
	 * Returns the value of a GET parameter.
	 * If the value is empty, null is returned.
	 *
	 * @param string $key The key of the GET parameter.
	 * @return string|null The value of the GET parameter if it exists, null otherwise.
	 */
	public static function get(string $key): string|null
	{
		$value = $_GET[$key] ?? null; // Get value from GET array
		if (!is_null($value)) $value = htmlspecialchars($value); // Remove special chars from value if it exists
		return $value;
	}

	/**
	 * Returns the value of a POST parameter.
	 * If the value is empty, null is returned.
	 *
	 * @param string $key The key of the POST parameter.
	 * @return string|null The value of the POST parameter if it exists, null otherwise.
	 */
	public static function post(string $key): string|null
	{
		$value = $_POST[$key] ?? null; // Get value from POST array
		if (!is_null($value)) $value = htmlspecialchars($value); // Remove special chars from value if it exists
		return $value;
	}

	/**
	 * Returns a value in the $_FILES array.
	 * If the value is empty, null is returned.
	 *
	 * @param string ...$keys The keys to access the file value in the $_FILES array.
	 * @return mixed|null The value of the file if found, null otherwise.
	 */
	public static function file(string ...$keys): mixed
	{
		$file = $_FILES;
		foreach ($keys as $key) {
			if (!isset($file[$key])) return null; // Break if key does not exist
			$file = $file[$key]; // Get value from FILES array or go deeper
		}
		return $file;
	}

	/**
	 * Returns a value in the $_SESSION array.
	 * If the value is empty, null is returned.
	 *
	 * @param string ...$keys The keys to access the session value in the $_SESSION array.
	 * @return mixed|null The value of the session if found, null otherwise.
	 */
	public static function session(string ...$keys): mixed
	{
		$session = $_SESSION;
		foreach ($keys as $key) {
			if (!isset($session[$key])) return null; // Break if key does not exist
			$session = $session[$key]; // Get value from SESSION array or go deeper
		}
		return $session;
	}

	/**
	 * Sets a value in the $_SESSION array.
	 * Returns true if the value is set, false otherwise.
	 *
	 * @param mixed $value The value to set in the $_SESSION array.
	 * @param string ...$keys The keys to access the session value in the $_SESSION array.
	 * @return bool True if the value is set, false otherwise.
	 */
	public static function setSession(mixed $value, string ...$keys): bool
	{
		return self::setSessionNested($_SESSION, $keys, $value); // Call nested function to set session value
	}

	private static function setSessionNested(array &$session, array $keys, mixed $value): bool
	{
		$key = array_shift($keys); // Get next key
		if (empty($keys)) {
			$session[$key] = $value; // Set value if no more keys
			return $session[$key] === $value; // Return true if value is set
		} else {
			if (!isset($session[$key]) || !is_array($session[$key])) {
				$session[$key] = []; // Create array if key does not exist or is not an array
			}
			return self::setSessionNested($session[$key], $keys, $value); // Call nested function to set session value
		}
	}

	/**
	 * Retrieves the current HTTP response code.
	 *
	 * @return int The HTTP response code.
	 */
	public static function responseCode(): int
	{
		return http_response_code(); // Get HTTP response code with php function
	}
}
