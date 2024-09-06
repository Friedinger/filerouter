<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

https://github.com/Friedinger/FileRouter

by Friedinger (friedinger.org)

*/

namespace FileRouter;

/**
 * Class ControllerDefault
 *
 * Responsible for handling all other file types.
 */
class ControllerDefault
{
	/**
	 * Outputs the content of the specified file to the browser.
	 *
	 * @param string $filePath The path of the file to output.
	 * @return bool Returns true if the file was successfully read and displayed, false otherwise.
	 */
	public static function handle(string $filePath): bool
	{
		return (bool) readfile($filePath);
	}
}
