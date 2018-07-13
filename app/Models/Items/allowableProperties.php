<?php

namespace App\Models\Items;
use Illuminate\Database\Eloquent\Model;

class allowableProperties extends Model
{
	
    protected $table = 'allowableProperties';
    
    public $timestamps = false;
    
    protected $fillable = [
        'property_id',
        'itemType_id'
    ];
	
	 public function property()
    {
        return $this->belongsTo('App\Models\Items\Property','property_id','id');
    }

	public function itemType()
    {
        return $this->belongsTo('App\Models\Items\ItemType', 'itemtype_id','id');
    }

    
}
