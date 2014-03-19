<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Widget_Gallery_Categories extends Model_Widget_Decorator_Pagination {
	
	public $cache_tags = array('gallery');
	
	public $id_ctx = 'category_id';
	public $path_ctx = '.category_path';

	public function get_current_category_id()
	{		
		$category_id = $this->_ctx->get($this->id_ctx);
		
		if($category_id === NULL)
		{
			$category_id = $this->get('category_id', 0);
		}

		$category_path = $this->_ctx->get($this->path_ctx);

		$query = DB::select('id')
			->from('photo_categories')
			->limit(1);

		if( ! empty($category_path))
		{
			$query->where('path', '=', $category_path);
		}
		else
		{
			$query->where('id', '=', (int) $category_id);
		}

		return $query->execute()->get('id');
	}
	
	public function get_categories()
	{
		$categories = ORM::factory('photo_category')
			->where('parent_id', '=', (int) $this->get_current_category_id());
		
		if( $this->with_image )
			$categories->where('image', '!=', '');
		
		return $categories;
	}

	public function count_total()
	{
		return $this->get_categories()->reset(FALSE)->count_all();
	}
	
	public function fetch_data()
	{
		$category_id = $this->get_current_category_id();
		$category = ORM::factory('photo_category', $category_id);
		
		if(!$category->loaded())
		{
			return array(
				'categories' => array(),
				'photos' => array(),
				'videos' => array(),
				'category' => NULL
			);
		}

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
		
		$data['id_ctx'] = Arr::get($data, 'id_ctx');
		$data['path_ctx'] = Arr::get($data, 'path_ctx');
		
		$data['with_images'] = (bool) Arr::get($data, 'with_images');

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