<?php defined('SYSPATH') or die('No direct access allowed.');

class DataSource_Hybrid_Field_Source_Gallery extends DataSource_Hybrid_Field_Source_OneToOne {

	protected $_props = array(
		'isreq' => TRUE
	);	
	
	protected $_widget_types = array('gallery_categories');
	
	public function __construct( array $data = NULL)
	{
		parent::__construct( $data );
		$this->family = DataSource_Hybrid_Field::FAMILY_SOURCE;
	}
	
	public function get_gallery_categories()
	{
		$categories = ORM::factory('photo_category')->map();
		
		$result = array(__('--- Not set ---'));
		foreach ($categories as $category)
		{
			$result[$category['id']] = str_repeat('- - ', $category['level']). $category['title'];
		}
		
		return $result;
	}
	
	public function category_exists($id)
	{
		return ORM::factory('photo_category', $id)->loaded();
	}

	public function get_type()
	{
		return 'INT(10)';
	}
	
	public static function fetch_widget_field( $widget, $field, $row, $fid, $recurse )
	{
		$related_widget = NULL;

		if($recurse > 0 AND isset($widget->doc_fetched_widgets[$fid]))
		{
			$related_widget = self::_fetch_related_widget($widget, $row, $fid, $recurse, 'category_id', TRUE);
		}

		return ($related_widget !== NULL) 
			? $related_widget 
			: $row[$fid];
	}
}