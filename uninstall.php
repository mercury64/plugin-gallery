<?php

$sources = Datasource_Data_Manager::get_all('hybrid');

foreach ($sources as $source)
{
	$ds = Datasource_Data_Manager::load($source['id']);
	
	$keys = array();
	foreach($ds->record()->fields() as $field)
	{
		if($field->type == 'source_gallery')
			$keys[] = $field->name;
	}

	DataSource_Hybrid_Field_Factory::remove_fields($ds->record(), $keys);
}