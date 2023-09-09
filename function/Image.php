<?php

namespace FileRouter;

use \GdImage;

class Image
{
	private string $imagePath;
	private GdImage $image;
	public function handle(string $imagePath, string $imageType)
	{
		$this->imagePath = $imagePath;
		if ($imageType == "image/png") {
			$this->image = imagecreatefrompng($imagePath);
			return imagepng($this->resize());
		}
		if ($imageType == "image/jpeg") {
			$this->image = imagecreatefromjpeg($imagePath);
			return imagejpeg($this->resize());
		}
		if ($imageType == "image/gif") {
			$this->image = imagecreatefromgif($imagePath);
			return imagegif($this->resize());
		}
		if ($imageType == "image/webp") {
			$this->image = imagecreatefromwebp($imagePath);
			return imagewebp($this->resize());
		}
		return false;
	}
	private function resize()
	{
		$widthOrigin = getimagesize($this->imagePath)[0];
		$widthRequest = Request::get("res") ?? $widthOrigin;
		$image = imagescale($this->image, min($widthOrigin, $widthRequest));
		imagesavealpha($image, true);
		return $image;
	}
}
