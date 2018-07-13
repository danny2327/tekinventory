<?php

namespace App\Models\Items;
use Illuminate\Database\Eloquent\Model;

class ItemType extends Model
{
	
	protected $table = 'itemtypes';

    protected $fillable = [

        'itemType',
        'markup_mult'
    ];

    public static function getPropertyList($id){
        $propertiesList = allowableProperties::
            with('Property')
            ->where('itemType_id',$id)
            ->get();

        return $propertiesList;
    }
    
    public function item()
    {
        return $this->belongsTo('App\Models\Items\Item','itemType_id');
    }  

    public function AllowProp()
    {
        return $this->hasMany('App\Models\Items\allowableProperties');
    }


}
