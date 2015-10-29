<?php
function epl_wpimport_get_meta_boxes($epl_meta_boxes) {
	global $epl_ai_meta_fields;
	$epl_ai_meta_fields = $epl_meta_boxes;
		return $epl_meta_boxes;
}
add_filter('epl_listing_meta_boxes','epl_wpimport_get_meta_boxes');

function epl_wpimport_register_fields() {
	global $epl_ai_meta_fields,$epl_wpimport;
	$epl_wpimport = new RapidAddon('Easy Property Listings Custom Fields', 'epl_wpimport_addon');
	if(!empty($epl_ai_meta_fields)) {
	    foreach($epl_ai_meta_fields as $epl_meta_box) {
		    if(!empty($epl_meta_box['groups'])) {
	        foreach($epl_meta_box['groups'] as $group) {
	        		$epl_wpimport->add_title( $group['label'], $group['label'] );
		                $fields = $group['fields'];
		                $fields = array_filter($fields);
		                if(!empty($fields)) {
					foreach($fields as $field) {
					
						switch($field['type']) {
							
							case 'textarea':
								$epl_wpimport->add_field($field['name'], $field['label'], 'textarea');
								break;
								
							case 'url':
							case 'date':
							case 'sold-date':
							case 'sold-date':
							case 'decimal':
							case 'number':
							case 'checkbox':
							case 'checkbox_single':
								$epl_wpimport->add_field($field['name'], $field['label'], 'text');
								break;
							
							case 'select': 
							case 'radio':
								$opts = isset($field['opts']) ? $field['opts'] : array();
								if( !empty($opts) ) {
									foreach($opts as $opt_key	=>	&$opt_value) {
										if( is_array($opt_value) ) {
											$opts[$opt_key] = $opt_value['label'];
										}
									}
								}
								$epl_wpimport->add_field($field['name'], $field['label'], 'radio',array());
								break;
							
							default:
								$type = in_array($field['type'], array('text','hidden',) ) ? 'text' : $field['type'];
								$epl_wpimport->add_field($field['name'], $field['label'], $type);
								break;
						}
					}
		                }
			}
		}
	}
	
	    // Register Import Function
	$epl_wpimport->set_import_function('epl_wpimport_import_function');
	
	// display a dismiss able notice warning the user to install WP All Import to use the add-on.
	// Customize the notice text by passing a string to admin_notice(), i.e. admin_notice("XYZ Theme recommends you install WP All Import (free | pro)")
	$epl_wpimport->admin_notice("Easy Property Listings requires <a href='http://wordpress.org/plugins/wp-all-import'>WP All Import Pro</a>");
	
	
	// the add-on will run for all themes/post types if no arguments are passed to run()
	$epl_wpimport->run(
	        array(
		        "post_types" => epl_get_core_post_types()
	        )
	);
	 
	}
}

add_action('init','epl_wpimport_register_fields');

// Import Function
function epl_wpimport_import_function( $post_id, $data, $import_options ) {
	global $epl_wpimport,$epl_ai_meta_fields;
	if(!empty($epl_ai_meta_fields)) {
		foreach($epl_ai_meta_fields as $epl_meta_box) {
			if(!empty($epl_meta_box['groups'])) {
				foreach($epl_meta_box['groups'] as $group) {
				        $fields = $group['fields'];
				        $fields = array_filter($fields);
				        if(!empty($fields)) {
						foreach($fields as $field) {
							if ( $epl_wpimport->can_update_meta($field['name'], $import_options) ) {
								update_post_meta($post_id, $field['name'], $data[$field['name']]);
							}
						}
				        }
				}
			}
		}
	}
}

function epl_wpimport_image_needs_update($unique_id,$url,$mod_time,$id) {

/*	if($id == 'm') {
		* feature image, go no further *
		echo $url;
		return;
	}*/
	$live_import	=	function_exists('epl_get_option')  ?  epl_get_option('epl_wpimport_skip_update') : 'off';
	if ( $live_import != 'on' ) {
		echo $url;
		return;
	}
	/** check if post with unique id already exists **/
	$args = array(
	    'meta_key' 			=> 'property_unique_id',
	    'meta_value' 		=> $unique_id,
	    'post_type' 		=> 'any',
	    'post_status' 		=> 'any',
	    'posts_per_page' 	=> -1
	);
	$posts = get_posts($args);
	if( !empty($posts) ) {
		foreach($posts as $epl_post) {

			/** only upload images which are recently modified **/
			if( get_post_meta($epl_post->ID,'property_images_mod_date',true)  != '') {
				if(strtotime(get_post_meta($epl_post->ID,'property_images_mod_date',true)) < strtotime($mod_time) ) {
					echo $url;
				}
			} else {
				echo $url;
			}
		}

	} else {
		echo $url;
	}
}

function epl_wpimport_img_loop($unique_id,$mod_time,$url,$id) {

	$mod_times 	= array_filter(explode(",",$mod_time));
	$urls 		= array_filter(explode(",",$url));
	$ids 		= array_filter(explode(",",$id));
	$len 		= count($urls);
	$i 			= 0;
	foreach($urls as $index	=>	$img_src) {
		if($img_src != '') {
			epl_wpimport_image_needs_update($unique_id,$img_src,$mod_times[$index],$ids[$index]);
			if ($i == $len - 1) {
		        // last
		    } else {
		    	echo "\n";
		    }
		}
		$i++;
	}

}

// skip image uploading
function epl_wpimport_is_image_to_update($default,$post_object,$xml_object) {
	// default is true 
	echo "<pre>aaaa";
	print_r($default);
	print_r($post_object);
	print_r($xml_object);
	die; 
}
//add_filter('wp_all_import_is_image_to_update','epl_wpimport_is_image_to_update',10,3);

// skip old image delition
function epl_wpimport_delete_images($default,$post_object,$xml_object) {
	// default is true
	return false; 
}
add_filter('wp_all_import_delete_images','epl_wpimport_delete_images',10,3);

function epl_wpimport_is_post_to_update($pid,$xml_node) {
	
	$live_import	=	function_exists('epl_get_option')  ?  epl_get_option('epl_wpimport_skip_update') : 'off';
	if ( $live_import == 'on' ) {

		/** only update posts if new data is available **/
		if( get_post_meta($pid,'property_mod_date',true)  != '') {
		
			$postmodtime 		= epl_feedsync_format_date(get_post_meta($pid, 'property_mod_date',true ));
			$updatemodtime		= epl_feedsync_format_date($xml_node['@attributes']['modTime']);
	
			if( strtotime($updatemodtime) > strtotime($postmodtime) ) {
				// update
				return true;
			}
		}
		return false;
	}
	return true;
}
add_filter('wp_all_import_is_post_to_update', 'epl_wpimport_is_post_to_update', 10, 2);