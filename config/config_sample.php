<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

Version: 2.0

*/

namespace FileRouter;

final class Config
{
	//All paths are relative to the server root
	const PATH_PUBLIC = "/../public/"; // Path to the public folder
	const PATH_MODULES = "/../modules/"; // Path to the modules folder
	const PATH_HEAD = "/../modules/head.php"; // Path to the head file
	const PATH_HEADER = "/../modules/header.php"; // Path to the header file
	const PATH_FOOTER = "/../modules/footer.php"; // Path to the footer file
	const PATH_ERROR = "/../modules/error.php"; // Path to the error file
	const ALLOW_PAGE_PHP = true; // Allow to execute php code in pages. Warning: This can be a security risk if not handled carefully.
	const SESSION_NAME = "FileRouter";
	const ERROR_FATAL = "<h1>Error</h1><p>An error occurred in the request.</p><p>Please contact the webmaster</p>";
	const ERROR_MESSAGE = [
		400 => "Bad Request",
		401 => "Unauthorized",
		403 => "Forbidden",
		404 => "Not Found",
		405 => "Method Not Allowed",
		500 => "Internal Server Error",
		"default" => "Error",
	];

	const TITLE_PREFIX = "FileRouter";
	const TITLE_SUFFIX = "";
	const TITLE_SEPARATOR = " | ";
}
