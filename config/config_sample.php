<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

https://github.com/Friedinger/FileRouter

by Friedinger (friedinger.org)

Version: 3.3.0

*/

namespace FileRouter;

class Config
{
	// Paths (relative to the server root)
	const PATH_PUBLIC = "/../public/"; // Path to the public folder
	const PATH_MODULES = "/../modules/"; // Path to the modules folder
	const PATH_HEAD = "/../modules/head.php"; // Path to the head file
	const PATH_HEADER = "/../modules/header.php"; // Path to the header file
	const PATH_FOOTER = "/../modules/footer.php"; // Path to the footer file
	const PATH_ERROR = "/../modules/error.php"; // Path to the error file

	// Sessions
	const SESSION = true; // Enable session handling
	const SESSION_NAME = "FileRouter"; // Name of the session
	const SESSION_COOKIE_PARAMS = [ // Session cookie parameters (https://www.php.net/manual/en/function.session-set-cookie-params.php)
		"lifetime" => 0,
		"path" => "/",
		"domain" => "filerouter.home.localhost",
		"secure" => false,
		"httponly" => true,
		"samesite" => "Strict",
	];

	// Security
	const CSRF_TEMPLATE = "csrf-token"; // CSRF token template variable name, also used as session key
	const CSRF_PARAMETER = "token"; // CSRF token parameter name for get and post requests
	const CSRF_LENGTH = 64; // Length of the CSRF token, set to 0 to disable CSRF protection

	// Page titles
	const TITLE_PREFIX = "FileRouter";
	const TITLE_SUFFIX = "";
	const TITLE_SEPARATOR = " | ";

	// Error handling and logging
	const DEBUG = true; // Enable debug mode (shows errors and warnings, disables error logging)
	const LOG = true; // Enable logging
	const LOG_MAX_FILE_SIZE = 1048576; // Maximum file size of log files before a new file is created (in bytes)
	const LOG_PATH = [ // Paths to log files (relative to the server root, {date} will be replaced with the current date)
		"error" => "/../logs/error_{date}.log", // Error log file required if logging is enabled
		"additional" => "/../logs/additional.log", // Additional log files, can be used for custom logging by Logger::log("message", "additional")
	];

	// Other
	const ALLOW_PAGE_PHP = true; // Allow to execute php code in pages. Warning: This can be a security risk if not handled carefully.
	const IMAGE_RESIZE_QUERY = "res"; // Query parameter to specify the width of an image to resize it
	const ERROR_FATAL = "<h1>Error</h1><p>An error occurred in the request.</p><p>Please contact the webmaster</p>"; // Fatal error message if error page can't be loaded
}
