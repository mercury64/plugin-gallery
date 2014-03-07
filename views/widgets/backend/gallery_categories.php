<div class="widget-content">
	<?php
		echo Bootstrap_Form_Element_Control_Group::factory(array(
			'element' => Bootstrap_Form_Element_Select::factory(array(
				'name' => 'category_id', 'options' => $categories
			))
			->attributes('class', Bootstrap_Form_Element_Input::XXLARGE)
			->selected($widget->category_id)
			->label(__('Root category'))
		));
	?>
</div>