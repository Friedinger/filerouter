<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

*/

namespace FileRouter;

final class Misc
{
	public static function session(): string|false
	{
		if (session_id() == "") {
			session_set_cookie_params(Config::SESSION_COOKIE_PARAMS);
			session_name(Config::SESSION_NAME);
			session_start();
		}
		return session_id();
	}

	public static function getMime(string $filePath): string|false
	{
		$mimeTypes = array( // List of mime types depending on file extension
			"php" => "text/html",
			"html" => "text/html",
			"css" => "text/css",
			"js" => "application/x-javascript",
			"ico" => "image/x-icon",
			"vbs" => "application/x-vbs",
		);
		$extension = pathinfo($filePath, PATHINFO_EXTENSION); // Get file extension
		return $mimeTypes[$extension] ?? mime_content_type($filePath); // Chose mime type depending on file extension, php mime function as fallback
	}
}
