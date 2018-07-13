<?php

namespace App\Models\Collections;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Models\User;
use App\Models\Items\Item;
use Interop\Container\ContainerInterface;



class ItemCollection extends Model
{
	
	protected $fillable = [
		'collection',
		'protected',
		'archived',
	];

	protected $table = "collections";	
	protected $primaryKey = "collection_id";		

	protected $items;
	protected $name;
/* 
	protected $c;
	
	public function __construct(ContainerInterface $c)
	{
		$this->c = $c;
		//$this->items = new Array();
	}
 */
	//add item[s] to a given collection
	//items is just ids
	public function add($itemsList, $collection_id)
	{
		//$this->items->collect($items);
		
		$items = Item::find(array_keys($itemsList));
		
/*		echo "<pre>";
		var_dump($items);
		echo "</pre>";

		die();*/
		foreach($items as $k=>$v){
			//Adds the record to the collection, default of Warehouse.
			ItemCollection::checkIfExists($k,1);
			$item->ItemCollection()->attach(
				1, 
				['quantity' => $v]
			);
		}

		return $response->withRedirect($this->c->router->pathFor('display'));	
	} 		

	

	//move items out of 
	public function move($items, $source, $destination)
	{


	}

/*	public function decrement($source,$k,$v)
	{
		return $this->c->db::table('collection_item')
				->where([
				    ['collection_id', '=', $source],
				    ['item_id', '=', $k],
				])
				->decrement('quantity', $v);
	}*/


	//mark item[s] as sold
	public function markAsSold($items, String $client)
	{

	}

	//getLastUpdated
	public function getLastUpdated() 
	{
		//get from db

	}

	public function allItems()
	{
		return $items;
	}

	public function contains(String $search)
	{
	
	//be searched
	}

	public function item() {

		return $this->belongsToMany("Item","collection_item","collection_id","item_id")->withPivot('quantity');

	}

   /*public function CollectionItem()
    {
        return $this->hasMany('App\Models\Items\CollectionItem', "collection_item","collection_id","collection_id");
    }*/


}