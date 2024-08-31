<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

Version: 2.2.2

*/

require_once $_SERVER["DOCUMENT_ROOT"] . "/../config/config.php"; // Load config file
require_once $_SERVER["DOCUMENT_ROOT"] . "/../function/FileRouter.php"; // Start FileRouter by loading main file

$fileRouter = new FileRouter\FileRouter();
