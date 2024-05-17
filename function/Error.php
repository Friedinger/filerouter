<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

*/

namespace FileRouter;

class Error
{
	private int $errorCode;
	private string $errorMessage;

	public function __construct(int $errorCode, string $errorMessage = null)
	{
		$this->errorCode = $errorCode;
		if (is_null($errorMessage)) {
			$this->errorMessage = Config::ERROR_MESSAGE[$errorCode] ?? Config::ERROR_MESSAGE["default"] ?? Config::ERROR_FATAL;
		} else {
			$this->errorMessage = $errorMessage;
		}
	}
	public function handle(): void
	{
		http_response_code($this->errorCode);
		$pathErrorPage = $_SERVER["DOCUMENT_ROOT"] . Config::PATH_ERROR;
		if (!file_exists($pathErrorPage)) die(Config::ERROR_FATAL);
		$output = new Output($pathErrorPage);
		$output->replace("error-code", htmlspecialchars($this->errorCode));
		$output->replace("error-message", htmlspecialchars($this->errorMessage));
		$output = ControllerHtml::handle($output);
		$output->print();
		exit;
	}
}
