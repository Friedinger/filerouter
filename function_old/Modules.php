<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

*/

namespace FileRouter;

final class Modules
{
	public static function head(string $title, string $additionalCss = ""): void
	{
		Output::set("title", $title);
		Output::set("additionalCss", $additionalCss);
		require($_SERVER["DOCUMENT_ROOT"] . Config::PATH_MODULES . "head.php");
	}
	public static function load(string $module): void
	{
		require($_SERVER["DOCUMENT_ROOT"] . Config::PATH_MODULES . $module . ".php");
	}
}
