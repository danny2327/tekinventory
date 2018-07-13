<?php

namespace App\Models\Items;
use Illuminate\Database\Eloquent\Model;

class CollectionItem extends Model
{
	
	protected $table = 'collection_item';

	protected $primaryKey = 'item_id';

	public $timestamps = false;

	protected $fillable = [

		'item_id',
		'collection_id',
		'quantity',
	];

	/*public function itemType()
    {
        return $this->hasOne('App\Models\Items\ItemType','id','itemType_id');
    }*/

   public function ItemCollection()
    {
        return $this->belongsToMany('App\Models\Collections\ItemCollection', "collections","collection_id","collection_id")->withPivot('quantity');
    }


   
}
