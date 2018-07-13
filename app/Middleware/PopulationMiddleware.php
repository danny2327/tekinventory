<?php

namespace App\Middleware;

use App\Models\Items\itemType;
use App\Models\Items\allowableProperties;
use App\Models\Items\Property;

class PopulationMiddleware extends Middleware
{
	//Gathers and passes all the fields needed to populate the Nav
	
	public function __invoke($request, $response, $next){
			
			$this->container->view->getEnvironment()->addGlobal('collections', $this->container->helpers->getCollections());
			$this->container->view->getEnvironment()->addGlobal('categories', $this->container->helpers->getCategory());
					
			$response = $next($request, $response);
			return $response;
	}

}
