<?php

namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;
use App\Models\Items\Property;

class PropertyUnique extends AbstractRule
{
	public function validate($input) 
	{
		return Property::where('property', $input)->count() === 0;
	}
}