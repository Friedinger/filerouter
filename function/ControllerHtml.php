<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

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
	/**
	 * Outputs the content of specified HTML or PHP file to the browser after processing it.
	 *
	 * @param string $filePath The file path to redirect to.
	 * @return bool Returns true if the redirection is successful, false otherwise.
	 */
	public static function redirect(string $filePath): bool
	{
		$content = new Output($filePath);
		$content = self::handle($content);

		// Handle custom function from route file
		$content = Proxy::handleCustom($content);

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
	public static function handle(Output $content): Output
	{
		// Get settings from content file
		$settings = $content->getNodeContentArray("settings");
		$content->replaceAll("settings", "");

		// Handle head, header and footer
		$content = self::handleHead($content, $settings);
		$content = self::handleHeader($content);
		$content = self::handleFooter($content);

		return $content;
	}

	private static function handleHead(Output $content, array $settings): Output
	{
		$head = new Output($_SERVER["DOCUMENT_ROOT"] . Config::PATH_HEAD);

		// Set page title
		if (!isset($settings["title"])) {
			// Set title from h1 with prefix and suffix
			$title = $content->getNodeContent("h1");
			if (!empty(Config::TITLE_PREFIX)) {
				$title = Config::TITLE_PREFIX . Config::TITLE_SEPARATOR . $title;
			}
			if (!empty(Config::TITLE_SUFFIX)) {
				$title = $title . Config::TITLE_SEPARATOR . Config::TITLE_SUFFIX;
			}
		} elseif (empty($settings["title"])) {
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
			$title = $settings["title"];
		}
		$head->replaceNodeContent("title", $title);

		$replace = "<html><head>" . $head->getNodeContent("head") . "</head><body>" . $content->getNodeContent("body") . "</body></html>";
		return new Output($replace, true);
	}

	private static function handleHeader(Output $content): Output
	{
		$contentHeader = $content->getNodeContent("header");
		if (is_null($contentHeader)) {
			// Load header from modules file
			$header = new Output($_SERVER["DOCUMENT_ROOT"] . Config::PATH_HEADER);
			$replace = $header->getNodeContent("body") . $content->getNodeContent("body");
			$content->replaceNodeContent("body", $replace);
			return $content;
		} elseif (empty(trim($contentHeader))) {
			// Remove header
			$content->replaceAll("header", "");
			return $content;
		} else {
			// Keep header set in content
			return $content;
		}
	}

	private static function handleFooter(Output $content): Output
	{
		$contentFooter = $content->getNodeContent("footer");
		if (is_null($contentFooter)) {
			// Load footer from modules file
			$footer = new Output($_SERVER["DOCUMENT_ROOT"] . Config::PATH_FOOTER);
			$replace = $content->getNodeContent("body") . $footer->getNodeContent("body");
			$content->replaceNodeContent("body", $replace);
			return $content;
		} elseif (empty(trim($contentFooter))) {
			// Remove footer
			$content->replaceAll("footer", "");
			return $content;
		} else {
			// Keep footer set in content
			return $content;
		}
	}
}
