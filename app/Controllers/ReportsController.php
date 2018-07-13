<?php

namespace App\Controllers;

use App\Models\User;

use App\Models\Items\Item;
use App\Models\Items\ItemType;
use App\Models\Items\allowableProperties;
use App\Models\Items\Property;

use App\Models\Sold\soldEntry;
use App\Models\Sold\soldItem;

use App\Models\Collections\ItemCollection;

use Respect\Validation\Validator as v;

class ReportsController extends Controller
{
	public function index($request, $response)
	{
		//Setup for last 3 months of sold reports
		//last 3 months inc current, name and number and year
		$TwoMonth['name'] = date("F", strtotime("first day of -2 month"));
		$TwoMonth['num'] = date("m", strtotime("first day of -2 month"));
		$TwoMonth['year'] = date("Y", strtotime("first day of -2 month"));
		$LastMonth['name'] = date("F", strtotime("first day of previous month"));
		$LastMonth['num'] = date("m", strtotime("first day of previous month"));
		$LastMonth['year'] = date("Y", strtotime("first day of previous month"));
		$thisMonth['name'] = date("F");
		$thisMonth['num'] = date("m");
		$thisMonth['year'] = date("Y");

		return $this->c->view->render($response, 'Reports\reports.twig', compact('TwoMonth','LastMonth','thisMonth'));	
	}

	public function getAllInventory($request, $response) {

			$items = Item::with('itemType')
			->orderBy('itemType_id','asc')
			->get();
			
			//$items = itemType::with('Item')->get();
			$propertiesList = $this->c->helpers->getPropertiesList();	

			$quantities = $this->c->helpers->getQuantitiesByCollection($items);
/*
			echo "<pre>";
			var_dump($quantities);
			echo "</pre>";
			die();*/


		return $this->c->view->render($response, 'Reports\all.twig', compact('items', 'propertiesList', 'quantities'));	
	}


	public function postAllInventory($request, $response) {
		
		$collections = ItemCollection::all()->pluck("collection_id");
		
		$items = $this->c->db::table('items AS i')
			->join('collection_Item AS ci','i.item_id','=','ci.item_id')
			->join('collections AS c','ci.collection_id','=','c.collection_id')
			->join('itemTypes AS it','i.itemType_id','it.id')
			->whereIn('ci.collection_id',$collections)
			->orderBy('c.collection_id','asc')
			->orderBy('i.itemType_id','asc')
			->get();

		$propertiesList = $this->c->helpers->getPropertiesList();	
/*			echo "<pre>";
			var_dump($quantities);
			echo "</pre>";
die();*/
		
		return $this->c->view->render($response, 'Reports\display.twig', compact('items', 'propertiesList', 'quantities'));	
	}

	public function getCollInventory($request, $response) {

		return $this->c->view->render($response, 'Reports\Collections.twig', compact(''));	
	}
	
	public function postCollInventory($request, $response) {
		
		$collections = array();
		foreach($_POST as $k=>$v){
			$collections[]=$v;			
		}		

		$items = $this->c->db::table('items AS i')
			->join('collection_Item AS ci','i.item_id','=','ci.item_id')
			->join('collections AS c','ci.collection_id','=','c.collection_id')
			->join('itemTypes AS it','i.itemType_id','it.id')
			->whereIn('ci.collection_id',$collections)
			->orderBy('c.collection_id','asc')
			->get();
		

		$propertiesList = $this->c->helpers->getPropertiesList();	

		$quantities = $this->c->helpers->getQuantities($items,$collections, true);
			/*echo "<pre>";
			print_r($quantities);
			echo "</pre>";*/
		return $this->c->view->render($response, 'Reports\display.twig', compact('items', 'propertiesList', 'quantities'));	
	}


	public function postSold($request, $response) {
		//There has to be a better way of doing this...
		if($request->getParam("TwoMonth")){
			$month = $request->getParam("TwoMonth");
		} else if($request->getParam("LastMonth")){
			$month = $request->getParam("LastMonth");
		} else if($request->getParam("thisMonth")){
			$month = $request->getParam("thisMonth");
		}

		/*$sold = $this->c->db::table('sold AS s')
			->join('sold_items AS si', 's.id','=','si.sold_id')
			->join('items AS i', 'si.item_id','=','i.item_id')
			->join('itemtypes AS it', 'i.itemType_id','=','it.id')
			//->where('s.created_at','LIKE',$month.'%')
			->orderBy('s.id')
			->orderBy('si.item_id')
			//->select('s.*',)
			->get();*/

		$sold = $this->c->db::table('sold AS s')
			->join('sold_items AS si', 's.id','=','si.sold_id')
			->join('items AS i', 'si.item_id','=','i.item_id')
			->join('itemtypes AS it', 'i.itemType_id','=','it.id')
			->where('s.created_at','LIKE',$month.'%')
			->orderBy('s.id')
			->orderBy('si.item_id')
			->select('s.*','i.item_id','i.desc','si.quantity', 'it.itemType')
			->get();

			/*$sold->each(function ($s) {
				echo 'Sold ID: '.$s->id .' - Item ID: '.  $s->item_id .' - Item ID: '.  $s->created_at . '<br />';
			});*/

/*
			echo "<pre>";
			//print_r($sold);
			print_r($sold[0]);
			echo "</pre>";
			die();*/


		return $this->c->view->render($response, 'Reports\sold.twig', compact('sold','month'));	

	}


}
