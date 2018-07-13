<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Interop\Container\ContainerInterface;

//This Model class exists solely as a template for other Models, and as such cannot be instantiated.  This is where common methods go.
abstract class MyModel extends Model
{

	 protected $c;

	 public function __construct(ContainerInterface $c)
	 {
	 	$this->c = $c;
	 }
}