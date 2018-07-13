<?php

namespace App;

use App\Models\Items\itemType;
use App\Models\Items\allowableProperties;
use App\Models\Items\Property;
use App\Models\Collections\ItemCollection;
use Interop\Container\ContainerInterface;

class Helpers {

	protected $c;
	
	public function __construct(ContainerInterface $c)
	{
		$this->c = $c;
	}

	public function getPropertiesList(){
		$propertiesList = allowableProperties::
			with('Property','itemType')
			->get();
			
		//for determining whether to use the filter for booleans
		foreach($propertiesList as $k=>$v) {
			$propertiesList[$k]['bool'] = $propertiesList[$k]['property']['dataType'] == 'Boolean' ? 'true' : 'false';	
			//echo $propertiesList[$k]['bool']."<br />";		
		}
		//die();

		return $propertiesList;
	}

	public function getPropertyList($id){
		$propertiesList = allowableProperties::
			with('Property')
			->where('itemType_id',$id)
			->get();

		//for determining whether to use the filter for booleans
		foreach($propertiesList as $k=>$v) {
			$propertiesList[$k]['bool'] = $propertiesList[$k]['property']['dataType'] == 'Boolean' ? 'true' : 'false';	
			//echo $propertiesList[$k]['bool']."<br />";		
		}

		return $propertiesList;
	}

	public function getCategory(){
		$categories = itemType::
			orderBy('itemType','asc')
			->get();

		return $categories;
	}

	public function getCollections() {
		$collections = itemCollection::
			orderBy('collection_id','asc')
			->get();

		return $collections;
	}

	public function getType($id) {
		$type = itemType::
			where('id',$id)
			->select('itemType')
			->first();
			
		return $type;
	}

	//Returns the quantity of items in all collections with a given id, total
	public function getQuantity($id){
		$quantity =	$this->c->db::table('collection_item')
		->where('item_id',$id)
		->sum('quantity');

		return $quantity;
	}

	//Returns the quantity of items in all collections within a given array of ids
	public function getQuantities($items, $collection, $isArray){

		foreach($items as $item){
			$itemsID[]= $item->item_id;
		}

		if($isArray) { 
			$itemsQuantity = $this->c->helpers->getQuantitiesColls($itemsID, $collection);

		} elseif($collection > 0){
			$itemsQuantity = $this->c->helpers->getQuantitiesSingle($itemsID, $collection);
			
		} else { //all items
			$itemsQuantity = $this->c->helpers->getQuantitiesAll($itemsID);
		}
		
		return $itemsQuantity;
	}

	//Returns the quantity of the given items in the given collection
	public function getQuantitiesSingle($itemsID, $collection){

		foreach($itemsID as $v){ 		
			$q = $this->c->db::table('collection_item')
				->where('item_id','=', $v)
				->where('collection_id','=',  $collection)
				->pluck('quantity');						
						
						
				//if($q[0]) {  //have this because of the 'undefined offset of 0' error on add stock page
				 //this time it was caused by no entry for 10 in warehouse
					$itemsQuantity[$v]= $q[0];
				//}
		}

		return $itemsQuantity;
	}

	public function getQuantitiesItem($itemID){
		
		$itemsQuantity = $this->c->db::table('collection_item AS ci')
			->join('collections AS c','ci.collection_id','=','c.collection_id')
			->where('item_id', $itemID)
			->get();

		return $itemsQuantity;
	}

	public function getQuantitiesAll($itemsID){

		foreach($itemsID as $v){ 		
			$itemsToMove[$v] = $this->c->db::table('collection_item')
				->where('item_id',$v)
				->sum('quantity');				
		}

		return $itemsToMove;
	}
	
	public function getQuantitiesColls($itemsID, $collections){
/*						echo "<pre>";
						var_dump($itemsID);
						echo "</pre>";
						echo "<pre>";
						var_dump($collections);
						echo "</pre>";
						die();*/

		// need to get the quantities of each item in each collection
			//$quan[collection_id][item_id]
/*		foreach($itemsID as $v){ 		
			$quantity[$v] = $this->c->db::table('collection_item')
				->where('item_id',$v)
				->wherein('collection_id',$collections)
				->get();
				//->pluck('quantity');
						
			
			//$justQuantity[$v] = $quantity[$v];
		}*/
		//$quantity[$v]


	$quan = $this->c->db::table('collection_item')
	->wherein('item_id',$itemsID)
	->wherein('collection_id',$collections)
	->get();


		foreach($quan as $q) {
			$quantity[$q->collection_id][$q->item_id] = $q->quantity;

		}
			/*echo "<pre>";
		print_r($quantity);
		echo "</pre>";
		die();*/
		/*echo "<pre>";
		var_dump($quantity);
		echo "</pre>";
		die();*/

		return $quantity;
	}

	public function getQuantitiesByCollection($items){
			
		$quantities = $this->c->db::table('collection_item')
			->get();				

		$quanByColl = array();
		
		foreach($quantities as $q){	
/* echo "<pre>";
		var_dump($q->quantity);
		echo "</pre> - ";*/
/*die();*/
			if(isset($quanByColl[$q->item_id]))
			{
				$quanByColl[$q->item_id] += $q->quantity;
			} else {
				$quanByColl[$q->item_id]= $q->quantity;
			}


			//$quanByColl[1][1] = 54;
		}		/*echo "<pre>";
		var_dump($quanByColl);
		echo "</pre> ";
die();*/
		return $quanByColl;
	}
}