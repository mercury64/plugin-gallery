<?php defined('SYSPATH') or die('No direct access allowed.');

try {
	mkdir(PUBLICPATH . 'photos', 0777);
}  catch (Exception $e) {}
