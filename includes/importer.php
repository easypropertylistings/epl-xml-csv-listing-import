<?php
function epl_wpimport_get_meta_boxes($epl_meta_boxes) {
	global $epl_ai_meta_fields;
	$epl_ai_meta_fields = $epl_meta_boxes;
		return $epl_meta_boxes;
}
add_filter('epl_listing_meta_boxes','epl_wpimport_get_meta_boxes');

function epl_wpimport_register_fields() {
	global $epl_ai_meta_fields, $epl_wpimport;
	
	// Initialize EPL WP All Import Pro add-on.
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
	
	// Log Import Actions
	//$epl_wpimport->set_import_function('epl_wpimport_import_function');
	
}

add_action('init','epl_wpimport_register_fields');



// Import Function
function epl_wpimport_import_function( $post_id, $data, $import_options ) {
	global $epl_wpimport,$epl_ai_meta_fields;
	
	if(!empty($epl_ai_meta_fields)) {
		
		$epl_wpimport->log( '<strong>EPL IMPORTER UPDATING FIELDS:</strong>' );

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
							
							// Log
							if ( !empty( $data[$field['name']] ) ) {
							
								$epl_wpimport->log( '- EPL Field Updated: `' . $field['name'] . '` value `' . $data[$field['name']] . '`' );
							
							}
						}
						
				        }
				}
			}
		}
	} 

}

// Notification that EPL Importer is Running
function epl_wpimport_log( $post_id ) {
	
	global $epl_wpimport;
	
	// Importer Title
	$epl_wpimport_label 	= "<strong>EPL IMPORTER ACTIVATED: </strong>";
	
	// Live Import Status						
	$live_import		= function_exists('epl_get_option')  ?  epl_get_option('epl_wpimport_skip_update') : 'off';
	
	// Live Import Label
	$live_import_label	= $live_import == 'on'  ?  'Record Skipping Enabled' : 'Record Skipping Disabled';
	
	// Log EPL All Importer Activation Status
	$epl_wpimport->log( $epl_wpimport_label . $live_import_label );
	
	

}
add_action('pmxi_before_post_import', 'epl_wpimport_log', 10, 1);

// Notification that EPL Importer is Running
function epl_wpimport_log_pmxi_gallery_image( $post_id ) {
	
	
	/*
	* Parameters
	* $pid 			– the ID of the post/page/Custom Post Type that was just created.
	* $attid 			– the ID of the attachment
	* $image_filepath 	– the full path to the file: C:\path\to\wordpress\wp-content\uploads\2010\05\filename.png
	*/
	
	global $epl_wpimport;
	
	// Importer Title
	$epl_wpimport_label 	= "<strong>EPL IMPORTER ACTIVATED IMAGES : </strong>";
	
	// Live Import Status						
	$live_import		= function_exists('epl_get_option')  ?  epl_get_option('epl_wpimport_skip_update') : 'off';
	
	// Live Import Label
	$live_import_label	= $live_import == 'on'  ?  'IMAGES Record Skipping Enabled' : 'IMAGES Record Skipping Disabled';
	
	// Log EPL All Importer Activation Status
	$epl_wpimport->log( $epl_wpimport_label . $live_import_label );
	
	

}
add_action('pmxi_before_post_import', 'epl_wpimport_log_pmxi_gallery_image', 10, 1);

//add_action('pmxi_gallery_image', 'epl_wpimport_log_pmxi_gallery_image', 10, 1);




// Update notification: Skipped
function epl_wpimport_post_skipped_notification($vars) {
	global $epl_wpimport;

	$epl_wpimport->log( '<strong>EPL Importer Skipped.</strong>' );
	
	return $vars;

}


function epl_wpimport_image_needs_update($unique_id,$url,$mod_time,$id) {

/*	if($id == 'm') {
		* feature image, go no further *
		echo $url;
		return;
	}*/
	
	global $epl_wpimport;
	
	$live_import	=	function_exists('epl_get_option')  ?  epl_get_option('epl_wpimport_skip_update') : 'off';
	if ( $live_import == 'off' ) {
		
		
		$epl_wpimport->log( '<strong>UPDATE IMAGE LIVE_IMPORT_OFF:</strong>' );
		
		
		echo $url;
		return;
	}
	
	
	$epl_wpimport->log( '<strong>UPDATE IMAGE IMAGE LIVE_IMPORT_>>>>>ON:</strong>' );
	
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
					$epl_wpimport->log( '<strong>UPDATE IMAGE IMAGE LIVE_IMPORT_>>>>> 1111111:</strong>' );
					echo $url;
				}
			} else {
				$epl_wpimport->log( '<strong>UPDATE IMAGE IMAGE LIVE_IMPORT_>>>>> 2222222:</strong>' );
				echo $url;

			}
		}

	} else {
		$epl_wpimport->log( '<strong>UPDATE IMAGE IMAGE LIVE_IMPORT_>>>>> 333333:</strong>' );
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
	
	global $epl_wpimport;
	$live_import	=	function_exists('epl_get_option')  ?  epl_get_option('epl_wpimport_skip_update') : 'off';
	if ( $live_import == 'on' ) {
		
		$epl_wpimport->log( '<strong>UPDATE IMAGE LIVE_IMPORT_ON: KEEP IMAGES</strong>' );
		
		
		// 
		return false;
	}
	
	$epl_wpimport->log( '<strong>UPDATE IMAGE LIVE_IMPORT_OFF: DELETE IMAGE for : '.$post_object["post_title"].' </strong>' );
	// default filter values in WP All Import is: true
	return true; 
}
//add_filter('wp_all_import_delete_images','epl_wpimport_delete_images',10,3);



// Notification that EPL Importer is Running
function epl_wpimport_notification( $notification = 'skip' , $post_id = false ) {
	
	global $epl_wpimport;
	
	// Importer Title
	$epl_wpimport_label 	= "<strong>EPL IMPORTER: </strong>";
	
	
	$notification_label 	= 'Record Skipped';
	
	$post_title = '';

	if ( $post_id != false ) {
		$post_title = get_the_title( $post_id );
		
		$post_title = ' `' . $post_title . '`';
		
	} 
	
	
	
	if ( $notification == 'update' ) {
		$notification_label 	= 'Date modified, updating';	
	} 
	
	if ( $notification == 'modified'  ) {
		$notification_label	= 'Modified Listing, updating';
	}
	
	if ( $notification == 'update_field'  ) {
		$notification_label	= 'Updating Field';
	}
	
	if ( $notification == 'skip_unchanged' ) {
		$notification_label	= 'Listing Modified Time Unchanged, Skipping Record update';
	}
	
	if ( $notification == 'updating' ) {
		$notification_label	= 'Updating Fields:';
	} 
	
	if ( $notification == 'skip' ) {
		$notification_label	= 'Skipped, previously imported record found for:';
	}
	
	// Output
	$epl_wpimport->log( $epl_wpimport_label . $notification_label . $post_title );

}




function epl_wpimport_is_post_to_update( $pid , $xml_node) {
	
	global $epl_wpimport;
	
	add_action('pmxi_before_post_import', 'epl_wpimport_post_saved_notification', 10, 1);
	
	$live_import	=	function_exists('epl_get_option')  ?  epl_get_option('epl_wpimport_skip_update') : 'off';
	if ( $live_import == 'on' && get_post_meta($pid,'property_mod_date',true) != '' ) {
		/** only update posts if new data is available **/
		$postmodtime 		= epl_feedsync_format_date(get_post_meta($pid, 'property_mod_date',true ));
		$updatemodtime		= epl_feedsync_format_date($xml_node['@attributes']['modTime']);
		
		

		if( strtotime($updatemodtime) > strtotime($postmodtime) ) {

			epl_wpimport_notification( 'update'  , $pid );

			// update
			return true;
		}
		
		epl_wpimport_notification( 'skip_unchanged'  , $pid );
				
		// Don't update
		return false;
	}
	
	epl_wpimport_notification( 'skip' , $pid );
	
	// Don't update
	return true;
}
add_filter('wp_all_import_is_post_to_update', 'epl_wpimport_is_post_to_update', 10, 2);


if( !function_exists('epl_wpimport_delete_attachments') ) {
	/**
	 * Delete attachments linked to a specified post
	 * @param int $parent_id Parent id of post to delete attachments for
	 */
	function epl_wpimport_delete_attachments($parent_id, $unlink = true, $type = 'images',$post_object,$xml_object) {	
		global $epl_wpimport;
		$del_attchments = true;
		
		$ids = array();

		$attachments = get_posts(array('post_parent' => $parent_id, 'post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null));
		
		if( get_post_meta($parent_id,'property_images_mod_date',true)  != '') { 
			$mod_date =  strtotime(epl_feedsync_format_date(get_post_meta($parent_id,'property_images_mod_date',true)));
		}
		
		$new_mod_date = 
		isset($xml_object['images']['img']) ? 
			current($xml_object['images']['img'][0]['modTime']) : 
			current($xml_object['objects']['img'][0]['modTime']);
		
		$new_mod_date = strtotime(epl_feedsync_format_date($new_mod_date));
		$live_import	=	function_exists('epl_get_option')  ?  epl_get_option('epl_wpimport_skip_update') : 'off';
		$epl_wpimport->log( 
			'<strong> EPL Image processing started : Old Mod Date : '.$mod_date.' New Mod Date : '.$new_mod_date.'</strong> Live Import : '.$live_import
		);
		foreach ($attachments as $attach) {

			if ( $live_import == 'off' ) {
				// if live update is off delete
				$del_attchments = true;
				$epl_wpimport->log( 
					'<strong>UPDATE IMAGE LIVE_IMPORT_OFF: DELETING IMAGE > ID : '.$attach->ID.' NAME : '.$attach->post_title.' </strong>' 
				);
			} else {
				// possible delete
				if( $mod_date == $new_mod_date )  {
					// DO not delete
					$del_attchments = false;
					$epl_wpimport->log( '<strong>Images unchanged, skipping > ID : '.$attach->ID.' NAME : '.$attach->post_title.' </strong>' );
				} else {
					// delete
					$del_attchments = true;
					$epl_wpimport->log( 
						'<strong>Images changes detected, deleting image > ID : '.$attach->ID.' NAME : '.$attach->post_title.' </strong>'
					 );
				
				}
			}
			if( $del_attchments ) {

				if ( $type == 'images' and has_post_thumbnail($parent_id) ) delete_post_thumbnail($parent_id);
				
				if ($type == 'files' and ! wp_attachment_is_image( $attach->ID ) ){
			
					if ($unlink)
					{
						wp_delete_attachment($attach->ID, true);							
					}
					else
					{
						$ids[] = $attach->ID;
					}
				}	
				elseif ($type == 'images' and wp_attachment_is_image( $attach->ID )) {

					if ($unlink)
					{ 
						wp_delete_attachment($attach->ID, true);
					}
					else
					{																		
						$ids[] = $attach->ID;				
					}
				}
			}	
		}

		global $wpdb;
				
		if ( ! empty( $ids ) ) {

			$ids_string = implode( ',', $ids );
			// unattach
			$result = $wpdb->query( "UPDATE $wpdb->posts SET post_parent = 0 WHERE post_type = 'attachment' AND ID IN ( $ids_string )" );

			foreach ( $ids as $att_id ) {
				clean_attachment_cache( $att_id );
			}
		}

		return $ids;
	}
}



