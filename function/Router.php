<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

*/

namespace FileRouter;

final class Router
{
	public bool $handled = false;
	public function handle($path): void
	{
		if (is_dir($path)) $path = rtrim($path, "/") . "/index.php";
		$directory = dirname($path);
		$directoryContent = glob("$directory/*");
		$directoryPosition = array_search(strtolower($path), array_map("strtolower", $directoryContent));
		if ($directoryPosition === false) {
			(new Error(404))->handle();
			return;
		}
		$path = $directoryContent[$directoryPosition];

		$mime = Misc::getMime($path);
		header("Content-Type: " . $mime . "; charset=utf-8");

		if ($mime == "text/html") {
			$this->handled = ControllerHtml::redirect($path);
			if ($this->handled) return;
		}
		if (str_starts_with($mime, "image/")) {
			$this->handled = ControllerImage::redirect($path);
			if ($this->handled) return;
		}
		$this->handled = ControllerDefault::redirect($path);
	}
}
