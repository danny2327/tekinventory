<?php

namespace App\Controllers;

use Interop\Container\ContainerInterface;



//This Controller class exists solely as a template for other controllers, and as such cannot be instantiated.  This is where common methods go.
abstract class Controller 
{
	
	protected $c;
	
	public function __construct(ContainerInterface $c)
	{
		$this->c = $c;
	}

	protected function render404($response){
		return $this->c->view->render($response->withStatus(404), 'errors/404.twig');
	}		
	
}