<?php

namespace App\Controllers\Auth;
use App\Controllers\Controller;
use App\Models\User;
use Respect\Validation\Validator as v;


class AuthController extends Controller
{

	public function getSignOut($request, $response)
	{
		$this->c->auth->logout();
		
		return $response->withRedirect($this->c->router->pathFor('home'));
	}

	public function getSignIn($request, $response)
	{
		return $this->c->view->render($response, 'auth/signin.twig');
	}

	public function postSignIn($request, $response)
	{
		
		$validation = $this->c->validator->validate($request, [
			'email' => v::noWhiteSpace()->notEmpty(),
		]);

		if ($validation->failed()) {

			$this->c->flash->addMessage('error', 'Do you really work here? Try again');
			return $response->withRedirect($this->c->router->pathFor('auth.signin'));
		}

		$auth = $this->c->auth->attempt(
			$request->getParam('email')
		);

		if (!$auth) {
			$this->c->flash->addMessage('error', 'Intruder Alert. Intruder Alert.');
			return $response->withRedirect($this->c->router->pathFor('auth.signin'));
		}
		return $response->withRedirect($this->c->router->pathFor('home'));

	}

}