<?php

namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;
use App\Models\Items\ItemType;

class ItemTypeUnique extends AbstractRule
{
	public function validate($input) 
	{
		return ItemType::where('itemType', $input)->count() === 0;
	}
}