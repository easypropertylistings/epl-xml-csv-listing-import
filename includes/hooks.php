<?php
function epl_wpimport_extensions_options_filter($epl_fields = null) {
	$fields = array();
	$epl_of_fields = array(
		'label'		=>	__('WP All Import Add-On','epl-wpimport')
	);

	$fields[] = array(
		'label'		=>	__('Settings', 'epl-of'),
		'intro'		=>	'<h3 style="margin-top:0;">' . __('Importer Settings.' , 'epl-wpimport') . '</h3>',
		'fields'	=>	array(

			array(
				'name'	=>	'epl_wpimport_skip_update',
				'label'	=>	__('Activate once initial import is set', 'epl-wpimport'),
				'type'		=>	'radio',
				'opts'		=>	array(
					'on'	=>	__('Enable (Skip record import based on modified time)','epl-wpimport'),
					'off'	=>	__('Disable (operates as per WP All Import Pro Default)','epl-wpimport')
				),
				'default'	=>	'off',
			),
		)
	);

	$epl_of_fields['fields'] = $fields;
	$epl_fields['epl_wpimport'] = $epl_of_fields;
	return $epl_fields;
}
add_filter('epl_extensions_options_filter_new', 'epl_wpimport_extensions_options_filter', 10, 1);
