<?php
return function (FileRouter\Output $content): FileRouter\Output {
	$content->replaceNodeContent("p", "Route File");
	return $content;
};
