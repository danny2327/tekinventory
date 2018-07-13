<?php

namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;
use App\Models\Collections\ItemCollection;

class CollectionUnique extends AbstractRule
{
	public function validate($input) 
	{
		return ItemCollection::where('collection', $input)->count() === 0;
	}
}