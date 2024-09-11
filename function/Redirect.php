<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

https://github.com/Friedinger/FileRouter

by Friedinger (friedinger.org)

*/

namespace FileRouter;


class Redirect extends \Exception
{
	public function __construct(string $uri, bool $permanent = false)
	{
		if (empty($uri)) {
			throw new \InvalidArgumentException("URI cannot be empty");
		}
		$response_code = $permanent ? 301 : 302; // Set response code
		header("Location: $uri", true, $response_code); // Set location header
	}
}
