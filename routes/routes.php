<?php

use App\Controllers\HomeController;
use App\Controllers\DisplayController;
use App\Controllers\InventoryController;
use App\Controllers\CollectionController;
use App\Controllers\ReportsController;

use App\Controllers\Auth\AuthController;
use App\Controllers\Auth\PasswordController;

use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\PopulationMiddleware;
use App\Middleware\OldInputMiddleware;



//Cannot access when signed in.
$app->group('', function(){	
	$this->get('/auth/signin', AuthController::class . ':getSignIn')->setName('auth.signin');
	$this->post('/auth/signin', AuthController::class . ':postSignIn');
})->add(new GuestMiddleware($container));

//Can only access when signed in.
$app->group('', function(){

	$this->get('/', HomeController::class . ':index')->setName('home');
	$this->get('/admin/newUser', HomeController::class . ':getNewUser')->setName('home.newUser');
	$this->post('/admin/newUser', HomeController::class . ':postNewUser')->setName('home.postNewUser');
	$this->get('/admin/editUser/{id}', HomeController::class . ':getEditUser')->setName('home.editUser');
	$this->post('/admin/editUser', HomeController::class . ':postEditUser')->setName('home.postEditUser');
	$this->get('/admin/manageUsers', HomeController::class . ':getManageUsers')->setName('home.manageUsers');
	$this->post('/admin/manageUsers', HomeController::class . ':postManageUsers')->setName('home.postManageUsers');
	$this->post('/admin/deleteUser', HomeController::class . ':ajax_deleteUser')->setName('home.ajax_deleteUser');

	$this->get('/auth/signout', AuthController::class . ':getSignOut')->setName('auth.signout');
	
	$this->get('/inventory', InventoryController::class . ':index')->setName('display');
	$this->get('/inventory/add', InventoryController::class . ':getAdd')->setName('inventory.add');
	$this->post('/inventory/add', InventoryController::class . ':postAdd')->setName('inventory.add.post');
	$this->get('/inventory/addStock', InventoryController::class . ':getAddStock')->setName('inventory.addStock');
	$this->post('/inventory/addStock', InventoryController::class . ':postAddStock')->setName('inventory.addStock.post');
	
	$this->get('/inventory/addType', InventoryController::class . ':getAddItemType')->setName('inventory.addType');
	
	$this->post('/inventory/addType', InventoryController::class . ':postAddItemType')->setName('inventory.addType.post');

	$this->post('/inventory/edit', InventoryController::class . ':postEdit')->setName('inventory.edit.post');

	$this->post('/inventory/ajax', InventoryController::class . ':ajax_getProps')->setName('inventory.ajax');
	$this->post('/inventory/ajaxItem', InventoryController::class . ':ajax_getPropsItem')->setName('inventory.ajaxItem');

	$this->get('/inventory/{id}', InventoryController::class . ':viewCategory')->setName('display.category');
	$this->get('/inventory/edit/{id}', InventoryController::class . ':getEditItem')->setName('inventory.edit.item');




	$this->get('/collections', CollectionController::class . ':index')->setName('collections');
	$this->post('/collections', CollectionController::class . ':indexPost')->setName('collections.post');
	
	$this->get('/collections/new', CollectionController::class . ':getAdd')->setName('collections.add');
	$this->post('/collections/new', CollectionController::class . ':postAdd')->setName('collections.add.post');

	$this->get('/collections/edit', CollectionController::class . ':getUpdate')->setName('collections.update');
	$this->post('/collections/edit', CollectionController::class . ':postUpdate')->setName('collections.update.post');
	
	$this->post('/collections/transfer', CollectionController::class . ':postTransfer')->setName('collections.transfer.post');	


	$this->get('/collections/archive/{id}', CollectionController::class . ':archive')->setName('collections.archive');
	$this->post('/collections/archive', CollectionController::class . ':postarchive')->setName('collections.archive.post');

	
	$this->post('/collections/ajaxOtherQuan', CollectionController::class . ':ajaxOtherQuan')->setName('collections.ajaxOtherQuan');	
	$this->post('/collections/ajaxPrevSales', CollectionController::class . ':ajaxPrevSales')->setName('collections.ajaxPrevSales');	
	$this->get('/collections/{id}', CollectionController::class . ':collection')->setName('collections.view');
	

	$this->get('/reports', ReportsController::class . ':index')->setName('reports');
	$this->get('/reports/all', ReportsController::class . ':getAllInventory')->setName('reports.all');
	$this->get('/reports/coll', ReportsController::class . ':getCollInventory')->setName('reports.coll');
	$this->post('/reports/coll', ReportsController::class . ':postCollInventory')->setName('reports.coll.post');
	$this->post('/reports/sold', ReportsController::class . ':postSold')->setName('reports.sold');


	$this->post('/search', HomeController::class . ':search')->setName('search');

	


})->add(new AuthMiddleware($container))->add(new PopulationMiddleware($container))->add(new OldInputMiddleware($container));

