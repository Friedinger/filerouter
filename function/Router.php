<?php

namespace FileRouter;

final class Router
{
	private string $path;
	private string $file;
	public function route(string $uri): bool
	{
		$this->path = $_SERVER["DOCUMENT_ROOT"] . Config::PATH_PUBLIC . Request::uri($uri);
		$routeFile = $this->getRouteFile();
		if ($routeFile == $this->path) $this->error(404);
		if ($routeFile) {
			$routeFileResponse = include $routeFile;
			if (!$routeFileResponse) return false;
			if (is_int($routeFileResponse)) $this->error($routeFileResponse);
		}
		if (Request::responseCode() != 200) $this->error(Request::responseCode());
		$this->redirect();
		return true;
	}
	public function rewrite(string $uri): void
	{
		$uri = htmlspecialchars($uri);
		header("Location: " . $uri);
	}
	public function error(int $code): void
	{
		$pathErrorPage = $_SERVER["DOCUMENT_ROOT"] . Config::PATH_ERROR;
		http_response_code($code);
		header("Content-Type: text/html");
		if (!file_exists($pathErrorPage)) die("<h1>Error</h1><p>An error occurred in the request.</p><p>Please contact the webmaster: info[at]friedinger.org</p>");
		Output::$status = htmlspecialchars($code);
		require($pathErrorPage);
		exit;
	}
	private function redirect(): void
	{
		$this->file = $this->path;
		if (is_dir($this->path)) $this->file = rtrim($this->path, "/") . "/index.php";
		if (!file_exists($this->file)) $this->error(404);
		$mime = $this->getMime();
		header("Content-Type: " . $mime . "; charset=utf-8");
		if (str_starts_with($mime, "image/") && class_exists(__NAMESPACE__ . "\Image")) {
			$imageHandle = (new Image())->handle($this->file, $mime);
			if ($imageHandle) return;
		}
		if ($mime == "text/html") {
			require($this->file);
		} else {
			readfile($this->file);
		}
	}
	private function getRouteFile(): string|false
	{
		$path = $this->path;
		for ($i = 0; $i < 100; $i++) {
			$file = $path . "/_route.php";
			if (file_exists($file)) return $file;
			if ($path == $_SERVER["DOCUMENT_ROOT"] . Config::PATH_PUBLIC) break;
			$path = dirname($path) . "/";
		}
		return false;
	}
	private function getMime(): string|false
	{
		$mimeTypes = array( // List of mime types depending on file extension
			"php" => "text/html",
			"html" => "text/html",
			"css" => "text/css",
			"js" => "application/x-javascript",
			"ico" => "image/x-icon",
			"vbs" => "application/x-vbs",
		);
		$extension = pathinfo($this->file, PATHINFO_EXTENSION); // Get file extension
		return $mimeTypes[$extension] ?? mime_content_type($this->file); // Chose mime type depending on file extension, php mime function as fallback
	}
}
