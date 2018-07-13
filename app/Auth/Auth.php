<?php

namespace App\Auth;

use App\Models\User;

class Auth
{

	public function attempt($email)
	{

		$user = User::where('email',$email)->first();

		if(!$user){
			return false;
		}

		//Sign in and create sessions var.
        $_SESSION['user'] = $user->id;
        return true;

		return false;
	}

	public function check() {
		return isset($_SESSION['user']);
	}

	public function user() {
		return User::find($_SESSION['user']);
	}

	public function logout()
	{
		unset($_SESSION['user']);
	}

}