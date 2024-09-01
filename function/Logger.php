<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

*/


namespace FileRouter;

class Logger
{
	public static function log(string $message, string $type): void
	{
		if (!Config::LOG) return;

		$date = date("Y-m-d H:i:s");
		$file = self::logPath($type);
		$logMessage = "[$date] $message" . PHP_EOL;
		file_put_contents($file, $logMessage, FILE_APPEND);
	}

	public static function logError(string $message, int $level): void
	{
		if (!Config::LOG) return;

		$levelName = match ($level) {
			E_NOTICE => "NOTICE",
			E_USER_NOTICE => "NOTICE",
			E_WARNING => "WARNING",
			E_USER_DEPRECATED => "DEPRECATED",
			E_DEPRECATED => "DEPRECATED",
			E_USER_WARNING => "WARNING",
			E_ERROR => "ERROR",
			E_USER_ERROR => "ERROR",
			default => "UNKNOWN",
		};
		error_log("$levelName: $message");
	}

	public static function logPath(string $type): string
	{
		$path = Config::LOG_PATH[$type] ?? "filerouter_{date}.log";
		$path = str_replace("{date}", date("Y-m-d"), $path);
		return $_SERVER["DOCUMENT_ROOT"] . $path;
	}
}
