<?php

namespace App\Models\Items;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
	
	protected $table = 'properties';
	
	public $timestamps = false;
	
	protected $fillable = [

        'property',
        'dataType'
    ];
	

	public function AllowProp()
    {
        return $this->hasMany('App\Models\Items\allowableProperties');
    }

    
}
