<?php defined('SYSPATH') or die('No direct access allowed.');

class Behavior_Gallery extends Behavior_Abstract
{
	public function  routes()
	{
		return array(
			'/<category_id>' => array(
				'method' => 'get_photos',
				'regex' => array(
					'category_id' => '[0-9]+'
				)
			),
			'/<category_path>' => array(
				'method' => 'get_photos',
				'regex' => array(
					'category_path' => '.*'
				)
			)
		);		
	}
	
	public function execute() 
	{
		
	}
	
	public function get_photos()
	{
	}
}