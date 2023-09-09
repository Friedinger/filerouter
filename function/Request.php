<?php

namespace FileRouter;

final class Request
{
	public static function uri(string $requestUri = null): string
	{
		$uri = $requestUri ?? filter_input(INPUT_SERVER, "REQUEST_URI", FILTER_SANITIZE_SPECIAL_CHARS);
		$uri = self::prepareRequest($uri);
		return htmlspecialchars($uri);
	}
	public static function responseCode(): int
	{
		return http_response_code();
	}
	public static function get(string $key): string|null
	{
		$get = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
		if (empty($get)) return null;
		return htmlspecialchars($get);
	}
	public static function post(string $key): string|null
	{
		$post = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
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
	public static function setSession(string $key, mixed $value): bool
	{
		$_SESSION[$key] = $value;
		return $_SESSION[$key] == $value;
	}
	public static function installPath(): string
	{
		return str_replace("\\", "", dirname($_SERVER["PHP_SELF"]) . "/");
	}
	private static function prepareRequest(string $requestUri): string
	{
		$request = htmlspecialchars(strtolower(urldecode($requestUri))); // Remove special chars from request
		$request = parse_url($request)["path"]; // Remove parameters
		$request = rtrim($request, "/") . "/"; // Force trailing slash
		$request = str_replace("/index.php/", "", $request); // Remove index.php
		$request = rtrim($request, "/"); // Remove trailing slash
		$request = ltrim($request, "/"); // Remove start slash
		return $request;
	}
}
