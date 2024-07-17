<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

*/

namespace FileRouter;

use DOMDocument;
use DOMNode;

final class Output
{
	private DOMDocument $dom;

	public function __construct(string $content, bool $directInput = false)
	{
		$this->dom = new DOMDocument();
		if ($directInput) {
			$output = $content;
		} elseif (Config::ALLOW_PAGE_PHP) {
			ob_start();
			require($content);
			$output = ob_get_clean();
		} else {
			$output = file_get_contents($content);
		}
		$this->dom->loadHTML(mb_convert_encoding($output, 'HTML-ENTITIES', 'UTF-8'), LIBXML_NOERROR);
	}

	public function print(): void
	{
		$this->dom->formatOutput = true;
		echo $this->dom->saveHTML($this->dom->documentElement);
	}

	public function getContent(string $tagName = null): string|null
	{
		if (is_null($tagName)) $tagName = $this->dom->documentElement->tagName;
		$node = $this->dom->getElementsByTagName($tagName)->item(0);
		if (!$node) return null;
		$dom = new DOMDocument;
		foreach ($node->childNodes as $child) {
			$dom->appendChild($dom->importNode($child, true));
		}
		return str_replace("%20", " ", $dom->saveHTML());
	}

	public function getContentArray(string $tagName = "body"): array
	{
		$node = $this->dom->getElementsByTagName($tagName)->item(0);
		if (!$node) return [];
		return $this->getContentArrayNode($node);
	}

	private function getContentArrayNode(DOMNode $node): array|null
	{
		$content = [];
		foreach ($node->childNodes as $child) {
			if ($child->nodeType == XML_TEXT_NODE && trim($child->nodeValue) != "") {
				$content["text"] = trim($child->nodeValue);
				continue;
			}
			if ($child->nodeType == XML_TEXT_NODE && $child->childNodes->length == 0) continue;
			if ($child->childNodes->length == 1) {
				$content[$child->nodeName] = $child->nodeValue;
				continue;
			}
			$content[$child->nodeName] = $this->getContentArrayNode($child);
		}
		return $content;
	}

	public function replace(string $tag, string|null $content, string $type = "text"): void
	{
		$tag = strtolower($tag);
		$this->replaceNodes($tag, $content, $type);
		$this->replaceAttributes($tag, $content ?? "");
	}

	public function replaceContent(string $tag, string|null $content, string $type = "text"): void
	{
		$tag = strtolower($tag);
		$nodeList = $this->dom->getElementsByTagName($tag);
		if ($nodeList->length == 0) return;

		foreach ($nodeList as $node) {
			$replacement = $this->dom->createElement($tag);
			self::copyAttributes($node, $replacement);
			$replacement->appendChild($this->createNode($content, $type));
			$node->parentNode->replaceChild($replacement, $node);
			return;
		}
	}

	private function replaceNodes(string $tag, string|null $content, string $type = "text"): void
	{
		$nodeList = $this->dom->getElementsByTagName($tag);
		if ($nodeList->length == 0) return;
		$nodes = [];
		$replacement = $this->createNode($content, $type);
		foreach ($nodeList as $node) {
			array_push($nodes, $node);
		}
		foreach ($nodes as $node) {
			$element = $replacement->cloneNode(true);
			self::copyAttributes($node, $element);
			$node->parentNode->replaceChild($element, $node);
		}
	}

	private function replaceAttributes(string $tag, string|null $content): void
	{
		$content = $content ?? "";
		$xpath = new \DOMXPath($this->dom);
		$nodes = $xpath->query("//" . "*[@*[contains(translate(., 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'), '{$tag}')]]");
		foreach ($nodes as $node) {
			foreach ($node->attributes as $attribute) {
				$value = $attribute->value;
				$value = str_ireplace("<{$tag}></{$tag}>", $content, $value);
				$value = str_ireplace("<{$tag} />", $content, $value);
				$value = str_ireplace("<{$tag}/>", $content, $value);
				$value = str_ireplace("<{$tag}>", $content, $value);
				$attribute->value = $value;
			}
		}
	}

	private function createNode(string|null $value, string $type = "text"): DOMNode
	{
		switch ($type) {
			case "iframe":
				$element = $this->dom->createElement("iframe");
				$element->setAttribute("src", "?action=view");
				$element->setAttribute("title", $value);
				break;
			case "code":
				$element = $this->dom->createElement("code");
				$element->nodeValue = htmlspecialchars(str_replace("\n", "<br>", $value));
				break;
			case "img":
				$element = $this->dom->createElement("img");
				$element->setAttribute("src", "?action=view");
				$element->setAttribute("alt", $value);
				break;
			case "audio":
				$element = $this->dom->createElement("audio");
				$element->setAttribute("src", "?action=view");
				$element->setAttribute("controls", true);
				break;
			case "video":
				$element = $this->dom->createElement("video");
				$element->setAttribute("src", "?action=view");
				$element->setAttribute("controls", true);
				break;
			case "xml":
				$valueDom = new DOMDocument();
				$valueDom->loadHTML(mb_convert_encoding($value, 'HTML-ENTITIES', 'UTF-8'), LIBXML_NOERROR);
				// TODO: False html opening tags
				$element = $this->dom->importNode($valueDom->documentElement, true);
				break;
			default:
				$element = $this->dom->createTextNode($value);
				break;
		}
		return $element;
	}

	private function copyAttributes($oldNode, $newNode): void
	{
		if ($newNode->nodeType == XML_TEXT_NODE || $newNode->nodeType == XML_DOCUMENT_FRAG_NODE) {
			return;
		}
		if ($oldNode->hasAttributes()) {
			foreach ($oldNode->attributes as $attribute) {
				$newNode->setAttribute($attribute->name, $attribute->value);
			}
		}
	}
}
