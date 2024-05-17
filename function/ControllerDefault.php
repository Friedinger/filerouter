<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

*/

namespace FileRouter;

class ControllerDefault
{
	public static function redirect(string $filePath): bool
	{
		return (bool) readfile($filePath);
	}
}
