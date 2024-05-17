<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

*/

namespace FileRouter;

class Proxy
{
	public bool $handled = false;
	private string $path;

	public function __construct($requestedPath)
	{
		$this->path = $requestedPath;
	}

	public function handle(): bool
	{
		$routeFile = $this->getRouteFile($this->path);
		if (!$routeFile) {
			return $this->handled(false);
		}
		ob_start();
		$routeFileResponse = include $routeFile;
		ob_get_clean();
		if (!$routeFileResponse) {
			return $this->handled(false);
		}
		if (is_bool($routeFileResponse)) {
			return $this->handled($routeFileResponse);
		}
		if ($routeFileResponse instanceof Error) {
			$routeFileResponse->handle();
			return $this->handled(true);
		}
		if (is_callable($routeFileResponse)) {
			$routeFileResponse();
			return $this->handled(false);
		}
		return $this->handled(false);
	}

	private function handled(bool $state): bool
	{
		$this->handled = $state;
		return $state;
	}

	private function getRouteFile(string $path): string|false
	{
		for ($iteration = 0; $iteration < 100; $iteration++) {
			$file = $path . "/_route.php";
			if (file_exists($file)) return $file;
			if ($path == $_SERVER["DOCUMENT_ROOT"] . Config::PATH_PUBLIC) break;
			$path = dirname($path) . "/";
		}
		return false;
	}
}
