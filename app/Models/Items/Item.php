<?php

namespace App\Models\Items;

use Illuminate\Database\Eloquent\Model;
use Slim\Container as con;

use Interop\Container\ContainerInterface;


class Item extends Model
{
	
	protected $primaryKey = 'item_id';

	protected $fillable = [

		'itemType_id',
		'desc',
		'attrib_1',
		'attrib_2',
		'attrib_3',
		'attrib_4',
		'vendor',
		'vendor_sku',
		'buyPrice'
	];

	/*private $c;

	public function __construct(con $c){
		$this->c = $c;
	}*/
	
	//gets the item and property names for 1 item
	public static function getInfo($id) 
	{
		$item = Item::with('itemType')
		->where('item_id',$id)
		->first();
		/* echo "<pre>";
		 var_dump($item);
		 echo "</pre>";
		 die();*/
		return $item;
	}

	public static function test()
	{
		$items = $this->c->db::table
		('items as i')
			->join('itemTypes AS it','i.itemType_id','=','it.id')
			->where('desc',LIKE,'%cable%')
			->orderBy('it.itemType','desc')
			->get();



/*		 echo "<pre>";
		 var_dump($items);
		 echo "</pre>";
		 die();*/

		return $items;
	}

	public function test2()
	{
		$items = $this->c->db::table
		('items as i')
			->join('itemTypes AS it','i.itemType_id','=','it.id')
			->orderBy('it.itemType','desc')
			->get();



/*		 echo "<pre>";
		 var_dump($items);
		 echo "</pre>";
		 die();*/

		return $items;
	}

	public function itemType()
    {
        return $this->hasOne('App\Models\Items\ItemType','id','itemType_id');
    }

   public function ItemCollection()
    {
        return $this->belongsToMany('App\Models\Collections\ItemCollection', "collection_item","item_id","collection_id")->withPivot('quantity');
    }


   
}
