<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

class Controller_Photos extends Controller_System_Backend {

	public function before()
	{
		parent::before();
		$this->breadcrumbs
			->add(__('Gallery'), Route::get('backend')->uri(array('controller'=>'photos')));
		
		Assets::package(array('jquery-ui'));
	}
	
	public function action_index()
	{
		$category_id = (int) $this->request->param('id');
		
		$category = ORM::factory('photo_category');

		if($category_id > 0) 
		{
			$category = ORM::factory('photo_category', $category_id);
			if( !$category->loaded())
			{
				throw new HTTP_Exception_404('Category bot found');
			}
			
			$parents = $category->get_parents();
			
			foreach ($parents as $parent)
			{
				$this->breadcrumbs
					->add($parent->title, URL::backend('photos/category/'.$parent->id));
			}
		}
		
		$categories = ORM::factory('photo_category')->where('parent_id', '=', $category_id)->find_all()->as_array('id');
		
		if($category_id > 0 AND ! $this->request->is_iframe()) 
		{
			$categories[0] = ORM::factory('photo_category')->values(array(
				'title' => __('Level up'),
				'parent_id' => $category->parent_id
			));
			
			ksort($categories);
		}
		
		$photos = ORM::factory('photo')
			->filter_by_category($category_id);

		$this->template->title = __('Photos');
		$this->template->content = View::factory( 'photos/index', array(
			'category_id' => $category_id,
			'photos' => $photos->find_all(),
			'categories' => $categories,
			'category' => $category
		) );
	}
	
	public function action_edit()
	{
		$id = (int) $this->request->param('id');
		$photo = ORM::factory('photo', $id);
	}
}