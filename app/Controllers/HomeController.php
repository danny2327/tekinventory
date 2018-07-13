<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Collections\ItemCollection;
use App\Models\Items\Item;
use App\Models\Items\CollectionItem;
use App\Models\Items\ItemType;
use App\Models\Items\allowableProperties;
use App\Models\Items\Property;
use Respect\Validation\Validator as v;

class HomeController extends Controller
{
	public function index($request, $response)
	{		
		//$i = new Item($this->c);
		/*$items = Item::test();
		foreach ($items as $item) {		
			echo $item->desc."<br />";
		}*/
		
		return $this->c->view->render($response, 'home.twig', compact(''));	
	}

	public function search($request, $response)
	{
		$searchIn = $request->getParam("selectSearch");
		$searchTerm = strtoupper($request->getParam("search"));

		//if ($searchIn=="all") {
			$items = Item::where(strtoupper('desc'),'LIKE','%'.$searchTerm.'%')
					->orwhere(strtoupper('attrib_1'),'LIKE','%'.$searchTerm.'%')
					->orWhere(strtoupper('attrib_2'),'LIKE','%'.$searchTerm.'%')
					->orWhere(strtoupper('attrib_3'),'LIKE','%'.$searchTerm.'%')
					->orWhere(strtoupper('attrib_4'),'LIKE','%'.$searchTerm.'%')
					->OrderBy('itemType_id','asc')
					->get();
		/*} else {

		}*/

		if($items->count() === 0){
				$this->c->flash->addMessage('error', 'No results for "'.$searchTerm.'"');
				return $this->c->view->render($response, 'home.twig');
		}		
		
		$propertiesList = $this->c->helpers->getPropertiesList();	

		$quantities = $this->c->helpers->getQuantities($items,0,false);
		//print_r($this->c->flash->getMessage('info'));
		$this->c->flash->addMessage('info', 'Showing results for "'.$searchTerm.'"');
		$flash = $_SESSION['slimFlash'];

		return $this->c->view->render($response, 'Display\display.twig', compact('items', 'propertiesList', 'quantities'));		 
	}

	public function getNewUser($request, $response, $args) 
	{

		return $this->c->view->render($response, 'users\new_user.twig', compact(''));
	}

	public function postNewUser($request, $response)
	{

		$validation = $this->c->validator->validate($request, [
			'name' => v::notEmpty(),
			'email' => v::notEmpty()->noWhiteSpace()->endsWith('@tekworks.ca')->EmailAvailable(),
		]);

		if ($validation->failed()) {			
			
			return $response->withRedirect($this->c->router->pathFor('home.newUser'));
		}

		$isAdmin = ($request->getParam('admin') ? 1 : 0);

		User::Create
		([
			'name' => $request->getParam('name'),
			'email' => $request->getParam('email'),
			'isAdmin' => $isAdmin,
		]);

		
		$this->c->flash->addMessage('info', 'User '.$request->getParam('name').' successfully created' );
		return $response->withRedirect($this->c->router->pathFor('home.manageUsers'));

	}

	public function getEditUser($request, $response, $args) 
	{	

		$id = $args;
		$user = User::find($id);
		
		if(count($user) == 0) {
			$this->c->flash->addMessage('info', 'User does not exist' );
			return $response->withRedirect($this->c->router->pathFor('home.manageUsers'));
		}

		//Not sure why it's returning a collection of 1 user here...
		$user = $user[0];
		
		return $this->c->view->render($response, 'users\profile.twig', compact('user'));
	}

	public function postEditUser($request, $response)
	{
		/*echo "<pre>";
		var_dump($_POST);
		echo "</pre>";
		die();*/



		//if email was not changed, don't check if it's available.
		if($_POST['origEmail'] === $_POST['email'] ) {
			$validation = $this->c->validator->validate($request, [
				'name' => v::notEmpty(),
				'email' => v::notEmpty()->noWhiteSpace()->endsWith('@tekworks.ca'),
			]); 
		} else {
			$validation = $this->c->validator->validate($request, [
			'name' => v::notEmpty(),
			'email' => v::notEmpty()->noWhiteSpace()->endsWith('@tekworks.ca')->EmailAvailable(),
		]);
		}

		if ($validation->failed()) {

			$this->c->flash->addMessage('error', 'Ensure email is not already in use (Check Manage Users page), and that it is typed correctly, with no whitespace.');
			
			return $response->withRedirect($this->c->router->pathFor('home.editUser',['id'=>$request->getParam('id')]
			));
		}

		$isAdmin = ($request->getParam('admin') ? 1 : 0);
		
		User::where('id',$request->getParam('id'))
		->update
		([
			'name' => $request->getParam('name'),
			'email' => $request->getParam('email'),
			'isAdmin' => $isAdmin,
		]);

		$this->c->flash->addMessage('info', 'User '.$request->getParam('name').' successfully updated');

		return $response->withRedirect($this->c->router->pathFor('home.manageUsers'));
	}

	public function getManageUsers($request, $response, $args) 
	{
		$users = User::all();

		return $this->c->view->render($response, 'users\manage_users.twig', compact('users'));
	}

	public function postManageUsers($request, $response)
	{

	}

	public function ajax_deleteUser($request, $response) 
	{
		$id = $request->getParam('id');

		$user = User::find($id);

		$user->delete();

		echo json_encode(true);

	}

		
}
