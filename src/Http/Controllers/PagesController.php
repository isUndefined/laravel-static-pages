<?php namespace Rts\Pages\Http\Controllers;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as IRoutingCController;
use Rts\Pages\Models\Pages;

 
class PagesController extends Controller
{
	public function PageShow($slug)
	{
		$pages = new Pages;
		$page = $pages::whereSlug($slug)->firstOrFail();
		
		$crumbs = array_reverse($pages->breadcrumbs($page->parent_id));
		
		return view('PagesView::page', compact('page','crumbs') );
	}
	
	public function index(){
		return view('PagesView::index');
	}

	public function backendPagesList(){
		$pages = Pages::orderBy('created_at','desc')->paginate(15);
		
		return view('PagesView::back.list', compact('pages'));
	}

	public function backendPageCreate(){ 
		if(\Request::has('_action')){
			return $this->submitPagesAdd();
		}
		
		$pages = Pages::select('id','header')->orderBy('created_at','desc')->get();
		
		return view('PagesView::back.create_page', compact('pages'));
	}
	
	protected function submitPagesAdd(){
		$this->middleware('csrf');
		
		if(checkI18n()){			
			$this->I18nTranslate();
		}
		
		// Валидируем входящие поля
		$v = \Validator::make(\Request::all(), Pages::$rules);
		
        if ($v->fails()){
			$renderHTML = view('errors.ajax')->with('errors',$v->errors()->first())->render();
			return response()->json( array('status' => 'error', 'msg' => $renderHTML) );
        }

		// Заполняем поля
		$pages = new Pages();
		$pages->fill(\Request::all());
		$pages->author_id = \Auth::user()->id;
		
		if(\Request::input('parent_id')){
			$parentPage = Pages::where('id',\Request::input('parent_id'))->first();
			if($parentPage){
				$pages->slug = $parentPage->slug . '/' .\Request::input('slug');
			}
		}		
		
		// Добавляем в базу
		if($pages->save()){		

			if(checkI18n()){
				$this->I18nTranslateSave($pages);
			}
		
			return response()->json( array(
				'status' => 'success', 
				'msg' => 'Page successfully added',
				'redirect'=> \url('/backend/pages')
				));  
		} else {
			return response()->json( array('status' => 'error', 'msg' => 'Something went wrong') );  
		}
	}
	
	public function backendPagesView($id){
		$page = Pages::where('id', '=', $id)->firstOrFail();
		$pages_list = Pages::select('id','header')->orderBy('created_at','desc')->get();
		
		return view('PagesView::back.page', compact('page','pages_list'));
	}

	public function backendPagesDelete($id = ''){
		if($id){
			$page = Pages::where('id',$id)->firstOrFail();
			if($page->delete()) {
				return redirect()->route('backend.pages.list')->with('status', 'Message deleted succesfully!');
			} else {
				return redirect()->back()->withErrors([
						'status' => 'System error. Please contact your administrator.',
					]);
			}
			} else {
			$pages = \Request::input('pages_id');
		}
	}
	
	public function backendPageUpdate(){
		$this->middleware('csrf');
		 
		$pages = Pages::findOrFail(\Request::input('page_id'));
		
		if(checkI18n()){			
			$this->I18nTranslate();
		}
		
		Pages::$rules['slug'] .= ',slug,'.$pages->id;
		
		// Валидируем входящие поля
		$v = \Validator::make(\Request::all(), Pages::$rules);
		
        if ($v->fails()){
			$renderHTML = view('errors.ajax')->with('errors',$v->errors()->first())->render();
			return response()->json( array('status' => 'error', 'msg' => $renderHTML) );
        }
		
		$pages->fill(\Request::all());

		if(\Request::input('parent_id')){
			$parentPage = Pages::where('id',\Request::input('parent_id'))->first();
			if($parentPage){
				$pages->slug = $parentPage->slug . '/' .\Request::input('slug');
			}
		}	
		
		if(checkI18n()){
			$this->I18nTranslateSave($pages);
		}
		
		// Обновляем в базе
		if($pages->save()){
			return response()->json( array(
				'status' => 'success', 
				'msg' => 'Page successfully updated',
				'redirect'=> \url('/backend/pages')
				));  
				
		} else {
			return response()->json( array('status' => 'error', 'msg' => 'Something went wrong') );  
		}
	}			

	public function I18nTranslate(){
		// * Get i18n inputs
		$I18nTranslate = \Request::input('I18nTranslate');
		
		$request = \Request::all();
		
		// * Replace values
		$request['header'] = (!empty($I18nTranslate[config('i18n.default')]['header']) ? $I18nTranslate[config('i18n.default')]['header'] : '');
		$request['article'] = (!empty($I18nTranslate[config('i18n.default')]['article']) ? $I18nTranslate[config('i18n.default')]['article'] : '');
		$request['excerpt'] = (!empty($I18nTranslate[config('i18n.default')]['excerpt']) ? $I18nTranslate[config('i18n.default')]['excerpt'] : '');
		
		\Request::replace($request);		
		
		// * For validation
		unset($I18nTranslate[config('i18n.default')]);
		
		if(!empty($I18nTranslate)){
			foreach($I18nTranslate as $locale=>$data){
				if(!empty($data['header']) and !empty($data['article'])){
					Pages::$rules['I18nTranslate.'.$locale.'.header'] = 'required|max:255';
					Pages::$rules['I18nTranslate.'.$locale.'.article'] = 'required';
					
					Pages::$messages['I18nTranslate.'.$locale.'.header.max'] = 'The header in '.$locale.'  may not be greater than :max.';					
				}
			}
		}
	}
	
	public function I18nTranslateSave($page){
		// * Get i18n inputs
		$I18nTranslate = \Request::input('I18nTranslate');
		
		unset($I18nTranslate[config('i18n.default')]);
		
		if(!empty($I18nTranslate)){
			foreach($I18nTranslate as $locale=>$data){
				if(!empty($data['header']) and !empty($data['article'])){
					$I18nTranslate = new \Rts\I18n\Models\I18nTranslate;
					
					if(!$i18n = $I18nTranslate::where('locale',$locale)
											->where('parent_id',$page->id)
											->where('type','page')
											->first()){
						$i18n = $I18nTranslate;						
						$i18n->locale = $locale;
						$i18n->type = 'page';
						$i18n->parent_id = $page->id;
					}
					
					$i18n->header = $data['header'];
					$i18n->article = $data['article'];
					$i18n->excerpt = (!empty($data['excerpt']) ? $data['excerpt'] : '');
					
					$i18n->save();
				}
			}
		}
	}
	
}