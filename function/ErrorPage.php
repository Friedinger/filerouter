<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

https://github.com/Friedinger/FileRouter

by Friedinger (friedinger.org)

*/

namespace FileRouter;

/**
 * Class Error
 *
 * Represents an error with an error code and an error message.
 */
class ErrorPage extends \Exception
{

	/**
	 * Creates, handles and outputs new error with provided error code and error message.
	 *
	 * If no error message is provided, the default error message for the error code is used (set in error page).
	 * Handles the error by setting the HTTP status code, loading and replacing placeholders in the error page,
	 * handling HTML content, printing the output, and stopping further execution.
	 *
	 * @param int $errorCode The error code.
	 * @param string|null $errorMessage The error message (optional).
	 */
	public function __construct(int $errorCode, string $errorMessage = null)
	{
		http_response_code($errorCode); // Set HTTP status code based on error code

		$pathErrorPage = $_SERVER["DOCUMENT_ROOT"] . Config::PATH_ERROR;
		if (!file_exists($pathErrorPage)) {
			// Fatal error if error page does not exist
			throw new \ValueError("Error page (Config::PATH_ERROR) not found in {$pathErrorPage}", E_USER_ERROR);
		}
		$output = Output::createFromFile($pathErrorPage); // Load error page to output handler

		$settings = $output->getContentArray("settings"); // Get error messages from error page
		$errorMessage = $errorMessage // Use provided error message
			?? $settings["error-messages"]["error-{$errorCode}"] // or get error message for error code
			?? $settings["error-messages"]["default"] // or get default error message
			?? Config::ERROR_FATAL // or use fatal error message
			?? "<h1>Error</h1><p>An error occurred in the request.</p><p>Please contact the webmaster</p>"; // or use fallback for fatal error

		$output->replaceAll("error-code", htmlspecialchars($errorCode)); // Replace error code placeholder
		$output->replaceAll("error-message", $errorMessage); // Replace error message placeholder
		$output = ControllerHtml::handleHtml($output); // Handle html content (e.g. add head, header and footer)
		$output->print(); // Print output
	}
}
