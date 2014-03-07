<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Widget_Gallery_Categories extends Model_Widget_Decorator_Pagination {
	
	public $cache_tags = array('gallery');

	public function get_current_category_id()
	{		
		$category_id = (int) $this->get('category_id', 0);
		
		$query = DB::select('id')
			->from('photo_categories')
			->limit(1);

		$category_path = $this->_ctx->get('.category_path');
		if( ! empty($category_path))
		{
			$query->where('path', '=', $category_path);
		}
		else
		{
			$query->where('id', '=', $category_id);
		}

		return $query->execute()->get('id');
	}
	
	public function get_categories()
	{
		return ORM::factory('photo_category')
			->where('image', '!=', '')
			->where('parent_id', '=', (int) $this->get_current_category_id());
	}

	public function count_total()
	{
		return $this->get_categories()->reset(FALSE)->count_all();
	}
	
	public function fetch_data()
	{
		$category_id = $this->get_current_category_id();
		$category = ORM::factory('photo_category', $category_id);

		$photos = ORM::factory('photo')
			->where('category_id', '=', $category_id)
			->where('type', '=', Model_Photo::TYPE_IMAGE)
			->find_all()
			->as_array();
		
		$videos = ORM::factory('photo')
			->where('category_id', '=', $category_id)
			->where('type', '=', Model_Photo::TYPE_VIDEO)
			->find_all()
			->as_array();

		return array(
			'categories' => $this->get_categories()
				->limit($this->list_size)
				->offset($this->list_offset)
				->order_by('id', 'desc')
				->find_all()
				->as_array(),
			'photos' => $photos,
			'videos' => $videos,
			'category' => $category,
		);
	}
	
	public function backend_data()
	{
		$categories = ORM::factory('photo_category')->find_all()->as_array('id', 'title');
		$categories[0] = __('No category');

		return array(
			'categories' => $categories,
		);
	}
	
	public function set_values(array $data)
	{
		if( ! empty( $data['category_id'] ))
		{
			$this->category_id = (int) $data['category_id'];
		}

		return parent::set_values($data);
	}
	
	public function get_cache_id()
	{
		$request = $this->_ctx->request();
		$path = $this->_ctx->get('.category_path');
		$query = 1;
		if($request instanceof Request)
		{
			$query = (int) $request->query('page');
		}

		return 'Widget::' . $this->id . '::' . $path . '::' . $query;
	}
}