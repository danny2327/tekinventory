<?php

namespace App\Controllers;

use App\Models\User;

use App\Models\Items\Item;
use App\Models\Items\ItemType;
use App\Models\Items\CollectionItem;
use App\Models\Items\allowableProperties;
use App\Models\Items\Property;

use App\Models\Collections\ItemCollection;

use Respect\Validation\Validator as v;

class InventoryController extends Controller
{
	public function index($request, $response)
	{
	
		$items = $this->c->db::table('items as i')
			->join('itemTypes AS it','i.itemType_id','=','it.id')
			->orderBy('it.itemType','asc')
			->orderBy('i.item_id','asc')			
			->get();

			//replaced to allow me to sort by item type name
		/* Item::with('itemType')
			->orderBy('itemType','asc')
			->get();*/
			
			if($items->count() === 0){
				return $this->c->view->render($response, 'Display\display.twig');	
			}

			$propertiesList = $this->c->helpers->getPropertiesList();	

			$quantities = $this->c->helpers->getQuantities($items,0, false);
			
			return $this->c->view->render($response, 'Display\display.twig', compact('items', 'propertiesList', 'quantities'));		
	}

	public function viewCategory($request, $response, $args)
	{
		$id = $args; 

		$items = Item::with('itemType')
		->where('itemType_id',$id)
		->get();

		if($items->count() === 0){
			return $this->c->view->render($response, 'Display\display.twig');	
		}

		$propertiesList = $this->c->helpers->getPropertyList($id);		

		$quantities = $this->c->helpers->getQuantities($items,0, false);

		return $this->c->view->render($response, 'Display\display.twig', compact('items', 'propertiesList', 'quantities'));	
	}

	public function getAdd($request, $response)
	{
		return $this->c->view->render($response, 'Inventory\add.twig', compact(''));	
	}

	public function postAdd($request, $response) //Add an Item to an existing category
	{
		//depends on how many attribs there are - minimum two
		if (isset($_POST['attrib_3'])) {
            $attrib_3 = $request->getParam('attrib_3');
		} else {
			$attrib_3 = null;
		}

		if (isset($_POST['attrib_4'])) {
            $attrib_4 = $request->getParam('attrib_4');
		} else {
			$attrib_4 = null;
		}

		$item = Item::create([
            'attrib_1' => $request->getParam('attrib_1'),
            'attrib_2' => $request->getParam('attrib_2'),
            'attrib_3' => $attrib_3,
            'attrib_4' => $attrib_4,
              
            'desc' => $request->getParam('desc'),
            'vendor' => $request->getParam('vendor'),
            'vendor_sku' => $request->getParam('vendor_sku'),
            'itemType_id' => $request->getParam('id'),            
            'buyPrice' => $request->getParam('buyPrice'),            
            //'sellPrice' => $request->getParam('sellPrice'),            
        ]);
		//Adds the record to the collection, default of Warehouse.
		$item->ItemCollection()->attach(
			$request->getParam('collection'), 
			['quantity' => $request->getParam('quantity')]
		);

		//if not being added to warehouse, adds the item with 0 stock to the 
		//warehouse - makes it easier to add stock
		if($request->getParam('collection')!=1) {
			$item->ItemCollection()->attach(
				1,
				['quantity' => 0]
			);  
		}
/*
 		echo "<pre>";
		var_dump($WH);
		echo "</pre>";*/
		
		$this->c->flash->addMessage('info', $request->getParam('desc').' added successfully');
		return $response->withRedirect($this->c->router->pathFor('display'));	
	}

	public function getEditItem($request, $response, $args)
	{
		$item = Item::getInfo($args);
		
		$markupItem = itemType::find($item->itemType_id)->first();
		$markup = floatval($markupItem->markup_mult);

		$_SESSION['item']=$item;

		return $this->c->view->render($response, 'Inventory\Edit.twig', compact('item','markup'));	
	}  

	public function postEdit($request, $response)
	{	
		/*$validation = $this->c->validator->validate($request, [
			'name' => v::notEmpty()->CollectionUnique(),
			]);

			if ($validation->failed()) {

				$this->c->flash->addMessage('error', 'Collection Exists');
				return $response->withRedirect($this->c->router->pathFor('collections.update'));
			}*/

		//compare old fields to new fields to find out what has changed
		$item = Item::find($_SESSION['item']['attributes']['item_id']);

		foreach($request->getParsedBody() as $k=>$v)
		{	
			//included id because of error
			if($k != 'id' && $_SESSION['item']['attributes'][$k] != $v){
				//update the fields that have changed
				 $item->$k = $v;
			}
		}
		//update the fields that have changed
		$item->save();

		unset($_SESSION['item']);
		
		$this->c->flash->addMessage('success', 'Item Updated');
		return $response->withRedirect($this->c->router->pathFor('display'));				
	}

	public function getAddItemType($request, $response)
	{
		$properties = Property::all();

		

		return $this->c->view->render($response, 'Inventory\addType.twig', compact('properties'));	
	}

	public function postAddItemType($request, $response)
	{
		

		//validation!
		$validation = $this->c->validator->validate($request, [
			'name' => v::notEmpty()->ItemTypeUnique(),
			'markup' => v::between(1, 5),
			'attrib_1_new' => v::PropertyUnique(),
			'attrib_2_new' => v::PropertyUnique(),
			'attrib_3_new' => v::PropertyUnique(),
			'attrib_4_new' => v::PropertyUnique(),
			]);

			if ($validation->failed()) {

				$this->c->flash->addMessage('error', 'Please check your fields');
				return $response->withRedirect($this->c->router->pathFor('inventory.addType'));
			}
		//determine if there are any new properties created
		//create the new properties
		//create list of all props to be added to allowable
		//create item type
		//create each allowableProperty

		//Gather the given properties and create and add any new ones
        $propertiesList = array();
        foreach ($request->getParsedBody() as $k => $v) {
        	$addToList="";
			//If there is a new property provided, add it first.
			if(strpos($k, "attrib")!== false && $v !==""){
				$id=substr($k,7,1);

				$propertiesList[] = Property::Create([
			        'property' => $v,
			        'dataType' => $request->getParam('type'.$id),  
			    ])->id;
        	}

        	if(strpos($k, "property")!== false && $v !==""){
        		$propertiesList[]=$v;
        	}			
        }

        //Create Item Type
        $newType = itemType::Create([
        	'itemType' => $request->getParam('name'),
        	'markup_mult' => $request->getParam('markup'),
        ]);


        //Add AllowableProperties
        foreach ($propertiesList as $prop){
        	$AP [] = allowableProperties::Create([
        		'itemType_id' => $newType->id,
        		'property_id' => $prop,
        	]);
        }
		
		$this->c->flash->addMessage('info', $request->getParam('name').' added successfully');
		return $response->withRedirect($this->c->router->pathFor('inventory.add'));	
	}

	public function getAddStock($request, $response)
	{

		$items = $this->c->db::table('items as i')
			->join('itemTypes AS it','i.itemType_id','=','it.id')
			->orderBy('it.itemType','asc')
			->orderBy('i.item_id','asc')
			->get();

			$propertiesList = $this->c->helpers->getPropertiesList();	

			$quantities = $this->c->helpers->getQuantities($items,1, false);


			return $this->c->view->render($response, 'Inventory\add-stock.twig', compact('items', 'propertiesList', 'quantities'));		
	}

	public function postAddStock($request, $response)
	{
		//get variables passed
		//get items from this 
		//in loop find that collection_item
		//get quantity and add to added number
		//set		

		foreach($request->getParsedBody() as $k=>$v)
		{	
			if(is_integer($k) && $v>0){//fix with new array filter
				$modified[$k]=$v;
			}
		}

		$items = Item::find(array_keys($modified));
			
		foreach($items as $item){
			$id = $item->item_id;
		
			//Adds the record to the collection, default of Warehouse.
			if($this->checkIfQuanExists($id,1))
			{	
				//get collection_item entry
				$currentItem = CollectionItem::where("item_id",$id)					
					->where("collection_id",1)
					->first();

				$newTotal = $currentItem->quantity + $modified[$id];

				$mm = $this->c->db::table('collection_item')
					->where('item_id','=', $id)
					->where('collection_id','=', 1)
					->update(['quantity' => $newTotal]);		

				
				/*
				//Would have liked to continue using this, but it seemed to ignore the collection_id and just update the quantity on the item in all collections
				$currentItem->quantity = $newTotal;				
				$currentItem->save();*/
			}
		}

		$this->c->flash->addMessage('success', 'Stock successfully added');
		return $response->withRedirect($this->c->router->pathFor('inventory.addStock'));	
	}

	public function checkIfQuanExists($item,$coll)
	{	
		if($this->c->db::table('collection_item')
			->where('item_id','=', $item)
			->where('collection_id','=', $coll)
			->count() === 0)
		{
			return false;
		}
		return true;
	}


	//ajax call from inside add item
	public function ajax_getProps($request, $response) {
		$id = $request->getParam('id');

		$propertiesList = $this->c->helpers->getPropertyList($id);

		//$_SESSION['category'] = '';//$this->c->helpers->getType($id);

		echo json_encode($propertiesList);
	}

	//ajax call from inside edit Item
	public function ajax_getPropsItem($request, $response) {

		$type = $request->getParam('type');		
		$id = $request->getParam('id');		

		$propertiesList = $this->c->helpers->getPropertyList($type);

		$item = Item::where('item_id',$id)
		->first();

		//$_SESSION['category'] = $this->c->helpers->getType($type);

		echo json_encode(array($propertiesList, $item));
	}

}
