<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

*/

namespace FileRouter;

use \GdImage;

/**
 * Class ControllerImage
 *
 * Responsible for handling image files.
 */
class ControllerImage
{
	/**
	 * Handles the specified image file and outputs a resized version of the image.
	 * The width of the resized image can be specified by the query parameter "res".
	 * If the width is not specified, the original width of the image is used.
	 * The height of the resized image is calculated based on the aspect ratio of the original image.
	 *
	 * @param string $filePath The path to the image file.
	 * @return bool Returns true if the image was successfully resized and outputted, false otherwise.
	 */
	public static function handle(string $filePath): bool
	{
		if (!class_exists("GdImage")) return false; // Check if GD library is available
		$imageType = Misc::getMime($filePath); // Get mime type of file

		// Create image based on mime type, resize it and output it
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
		$image = imagescale($image, self::getWidth($image)); // Resize image to requested width
		imagesavealpha($image, true); // Save alpha channel
		return $image;
	}

	private static function getWidth(GdImage $image): int
	{
		$originalWidth = imagesx($image) ?? 0; // Get original width of image
		$requestWidth = Request::get(Config::IMAGE_RESIZE_QUERY) ?? $originalWidth; // Get requested width from query parameter
		return min($originalWidth, $requestWidth); // Return requested width if smaller than original width
	}
}
