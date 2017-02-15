<?php namespace Rts\Pages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use \DB;
use \Rts\I18n\Models\I18nTranslate;

class Pages extends Model {

	protected $table = 'pages';
	
    //Делаем поля доступными для автозаполнения
    protected $fillable = array('header', 'slug', 'excerpt', 'article', 'parent_id', 'author_id', 'published' , 'thumbnail');
	
	protected $errors;
	
	protected $translatable = array('header','article','excerpt');
	
	protected $translateModel = array();
	
	public static $messages = array();
	
	//Некоторые правила валидиции
    public static $rules = array(
        'header' => 'required|max:255',
        'slug' => 'required|between:2,50|unique:posts',
        'article' => 'required'		
    );
	
	public function __get($key) {
	 	if(in_array($key,$this->translatable)){
			$currentLocale = app()->getLocale();

			if($currentLocale!=config('i18n.default')){
				if(empty($this->translateModel)){
					$this->translateModel = $this->postLocale($currentLocale,true);
				}
				$this->attributes[$key]	= $this->translateModel->$key;
			}
		} 
		return $this->getAttribute($key);
	}
	
	public function thumb($img = null){
		if($img && file_exists(public_path().$img)){
			$file = pathinfo(public_path().$img);
			
			return (object)[
				'name'=>$file['basename'],
			];
		} 
		return false;
	}
	
	public function thumbBySize($size){
		return str_replace('/storage/topic/thumbs','/thumbnails/topic/'.$size, $this->attributes['thumbnail']);
	}
	
	public function author(){
		return \App\User::find($this->attributes['author_id']);
	}
	
	public function parentCategory(){
		return $this->hasOne('\Rts\Pages\Models\Pages','parent_id','id');
	}
	
	public function getShortSlugAttribute(){
		$aSlugs = explode('/',$this->attributes['slug']);
		return end($aSlugs);		
	}
		
	public function postLocale($locale, $replaceWithDefault = false){
		
		if(!in_array($locale, array_keys(config('i18n.locales')))){
			return false;
		}
		
		if (!$i18n = I18nTranslate::where('locale',$locale)
											->where('parent_id', $this->attributes['id'])
											->where('type','page')
											->first()){
				if($replaceWithDefault or $locale == config('i18n.default')){
					$i18n = DB::table($this->table)->where('id',$this->attributes['id'])->first();
				}
				
			}
			
		return $i18n;
	}
	
	public function breadcrumbs($id=0){
		$pages = self::get()->keyBy('id')->toArray();	
		$pages = $this->build_crumbs($pages, $id);
		if(checkI18n()){
			if(!empty($pages)){
				$pagesKeys = array_keys($pages);
				$groupTransItems = I18nTranslate::whereIn('parent_id',$pagesKeys)->where('type','page')->where('locale', app()->getLocale())->get()->keyBy('parent_id');
				if(!empty($groupTransItems)){
					foreach($pages as $k=>$page){
						if(isset($groupTransItems[$k])){
							$pages[$k]['header'] = $groupTransItems[$k]['header'];
						}
					}
				}				
			}
		}
		return $pages;
	}
	
	public function build_crumbs($pages, $parent = null, $level = 0){
		$return = array();
		if(isset($pages[$parent]))
		{
			$page = $pages[$parent];
			$return[$page['id']] = $page;
			
			if(!empty($page['parent_id'])){
				$return += $this->build_crumbs($pages, $page['parent_id'], $level+1);				
			}
		}
		return $return;
	}
	
}
	