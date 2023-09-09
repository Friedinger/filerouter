<?php

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
			session_name("PhysiotherapieImHandwerkshof");
			session_start();
		}
		return session_id();
	}
}
