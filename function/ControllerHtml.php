<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

*/

namespace FileRouter;

class ControllerHtml
{
	private static array $settings;
	private static Output $content;
	public static function redirect(string $filePath): bool
	{
		$content = new Output($filePath);
		$content = self::handle($content);
		$content->print();
		return true;
	}

	public static function handle(Output $content): Output
	{
		self::$content = $content;

		self::$settings = self::$content->getContentArray("settings");
		self::$content->replace("settings", "");
		self::handleHead();
		self::handleHeader();
		self::handleFooter();

		return self::$content;
	}

	private static function handleHead(): void
	{
		$head = new Output($_SERVER["DOCUMENT_ROOT"] . Config::PATH_HEAD);
		if (!isset(self::$settings["title"])) {
			$title = self::$content->getContent("h1");
			if (!empty(Config::TITLE_PREFIX)) {
				$title = Config::TITLE_PREFIX . Config::TITLE_SEPARATOR . $title;
			}
			if (!empty(Config::TITLE_SUFFIX)) {
				$title = $title . Config::TITLE_SEPARATOR . Config::TITLE_SUFFIX;
			}
		} elseif (empty(self::$settings["title"])) {
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
			$title = self::$settings["title"];
		}
		$head->replaceContent("title", $title);
		$replace = "<html><head>" . $head->getContent("head") . "</head><body>" . self::$content->getContent("body") . "</body></html>";
		self::$content = new Output($replace, true);
	}

	private static function handleHeader(): void
	{
		$contentHeader = self::$content->getContent("header");
		if (is_null($contentHeader)) {
			$header = new Output($_SERVER["DOCUMENT_ROOT"] . Config::PATH_HEADER);
			self::$content->replaceContent("body", $header->getContent("html") . self::$content->getContent("body"), "xml");
		} elseif (empty(trim($contentHeader))) {
			self::$content->replace("header", "");
		}
	}

	private static function handleFooter(): void
	{
		$contentFooter = self::$content->getContent("footer");
		if (is_null($contentFooter)) {
			$footer = new Output($_SERVER["DOCUMENT_ROOT"] . Config::PATH_FOOTER);
			self::$content->replaceContent("body", self::$content->getContent("body") . $footer->getContent("html"), "xml");
		} elseif (empty(trim($contentFooter))) {
			self::$content->replace("footer", "");
		}
	}
}
