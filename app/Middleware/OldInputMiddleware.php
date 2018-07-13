<?php

namespace App\Middleware;

class OldInputMiddleware extends Middleware
{
	
	public function __invoke($request, $response, $next){



			//  if($request->isPost()) {
			// // 	//$_SESSION['old'] = $request->getParams();
			//  	//echo(":".$request->isGet());print_r($_SESSION['old']);dd($request->getParams());
			//  }

			 $this->container->view->getEnvironment()->addGlobal('old',$_SESSION['old']);
			 $_SESSION['old'] = $request->getParams();
			// $_SESSION['old']['test'] = "still here";

			$response = $next($request, $response);
			return $response;
	}
}