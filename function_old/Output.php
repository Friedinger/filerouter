<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

*/

namespace FileRouter;

final class Output
{
	private static array $output = [];
	public static function setOutput()
	{
		self::set("user", Request::session("user") ?? null);
	}
	public static function set(string $key, mixed $value): void
	{
		self::$output[$key] = $value;
	}
	public static function get(string $key): mixed
	{
		return self::$output[$key] ?? null;
	}
}
