<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

https://github.com/Friedinger/FileRouter

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

		$message = str_replace("\n", "\n\t", $message);
		$message = str_replace("\r", "", $message);
		$log = "[$date] $message" . PHP_EOL;

		file_put_contents($file, $log, FILE_APPEND);
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
		$file = $_SERVER["DOCUMENT_ROOT"] . $path;
		if (file_exists($file) && filesize($file) >= Config::LOG_MAX_FILE_SIZE) {
			$file = self::getNewLogFile($file);
		}
		return $file;
	}

	private static function getNewLogFile($file)
	{
		$logDir = dirname($file);
		$baseName = basename($file, '.log');
		$index = 1;

		do {
			$newFile = $logDir . DIRECTORY_SEPARATOR . $baseName . '_' . $index . '.log';
			$index++;
		} while (file_exists($newFile));

		return $newFile;
	}
}
