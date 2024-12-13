<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

https://github.com/Friedinger/FileRouter

by Friedinger (friedinger.org)

*/

namespace FileRouter;

/**
 * Class ControllerHtml
 *
 * Responsible for handling HTML and PHP files.
 */
class ControllerHtml
{
	private static Output $settings;

	/**
	 * Outputs the content of specified HTML or PHP file to the browser after processing it.
	 *
	 * @param string $filePath The file path to redirect to.
	 * @return bool Returns true if the redirection is successful, false otherwise.
	 */
	public static function handle(string $filePath): bool
	{
		$content = new Output($filePath); // Load content of the web page

		$content = self::handleHtml($content); // Handle content of the web page

		// Print handled content
		$content->print();
		return true;
	}

	/**
	 * Handles the content of the web page.
	 *
	 * This method processes the content of the web page by handling the head, header, and footer.
	 * The settings are read from the content file and the title is set accordingly.
	 *
	 * @param Output $content The content of the web page.
	 * @return Output The processed content of the web page.
	 */
	public static function handleHtml(Output $content): Output
	{
		$content = self::handleSettings($content); // Get and store settings if not already set

		$content = Config::handleCustomPre($content, self::$settings); // Handle custom config pre function
		$content = Proxy::handleCustom($content, self::$settings); // Handle custom route file function

		// Handle head, header and footer
		$content = self::handleHead($content);
		$content = self::handleHeader($content);
		$content = self::handleFooter($content);

		$content = Config::handleCustomPost($content, self::$settings); // Handle custom config post function

		// Output CSRF token if enabled
		if (Config::SESSION && Config::CSRF_LENGTH > 0) {
			$content->replaceAllSafe(Config::CSRF_TEMPLATE, Misc::generateCsrfToken());
		}

		return $content;
	}

	private static function handleHead(Output $content): Output
	{
		$head = new Output($_SERVER["DOCUMENT_ROOT"] . Config::PATH_HEAD);

		// Set page title
		if (is_null(self::$settings->getContent("title"))) {
			// Set title from h1 with prefix and suffix
			$title = $content->getContent("h1");
			if (!empty(Config::TITLE_PREFIX)) {
				$title = Config::TITLE_PREFIX . Config::TITLE_SEPARATOR . $title;
			}
			if (!empty(Config::TITLE_SUFFIX)) {
				$title = $title . Config::TITLE_SEPARATOR . Config::TITLE_SUFFIX;
			}
		} elseif (empty(self::$settings->getContent("title"))) {
			// Empty title just use prefix and suffix
			$title = "";
			if (!empty(Config::TITLE_PREFIX)) {
				$title = Config::TITLE_PREFIX;
			}
			if (!empty(Config::TITLE_PREFIX && !empty(Config::TITLE_SUFFIX))) {
				$title = $title . Config::TITLE_SEPARATOR;
			}
			if (!empty(Config::TITLE_SUFFIX)) {
				$title = $title . Config::TITLE_SUFFIX;
			}
		} else {
			// set title from settings with prefix and suffix
			$title = self::$settings->getContent("title");
		}
		$title = strip_tags($title);
		$title = mb_convert_encoding($title, "UTF-8", ["HTML-ENTITIES"]);
		$head->replaceContent("title", $title);
		$content->setContent("head", $head->getContent("head"), null, true);
		return $content;
	}

	private static function handleHeader(Output $content): Output
	{
		$contentHeader = $content->getContent("header");
		if (is_null($contentHeader)) {
			// Load header from modules file
			$header = new Output($_SERVER["DOCUMENT_ROOT"] . Config::PATH_HEADER);
			$replace = $header->getContent("body") . $content->getContent("body");
			$content->replaceContent("body", $replace);
		} elseif (empty(trim($contentHeader))) {
			$content->replaceAll("header", ""); // Remove header
		}
		return $content;
	}

	private static function handleFooter(Output $content): Output
	{
		$contentFooter = $content->getContent("footer");
		if (is_null($contentFooter)) {
			// Load footer from modules file
			$footer = new Output($_SERVER["DOCUMENT_ROOT"] . Config::PATH_FOOTER);
			$replace = $content->getContent("body") . $footer->getContent("body");
			$content->replaceContent("body", $replace);
		} elseif (empty(trim($contentFooter))) {
			$content->replaceAll("footer", ""); // Remove footer
		}
		return $content;
	}

	private static function handleSettings(Output $content): Output
	{
		if (!isset(self::$settings)) {
			self::$settings = new Output($content->getContent("settings") ?? " ", true);
			$content->replaceAll("settings", "");
		}
		return $content;
	}
}
