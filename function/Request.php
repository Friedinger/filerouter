<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

*/

namespace FileRouter;

final class Request
{
	public static function uri(): string
	{
		$uri = $_SERVER["REQUEST_URI"];
		$uri = htmlspecialchars(strtolower(urldecode($uri))); // Remove special chars from request
		$uri = parse_url($uri, PHP_URL_PATH); // Remove parameters
		$uri = rtrim($uri, "/") . "/"; // Force trailing slash
		$uri = "/" . ltrim($uri, "/"); // Force start slash
		$uri = str_replace("/index.php/", "", $uri); // Remove index.php
		$uri = rtrim($uri, "/"); // Remove trailing slash
		$uri = ltrim($uri, "/"); // Remove start slash
		return htmlspecialchars($uri);
	}

	public static function filePath(): string
	{
		return $_SERVER["DOCUMENT_ROOT"] . Config::PATH_PUBLIC . Request::uri();
	}

	public static function get(string $key): string|null
	{
		$get = $_GET[$key] ?? null;
		if (empty($get)) return null;
		return htmlspecialchars($get);
	}

	public static function post(string $key): string|null
	{
		$post = $_POST[$key] ?? null;
		if (empty($post)) return null;
		return htmlspecialchars($post);
	}

	public static function file(string ...$keys): mixed
	{
		$file = $_FILES;
		foreach ($keys as $key) {
			if (!isset($file[$key])) return null;
			$file = $file[$key];
		}
		return $file;
	}

	public static function session(string ...$keys): mixed
	{
		$session = $_SESSION;
		foreach ($keys as $key) {
			if (!isset($session[$key])) return null;
			$session = $session[$key];
		}
		return $session;
	}

	public static function setSession(mixed $value, string ...$keys): bool
	{
		return self::setNestedSession($_SESSION, $keys, $value);
	}

	private static function setNestedSession(array &$session, array $keys, mixed $value): bool
	{
		$key = array_shift($keys);
		if (empty($keys)) {
			$session[$key] = $value;
			return $session[$key] === $value;
		} else {
			if (!isset($session[$key]) || !is_array($session[$key])) {
				$session[$key] = [];
			}
			return self::setNestedSession($session[$key], $keys, $value);
		}
	}

	public static function responseCode(): int
	{
		return http_response_code();
	}
}
