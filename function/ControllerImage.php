<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

*/

namespace FileRouter;

use \GdImage;

class ControllerImage
{
	public static function redirect(string $filePath): bool
	{
		if (!class_exists("GdImage")) return false;
		$imageType = Misc::getMime($filePath);
		if ($imageType == "image/png") {
			$image = imagecreatefrompng($filePath);
			return imagepng(self::resize($image));
		}
		if ($imageType == "image/jpeg") {
			$image = imagecreatefromjpeg($filePath);
			return imagejpeg(self::resize($image));
		}
		if ($imageType == "image/gif") {
			$image = imagecreatefromgif($filePath);
			return imagegif(self::resize($image));
		}
		if ($imageType == "image/webp") {
			$image = imagecreatefromwebp($filePath);
			return imagewebp(self::resize($image));
		}
		return false;
	}

	private static function resize(GdImage $image): GdImage
	{
		$image = imagescale($image, self::getWidth($image));
		imagesavealpha($image, true);
		return $image;
	}

	private static function getWidth(GdImage $image): int
	{
		$originalWidth = imagesx($image) ?? 0;
		$requestWidth = Request::get("res") ?? $originalWidth;
		return min($originalWidth, $requestWidth);
	}
}
