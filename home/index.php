<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

Version 3.2.0

*/

require_once $_SERVER["DOCUMENT_ROOT"] . "/../config/config.php"; // Load config file
require_once $_SERVER["DOCUMENT_ROOT"] . "/../function/FileRouter.php"; // Load FileRouter main class

$fileRouter = new FileRouter\FileRouter(); // Start FileRouter
