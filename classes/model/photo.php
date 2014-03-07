<?php defined('SYSPATH') or die('No direct script access.');

class Model_Photo extends ORM {
	
	const TYPE_IMAGE = 'image';
	const TYPE_VIDEO = 'video';
	
	protected $_created_column = array(
		'column' => 'created_on',
		'format' => 'Y-m-d H:i:s'
	);
	
	protected $_belongs_to = array(
		'category' => array('model' => 'photo_category')
	);

	protected $_sorting = array(
		'position' => 'asc'
	);
	
	protected $_loaded_with = array(
		'category'
	);

	public function images()
	{	
		return array(
			'photos' . DIRECTORY_SEPARATOR . 'full' => array(
				'subfolder' => $this->category->path,
				'quality' => 85
			),
			'photos' . DIRECTORY_SEPARATOR . '274_215' => array(
				'subfolder' => $this->category->path,
				'width' => 274,
				'height' => 215,
				'quality' => 100,
				'master' => Image::AUTO,
			),
		);
	}
	
	public function get_next_position()
	{
		$last_position = DB::select(array(DB::expr('MAX(position)'), 'pos'))
			->from($this->table_name())
			->where('category_id', '=', $this->category_id)
			->execute($this->_db)
			->get('pos', 0);
		
		return ((int) $last_position) + 1;
	}

	public function create(\Validation $validation = NULL) 
	{
		if ($this->position == 0)
		{
			$this->position = $this->get_next_position();
		}

		return parent::create($validation);
	}
	
	public function empty_category_image()
	{
		DB::update('photo_categories')
			->where('image', '=', $this->filename)
			->set(array('image' => ''))
			->execute($this->_db);
		
		return $this;
	}
	
	/**
	 * 
	 * @param integer $category_id
	 * @return boolean
	 */
	public function move($category_id)
	{
		$category = ORM::factory('photo_category', $category_id);
		$status = FALSE;
		
		if( ($category_id > 0 AND ! $category->loaded()) OR ! $this->loaded() )
		{
			return FALSE;
		}
		
		if($this->type == Model_Photo::TYPE_IMAGE)
		{
			foreach ($this->images() as $path => $data)
			{
				$old_dir = PUBLICPATH . $path . DIRECTORY_SEPARATOR . $this->category->path . DIRECTORY_SEPARATOR;
				$new_dir = PUBLICPATH . $path . DIRECTORY_SEPARATOR . $category->path . DIRECTORY_SEPARATOR;

				if(file_exists($old_dir . $this->filename))
				{
					if( ! is_dir($new_dir) )
					{
						mkdir( $new_dir, 0777, TRUE );
						chmod( $new_dir, 0777 );
					}

					$status = rename($old_dir . $this->filename, $new_dir . $this->filename);
				}
			}
		}
		
		if($status === TRUE)
		{
			$this->set('category_id', $category_id)->update();
		}
		
		return $status;
	}

	public function delete()
	{
		if ( ! $this->loaded() )
		{
			throw new Kohana_Exception('photo not loaded');
		}
		
		$this->empty_category_image();
		
		if($this->type == Model_Photo::TYPE_IMAGE)
		{
			$this->unlink_files();
		}
		
		return parent::delete();
	}
	
	public function delete_by_category($id)
	{
		$photos = $this->reset(FALSE)
			->where('category_id', '=', (int) $id)
			->find_all();
		
		foreach ($photos as $photo)
		{
			$photo->delete();
		}
		
		return $this;
	}

	/**
	 * 
	 * @param string $folder
	 * @return string
	 */
	public function full()
	{
		return PUBLIC_URL . 'photos/full/' . $this->category->path . '/' . $this->filename;
	}
	
	public function thumb()
	{
		return PUBLIC_URL . 'photos/274_215/' . $this->category->path . '/' . $this->filename;
	}
	
	public function video($width = 600, $height = 400)
	{
		if(strpos($this->filename, 'youtube') !== FALSE)
			return '<iframe width="'.$width.'" height="'.$height.'" src="'.$this->filename.'"></iframe>';
		else
			return "<object width='".$width."' height='".$height."'>".
				"<param name='movie' value='".$this->filename."'></param>".
				"<param name='wmode' value='transparent'></param>".
				"<param name='allowScriptAccess' value='always'></param>".
				"<param name='allowFullScreen' value='true'></param>".
				"<embed src='".$this->filename."' type='application/x-shockwave-flash' wmode='window' width='".$width."' height='".$height."' allowFullScreen='true' allowScriptAccess='always'></embed>".
			'</object>';
	}

	public function is_image()
	{
		return $this->type == Model_Photo::TYPE_IMAGE;
	}

	public function filter_by_category($id)
	{
		 return $this
			->where('category_id', '=', (int) $id);
	}
	
	/**
	 * 
	 * @param string $file
	 * @param string $field
	 * @param array $params
	 * @return null|string
	 * @throws Kohana_Exception
	 */
	public function add_image( $file, $field = NULL, $params = NULL )
	{
		if ( $field !== NULL AND ! $this->loaded() )
		{
			throw new Kohana_Exception( 'Model must be loaded' );
		}

		if ( $params === NULL )
		{
			$params = $this->images();
		}

		$tmp_file = TMPPATH . trim( $file );

		if ( ! file_exists( $tmp_file ) OR is_dir( $tmp_file ))
		{
			return NULL;
		}

		$ext = strtolower( pathinfo( $tmp_file, PATHINFO_EXTENSION ) );
		$filename = uniqid() . '.' . $ext;
		
		foreach ( $params as $path => $_params )
		{
			$path = PUBLICPATH . trim( $path, '/' ) . DIRECTORY_SEPARATOR;

			$local_params = array(
				'width' => NULL,
				'height' => NULL,
				'master' => NULL,
				'quality' => 95,
				'crop' => TRUE
			);

			$_params = Arr::merge( $local_params, $_params );
			
			if( !empty($_params['subfolder']) )
			{
				$path .= trim($_params['subfolder']) . DIRECTORY_SEPARATOR;
			}
			
			$path = FileSystem::normalize_path($path);
			
			if ( ! is_dir( $path ) )
			{
				mkdir( $path, 0777, TRUE );
				chmod( $path, 0777 );
			}

			$file = $path . $filename;

			if ( ! copy( $tmp_file, $file ) )
			{
				continue;
			}

			chmod( $file, 0777 );

			$image = Image::factory( $file );

			if(!empty($_params['width']) AND !empty($_params['height']))
			{
				if($_params['width'] < $image->width OR $_params['height'] < $image->height )
					$image->resize( $_params['width'], $_params['height'], $_params['master'] );

				if($_params['crop'])
					$image->crop( $_params['width'], $_params['height'] );
			}

			$image->save();
		}

		if ( $field !== NULL )
		{
			$this
				->set($field, $filename)
				->update();
		}
			
		unlink( $tmp_file );

		return $filename;
	}
	
	/**
	 * 
	 * @param type $field
	 * @return \ORM
	 * @throws Kohana_Exception
	 */
	public function delete_image( $field )
	{
		if ( ! $this->loaded() )
		{
			throw new Kohana_Exception( 'Model must be loaded' );
		}

		foreach ($this->images() as $path => $data)
		{
			$file = PUBLICPATH . $path . DIRECTORY_SEPARATOR . $this->get($field);
			if(file_exists($file) AND !is_dir($file))
			{
				unlink($file);
			}
		}

		$this
			->set($field, '')
			->update();
		
		return $this;
	}
	
	/**
	 * 
	 * @return \Model_Photo
	 */
	public function unlink_files()
	{
		foreach ($this->images() as $path => $data)
		{
			$file = PUBLICPATH . $path . DIRECTORY_SEPARATOR . $this->category->path . $this->filename;
			if(file_exists($file))
			{
				unlink($file);
			}
		}
		
		return $this;
	}
	
	/**
	 * return string
	 */
	public function parse_video_link( $string )
	{
		if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $string, $match)) 
		{
			return 'http://www.youtube.com/embed/' . $match[1];
		}
		elseif( preg_match('%([^"&?/ ]{11})%i', $string, $match))
		{
			return 'http://www.youtube.com/embed/' . $match[0];
		}
		
		return $string;
	}
	
	public function after_save()
	{
		Cache::instance()->delete_tag('gallery');
	}

    public function after_delete( $id )	
	{
		Cache::instance()->delete_tag('gallery');
	}
}