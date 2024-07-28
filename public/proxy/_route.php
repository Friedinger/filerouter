<?php
return function (FileRouter\Output $content): FileRouter\Output {
	$content->replaceContent("p", "Route File");
	return $content;
};
