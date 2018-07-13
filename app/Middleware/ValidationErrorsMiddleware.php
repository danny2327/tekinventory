<?php

namespace App\Middleware;

class ValidationErrorsMiddleware extends Middleware
{
	
	public function __invoke($request, $response, $next){
		
			$this->container->view->getEnvironment()->addGlobal('errors',$_SESSION['validationErrors']);
			$_SESSION['validationErrors']=null;
			
			$response = $next($request, $response);
			return $response;

	}

}