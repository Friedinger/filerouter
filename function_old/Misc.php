<?php

/*

FileRouter
A simple php router that allows to run code before accessing a file while keeping the file structure as the url structure.

by Friedinger (friedinger.org)

*/

namespace FileRouter;

final class Misc
{
	public static function session(): string|false
	{
		if (session_id() == "") {
			session_set_cookie_params([
				"secure" => true,
				"httponly" => true,
				"samesite" => "Strict",
			]);
			session_name("Friedinger");
			session_start();
		}
		return session_id();
	}
}
