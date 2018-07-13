<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Items\Item;
use App\Models\Items\ItemType;
use App\Models\Items\allowableProperties;
use App\Models\Items\Property;
use App\Models\Collections\ItemCollection;
use App\Models\Sold\soldItem;
use Respect\Validation\Validator as v;

class CollectionController extends Controller
{
	public function index($request, $response)
	{

		$items = Item::with('itemType')
		->orderBy('itemType_id','asc')
		->get();
			
		$propertiesList = $this->c->helpers->getPropertiesList();		

		return $this->c->view->render($response, 
			'Collections\collections.twig', 
			compact('items', 'propertiesList'));	
	}

	public function indexPost($request, $response)
	{		
		$id = $request->getParam('collections');

		$coll = array('id' => $id);

		return $response->withRedirect($this->c->router->pathFor('collections.view', $coll));
	}

	public function collection($request, $response, $args)
	{
		$id = $args;

		//if it's 0, ALL was chosen
		if($id['id']==0){
			return $response->withRedirect($this->c->router->pathFor('collections'));
		}

		//check to see if collection exists
		if(ItemCollection::where('collection_id', $id)->count() === 0){
			$this->c->flash->addMessage('error', 'Collection with that ID doesn\'t exist');
			return $response->withRedirect($this->c->router->pathFor('collections'));
		}

		$items = $this->c->db::table('items AS i')
			->join('collection_Item AS ci','i.item_id','=','ci.item_id')
			->join('collections AS c','ci.collection_id','=','c.collection_id')
			->join('itemTypes AS it','i.itemType_id','it.id')
			->where('ci.collection_id',$id)
			->orderBy('itemType_id','asc')
			->orderBy('i.item_id','asc')
			->get();

		if($items->count() ===0 )
			{
				return $this->c->view->render($response, 'Collections\collection.twig');					
			}

		$propertiesList = $this->c->helpers->getPropertiesList();		
		
		$quantities = $this->c->helpers->getQuantities($items, $id, false);
		

		$collectionTitle = ItemCollection::
			where('collection_id',$id)
			->first()
			->collection;

		return $this->c->view->render($response, 
			'Collections\collection.twig', 
			compact('items','propertiesList','id','quantities','collectionTitle'));	

	}

	public function getAdd($request, $response)
	{		
		return $this->c->view->render($response, 'Collections\Add.twig');	
	}

	public function postAdd($request, $response)
	{
		$validation = $this->c->validator->validate($request, [
			'collectionName' => v::notEmpty()->CollectionUnique(),
		]);

		if ($validation->failed()) {

			$this->c->flash->addMessage('error', 'Collection \''. $request->getParam('collectionName') .'\' Already Exists');
			return $response->withRedirect($this->c->router->pathFor('collections.add'));
		}

		$protected=0;
		if($request->getParam('protected')=="true"){
			$protected=1;		   
		}

/*		$id = $this->c->db::table('collections')->insertGetId([
		    [
		    'collection' => $request->getParam('collectionName'),
		    //'protected' => $protected
		    ],
		]);*/

		$coll = ItemCollection::Create
		([			
			'collection' => $request->getParam('collectionName'),
			'protected' => $protected
		]);

		$this->c->flash->addMessage('info', 'New Collection '.$request->getParam('collectionName').' added successfully');

		
		return $response->withRedirect($this->c->router->pathFor('collections.view',array('id'=>1)));
	}

	public function getUpdate($request, $response)
	{				
		return $this->c->view->render($response, 'Collections\Update.twig');	
	}

	public function postUpdate($request, $response)
	{
		
	/* 	if both change - run validation and update
		if protected only changes - no validation but update
		if name only changes - validation and update
		if neither change - no validation no update */
		
		$nameChanged = false;
		$protChanged = false;
		$newName = $request->getParam('name');
		$protected = 0;
		
		//check protected and see if that's all that was changed to account for needing to run update but not change name to change protected status
		
		if($request->getParam('protected') === 'on') { $protected =1; }			
		
		if($request->getParam('origName') !== $newName){ $nameChanged = true; }

		if($request->getParam('origProt') != $protected) { $protChanged = true; }

		//decide whether to do validation 
		if($nameChanged && $protChanged) {
			$validation = $this->c->validator->validate($request, [
			'name' => v::notEmpty()->CollectionUnique(),
			]);

			if ($validation->failed()) {

				$this->c->flash->addMessage('error', 'Collection Exists');
				return $response->withRedirect($this->c->router->pathFor('collections.update'));
			}
		} elseif($nameChanged) {
			$validation = $this->c->validator->validate($request, [
			'name' => v::notEmpty()->CollectionUnique(),
			]);

			if ($validation->failed()) {

				$this->c->flash->addMessage('error', 'Collection Exists');
				return $response->withRedirect($this->c->router->pathFor('collections.update'));
			}

		} elseif(!$protChanged && !$nameChanged) {

			$this->c->flash->addMessage('info', 'No changes detected; Collection not modified');
			
			return $response->withRedirect($this->c->router->pathFor('collections.update'));
		}		

		$id = $request->getParam('collection'); 
		
		$collection = ItemCollection::where(
			'collection_id',$request->getParam('collection'))
			->update(['collection' => $newName,'protected' => $protected]
		);
		
		
		$this->c->flash->addMessage('success', 'Collection Updated');
		return $response->withRedirect($this->c->router->pathFor('collections'));	
	}


public function postTransfer($request, $response)
	{
		

		$source = $request->getParam('source');
		$destination = $request->getParam('destination');

		//extracts the list of items that have been changed
		$updated = array();
		foreach($request->getParsedBody() as $k=>$v){
			if(is_numeric($k) &&  $v > 0){
				$updated[$k] = $v;
			}
		}	
				
		foreach($updated as $k=>$v){
			
			//remove items from collection source - moved out from if because will always need it.
			$srcDec = $this->decrement($source,$k,$v);

			//if not marked as sold, sold is 0 value
			if($destination > 0) {
				try {					
					//add it to collection destination
					$destInc = $this->increment($destination,$k,$v);
				} catch(Exception $e) {
					console.log($e->getMessage());
					//flash with error
					$this->c->flash->addMessage('error', 'Error Occured: '.$e->getMessage());
					//send back to collection view page from whence they came
					return $response->withRedirect($this->c->router->pathFor('collections.view',array('id'=>$source)));
				}							
			}		
		}
			
			//It's ugly that i had to do this if statement twice, but i couldn't find a way around it.
		if($destination > 0) {
			$coll = ItemCollection::where('collection_id',$destination)->first()->collection;

			$this->c->flash->addMessage('info', 'Items have been transferred to '.$coll);
			
			return $response->withRedirect($this->c->router->pathFor('collections.view',array('id'=>1)));			
		}
		
		$info = [];
		//now i can pass new and info to the markSold f
		if(empty($request->getParam('client'))) {
			$info['new'] = false;
			$info['existingClient'] = $request->getParam('sales');
		} else {
			$info['new'] = true;
			$info['client'] = $request->getParam('client');
			$info['notes'] = $request->getParam('notes');			
		}				
		
		
		//call markAsSold 
		$sold = $this->markAsSold($source, $info, $updated);
		
		if($sold) {
			$this->c->flash->addMessage('info', 'Items have been successfully marked as sold');
		}

		return $response->withRedirect($this->c->router->pathFor('collections.view',array('id'=>1)));
	}


	public function archive($request, $response, $args)
	{
/*		echo "<pre>";
		var_dump($_SESSION['old']);
		echo "</pre>";*/

		$id=$args;

		$items = $this->c->db::table('items AS i')
			->join('collection_Item AS ci','i.item_id','=','ci.item_id')
			->join('collections AS c','ci.collection_id','=','c.collection_id')
			->join('itemTypes AS it','i.itemType_id','it.id')
			->where('ci.collection_id',$id)
			->orderBy('itemType_id','asc')
			->orderBy('i.item_id','asc')
			->get();

		$collectionTitle = ItemCollection::where('collection_id',$id)
			->first()->collection;

		if($items->count() === 0)
		{
			return $this->c->view->render($response, 'Collections\archive.twig', 
			compact('id','collectionTitle'));					
		}
		
		$propertiesList = $this->c->helpers->getPropertiesList();		
		
		$quantities = $this->c->helpers->getQuantities($items, $id, false);			

		return $this->c->view->render($response, 
			'Collections\archive.twig', 
			compact('items','propertiesList','id','quantities', 'collectionTitle'));	
	}

	public function postArchive($request, $response)
	{
		$source=$request->getParam('source');		
				/* foreach items add this item quantity into sold_items */
							
		//get items sold into an array
		$items = array();
		//check to see if any items marked as sold
		$itemsSold = false;
		foreach($request->getParsedBody() as $k=>$v)
		{
			if(is_numeric($k))
			{
				$items[$k]=$v;

				//if at least one item has a non 0, ie items marked as sold is not 0, has items is true
				$v > 0 ? $itemsSold = true : '' ;				
			}
		}
		
		//if items, mark them as sold.
		if($itemsSold)  
		{
	
			$info = [];
			//now i can pass new and info to the markSold 
			if(empty($request->getParam('client'))) {
				$info['new'] = false;
				$info['existingClient'] = $request->getParam('sales');
			} else {
						//validation
				$validation = $this->c->validator->validate($request, [
					'client' => v::notEmpty(),
				]);

				if ($validation->failed()) {
					$this->c->flash->addMessage('error', 'Fix Errors');
					return $response->withRedirect($this->c->router->pathFor(
						'collections.archive',
						['id'=>$request->getParam('source')]
					));
				}
			
				$info['new'] = true;
				$info['client'] = $request->getParam('client');
				$info['notes'] = $request->getParam('notes');			
			}					
			
			//call markAsSold
			 $addedToSold = $this->markAsSold($source, $info, $items);	
				
		} else {
			//handles the event that no items are marked as sold, but we want the increment and decrement to still occur
			$addedToSold = true;
		} //end of if items is not empty, ie some items were marked as sold

		$quantities = $this->c->helpers->getQuantitiesSingle(array_keys($items),$source, false);

		//itemsLeft will be used for archive to add these back to the warehouse
		$itemsLeft = array();		

		foreach ($items as $k=>$v)
		{
			//work out items remaining
			$itemsLeft[$k]=$quantities[$k]-$items[$k];					
			
			//make sure added to sold was successful and increment and decrement accordingly
			if($addedToSold){
				//add items left back to warehouse
				$this->increment(1,$k,$itemsLeft[$k]);
				//subtract items from source collection
				//$this->decrement($source,$k,$quantities[$k]);
			}			
		}			

		$collection = ItemCollection::where('collection_id',$source)->first();
		 
		//archive collection
		//delete items from collection_Item
		$this->c->db::delete
			("delete from collection_item 
				where collection_id = '".$source."'"
			);			
		//create archive collection		
		$this->c->db::table('archived_collections')->insert([
		    [
		    'collection_id' => $collection->collection_id,
		    'collection' => $collection->collection,
		    'protected' => $collection->protected 
		    ], 
		]);		

		$collection->delete();
	
		$this->c->flash->addMessage('info', $collection->collection . ' has been archived.');
		
		return $response->withRedirect($this->c->router->pathFor('collections.view',array('id'=>1)));	
	}

		
	public function markAsSold($coll_id, $info, $items)
	{
		  /* gathering client
		  creating entry in sold, gets id entry back, or uses existing sold id
		  uses this to add items to sold_items */
		 //$collection = ItemCollection::where('collection_id',$coll_id)->first();
		 		 
		if($info['new']) {
			//creates entry in sold for the client, notes etc, then uses id created for sold_items
			$IDSold = $this->c->db::table('sold')
				->insertGetId([										
					'client' => $info['client'],
					'collection_id' => $coll_id,
					'notes'=> $info['notes']
				]);
		} else { //existing
			$IDSold = $info['existingClient']; 			
		}

		foreach ($items as $k=>$v) {
			//if the collection being archived has items with no quantity being sold, then there is no point in making the entry.
		
			if($v != 0) {
				//check to see if that item already exists in this sold entry and update or create it with quantity of 0 accordingly
				$exists =  soldItem::firstOrCreate( 
					['sold_id' => $IDSold, 'item_id' => $k],
					['quantity'=> 0]
				);

				//now that I know the entry exists, add the quantity (which may be 0 or not) to the amount I marked as sold
				$addedToSold = $exists->update(
					['quantity'=> ($exists->quantity + $v)]
				 );
			}			
		}

		return $addedToSold; 	
	}

	
	private function decrement($source,$k,$v)
	{
		return $this->c->db::table('collection_item')
				->where([
				    ['collection_id', '=', $source],
				    ['item_id', '=', $k],
				])
				->decrement('quantity', $v);
	}

	private function increment($destination,$k,$v)
	{
		//Check to see if the item is already in the collection. If not, add it.
		if($this->c->db::table('collection_item')
			->where([
			    ['collection_id', '=', $destination],
			    ['item_id', '=', $k],
			])->count() === 0)
			{
				$this->c->db::table('collection_item')
				->insert([
					['item_id' => $k,'collection_id' => $destination ],   
					  
				]);
			}

			//increment the destination	
		return $this->c->db::table('collection_item')
				->where([
				    ['collection_id', '=', $destination],
				    ['item_id', '=', $k],
				])
				->increment('quantity', $v);	
	}

	public function ajaxOtherQuan($request, $response){
		$items = $this->c->db::table('collection_item AS ci')
			->join('collections AS c','ci.collection_id','=','c.collection_id')
			->where('item_id', '=', $request->getParam('id'))
			->get();
		$quantities = array();
		foreach($items as $item)
		{
			$quantities[$item->collection]=$item->quantity;
		}	

		echo json_encode($quantities);
	}

	public function ajaxPrevSales($request, $response){
		
		if($request->getParam('all') == "true") {

			$entries = $this->c->db::table('sold')
				->orderBy('created_at','desc')
				->get();

		} else {

			$entries = $this->c->db::table('sold')
				->orderBy('created_at','desc')
				->limit(10)
				->get();
		}

		echo json_encode($entries);
	}

}
