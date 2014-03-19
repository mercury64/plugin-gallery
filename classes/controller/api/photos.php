<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

class Controller_API_Photos extends Controller_System_API {

	public function post_upload()
	{
		$file = Upload::file($_FILES['file']);
		
		list($status, $filename) = $file;
		
		if($status == TRUE)
		{
			$photo = ORM::factory('photo')
				->values(array(
					'category_id' => (int) $this->param('id', NULL, 0),
					'type' => Model_Photo::TYPE_IMAGE
				))
				->create();
			
			$photo->add_image($filename, 'filename');
			
			$this->response( View::factory('photos/image', array('photo' => $photo, 'category' => $photo->category))->render() );
		}
	}
	
	public function post_from_url()
	{
		$url = $this->param('url', NULL, TRUE);
		$category_id = (int) $this->param('category_id', NULL, TRUE);
		
		if( ! Valid::url($url))
		{
			$this->message(__('URL not valid'));
			return;
		}
		
		if ( ! preg_match ("/\b(?:vimeo|youtube|dailymotion)\.com\b/i", $url)) 
		{
			$this->message(__('Video must be from youtube'));
			return;
		}
		
		$photo = ORM::factory('photo');
		
		$photo->values(array(
			'filename' => $photo->parse_video_link($url),
			'type' => Model_Photo::TYPE_VIDEO,
			'category_id' => $category_id
		))->create();
		
		$this->response( View::factory('photos/image', array('photo' => $photo, 'category' => $photo->category))->render() );
	}
	
	public function post_delete()
	{
		$id = (int) $this->param('id', NULL, TRUE);
		$photo = ORM::factory('photo', $id)->delete();
		
		$this->response(TRUE);
	}
	
	public function post_category_save()
	{
		$id = (int) $this->param('id');
		
		$data = array(
			'title' => $this->param('title', NULL, TRUE),
			'description' => $this->param('description', NULL),
			'slug' => $this->param('slug', NULL, TRUE)
		);
		
		$category = ORM::factory('photo_category');
		
		if($id > 0)
		{
			$category->where('id', '=', $id)->find();
			
			if( ! $category->loaded()) return;
		}
		else
		{
			$data['parent_id'] = (int) $this->param('parent_id', NULL);
		}
		
		$category->values($data)->save();
		$this->response(View::factory('photos/category', array('category' => $category))->render());
	}
	
	public function get_category_edit()
	{
		$id = (int) $this->param('id');
		
		$category = ORM::factory('photo_category', $id);
			
		if( ! $category->loaded()) return;
		
		$this->response(View::factory('photos/blocks/category_form', array('category' => $category))->render());
	}
	
	public function post_category_delete()
	{
		$id = (int) $this->param('id', NULL, TRUE);
		
		if(ORM::factory('photo_category', $id)->delete())
		{
			$this->response(TRUE);
		}
	}
	
	public function post_category_image()
	{
		$id = (int) $this->param('id', NULL, TRUE);
		$category_id = (int) $this->param('category_id', NULL, TRUE);
		
		$photo = ORM::factory('photo', $id);
		$category = ORM::factory('photo_category', $category_id);
		if($photo->loaded() AND $category->loaded())
		{
			$category->values(array(
				'image' => $photo->filename
			))->update();

			$this->response(TRUE);
		}
	}

	public function post_categories_sort()
	{
		$parent_id = (int) $this->param('parent_id', NULL, TRUE);
		$data = $this->param('pos', NULL, TRUE);
		
		foreach ($data as $pos => $id)
		{
			DB::update('photo_categories')
				->set(array(
					'position' => $pos
				))
				->where('id', '=', $id)
				->where('parent_id', '=', $parent_id)
				->execute();
		}
		
		ORM::factory('photo')->after_save();

		$this->response(TRUE);
	}
	
	public function post_categories_move()
	{
		$id = (int) $this->param('parent_id', NULL, TRUE);
		$category_id = (int) $this->param('category_id', NULL, TRUE);
		
		$category = ORM::factory('photo_category', $id);

		$this->response($category->move($category_id));
	}

	public function post_move()
	{
		$id = (int) $this->param('id', NULL, TRUE);
		$category_id = (int) $this->param('category_id', NULL);
		
		$photo = ORM::factory('photo', $id);
		
		if( $photo->move($category_id) )
		{
			if($this->param('category_image') == 'true')
			{
				$photo->empty_category_image();
			}

			$this->response(TRUE);
		}
	}

	public function post_sort()
	{
		$data = $this->param('pos', NULL, TRUE);
		$category_id = (int) $this->param('category_id', NULL);
		
		$old_pos = DB::select('id', 'position')
			->from('photos')
			->where('category_id', '=', $category_id)
			->order_by('position', 'asc')
			->execute()
			->as_array(NULL, 'id');
		
		$diff = array_diff_assoc($old_pos, $data);
		foreach ($data as $pos => $id)
		{
			DB::update('photos')
				->set(array(
					'position' => $pos
				))
				->where('id', '=', $id)
				->where('category_id', '=', $category_id)
				->execute();
		}
		
		ORM::factory('photo')->after_save();

		$this->response(TRUE);
	}
}