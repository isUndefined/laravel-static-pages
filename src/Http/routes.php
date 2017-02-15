<?php	

Route::get('/page/{slug}', [
	'as' => 'backend.pages.list',
	'uses' => 'Rts\Pages\Http\Controllers\PagesController@PageShow',
])->where('slug','[A-Za-z0-9_/-]+');
	
	
Route::group(['prefix' => 'backend', 'middleware'=> 'auth', 'namespace' => 'Rts\Pages\Http\Controllers'], function () {

	Route::get('/pages', [
		'as' => 'backend.pages.list',
		'middleware' => ['permission:page.add','permission:page.edit','permission:page.delete'],
		'uses' => 'PagesController@backendPagesList',
	]);
	Route::get('/page/new', [
		'as' => 'backend.pages.create', 
		'middleware' => 'permission:page.add',
		'uses' => 'PagesController@backendPageCreate',
	]);
	Route::post('/page/new', [
		'as' => 'backend.pages.create', 
		'middleware' => 'permission:page.add',
		'uses' => 'PagesController@backendPageCreate',
	]);
	Route::get('/page/{id}', [
		'as' => 'backend.Pages.view',
		'middleware' => ['permission:page.add','permission:page.edit','permission:page.delete'],
		'uses' => 'PagesController@backendPagesView',
	])->where('id','[0-9]+');
	
	Route::get('/page/delete/{id}', [
		'as' => 'backend.Pages.delete',
		'middleware' => 'permission:page.delete',
		'uses' => 'PagesController@backendPagesDelete',
	])->where('id','[0-9]+');
	
	Route::post('/page/delete', [
		'as' => 'backend.Pages.delete',
		'middleware' => 'permission:page.delete',
		'uses' => 'PagesController@backendPagesDelete',
	]);
	Route::post('/page/update', [
		'as' => 'backend.page.update',
		'middleware' => ['permission:page.edit','ajax'],
		'uses' => 'PagesController@backendPageUpdate',
	]);
	
	Route::post('/pages/edit/{id}', [
		'as' => 'backend.Pages.send_reply',
		'middleware' => 'permission:page.edit',
		'uses' => 'PagesController@backendPagesendReply',
	])->where('id','[0-9]+');
	
});

