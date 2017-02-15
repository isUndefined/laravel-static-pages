<?php namespace Rts\Pages;
 
use Illuminate\Support\ServiceProvider;
 
class PagesServiceProvider extends ServiceProvider
{
		public function boot(){
		
		$this->publishes([
            __DIR__ . '/migrations/' => $this->app->databasePath() . '/migrations'
        ], 'pages_migrations');
		
		$this->loadViewsFrom( __DIR__ . '/views', 'PagesView');
		
		require __DIR__ . '/Http/routes.php';
	}

	
	public function register(){
		$this->app->bind('Pages', function () {
			return new Pages;
		});
	}
 
}