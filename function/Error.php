<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

*/

namespace FileRouter;

/**
 * Class Error
 *
 * Represents an error with an error code and an error message.
 */
class Error
{
	private $errorCode;

	private $errorMessage;

	/**
	 * Error constructor.
	 * Creates new error with provided error code and error message.
	 * If no error message is provided, the default error message for the error code is used.
	 *
	 * @param int $errorCode The error code.
	 * @param string|null $errorMessage The error message (optional).
	 */
	public function __construct(int $errorCode, string $errorMessage = null)
	{
		$this->errorCode = $errorCode; // Set error code

		if (is_null($errorMessage)) {
			// Set error message based on error code if no custom message is provided
			$this->errorMessage = Config::ERROR_MESSAGE[$errorCode]
				?? Config::ERROR_MESSAGE["default"]
				?? Config::ERROR_FATAL
				?? "<h1>Error</h1><p>An error occurred in the request.</p><p>Please contact the webmaster</p>";
		} else {
			$this->errorMessage = $errorMessage; // Set custom error message
		}
	}

	/**
	 * Handles the error by setting the HTTP status code, loading and replacing placeholders in the error page,
	 * handling HTML content, printing the output, and stopping further execution.
	 *
	 * @return void
	 */
	public function handle(): void
	{
		http_response_code($this->errorCode); // Set HTTP status code based on error code

		$pathErrorPage = $_SERVER["DOCUMENT_ROOT"] . Config::PATH_ERROR;
		if (!file_exists($pathErrorPage)) die(Config::ERROR_FATAL); // Fatal error if error page does not exist

		$output = new Output($pathErrorPage); // Load error page to output handler
		$output->replaceAll("error-code", htmlspecialchars($this->errorCode)); // Replace error code placeholder
		$output->replaceAll("error-message", htmlspecialchars($this->errorMessage)); // Replace error message placeholder
		$output = ControllerHtml::handle($output); // Handle html content (e.g. add head, header and footer)
		$output->print(); // Print output
		exit; // Stop further execution
	}
}
