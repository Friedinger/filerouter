<?php

namespace FileRouter;

final class Modules
{
	public static function head(string $title, string $additionalCss = ""): void
	{
		Output::$title = $title;
		Output::$additionalCss = $additionalCss;
		require($_SERVER["DOCUMENT_ROOT"] . Config::PATH_MODULES . "head.php");
	}
	public static function header(): void
	{
		require($_SERVER["DOCUMENT_ROOT"] . Config::PATH_MODULES . "header.php");
	}
	public static function footer(): void
	{
		require($_SERVER["DOCUMENT_ROOT"] . Config::PATH_MODULES . "footer.php");
	}
	public static function pathPages(): string
	{
		return $_SERVER["DOCUMENT_ROOT"] . Config::PATH_PUBLIC;
	}
}
