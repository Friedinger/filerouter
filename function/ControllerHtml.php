<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

*/

namespace FileRouter;

class ControllerHtml
{
	public static function redirect(string $filePath): bool
	{
		$content = new Output($filePath);
		$content = self::handle($content);

		// Print handled content
		$content->print();
		return true;
	}

	public static function handle(Output $content): Output
	{
		// Get settings from content file
		$settings = $content->getContentArray("settings");
		$content->replace("settings", "");

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
			$title = $content->getContent("h1");
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
		$head->replaceContent("title", $title);

		$replace = "<html><head>" . $head->getContent("head") . "</head><body>" . $content->getContent("body") . "</body></html>";
		return new Output($replace, true);
	}

	private static function handleHeader(Output $content): Output
	{
		$contentHeader = $content->getContent("header");
		if (is_null($contentHeader)) {
			// Load header from modules file
			$header = new Output($_SERVER["DOCUMENT_ROOT"] . Config::PATH_HEADER);
			$replace = $header->getContent("body") . $content->getContent("body");
			$content->replaceContent("body", $replace, "xml");
			return $content;
		} elseif (empty(trim($contentHeader))) {
			// Remove header
			$content->replace("header", "");
			return $content;
		} else {
			// Keep header set in content
			return $content;
		}
	}

	private static function handleFooter(Output $content): Output
	{
		$contentFooter = $content->getContent("footer");
		if (is_null($contentFooter)) {
			// Load footer from modules file
			$footer = new Output($_SERVER["DOCUMENT_ROOT"] . Config::PATH_FOOTER);
			$replace = $content->getContent("body") . $footer->getContent("body");
			$content->replaceContent("body", $replace, "xml");
			return $content;
		} elseif (empty(trim($contentFooter))) {
			// Remove footer
			$content->replace("footer", "");
			return $content;
		} else {
			// Keep footer set in content
			return $content;
		}
	}
}
