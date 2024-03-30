<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

*/

namespace FileRouter;

use \GdImage;

final class Image
{
	public static function handle(string $imagePath, string $imageType)
	{
		$originalWidth = getimagesize($imagePath)[0] ?? 0;
		$requestWidth = Request::get("res") ?? $originalWidth;
		$width = min($originalWidth, $requestWidth);
		if ($imageType == "image/png") {
			$image = imagecreatefrompng($imagePath);
			return imagepng(self::resize($image, $width));
		}
		if ($imageType == "image/jpeg") {
			$image = imagecreatefromjpeg($imagePath);
			return imagejpeg(self::resize($image, $width));
		}
		if ($imageType == "image/gif") {
			$image = imagecreatefromgif($imagePath);
			return imagegif(self::resize($image, $width));
		}
		if ($imageType == "image/webp") {
			$image = imagecreatefromwebp($imagePath);
			return imagewebp(self::resize($image, $width));
		}
		return false;
	}
	private static function resize(GdImage $image, int $width)
	{
		$image = imagescale($image, $width);
		imagesavealpha($image, true);
		return $image;
	}
}
