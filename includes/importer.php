<?php
/**
 * Importer Functions
 *
 * @package    EPL-IMPORTER-ADD-ON
 * @subpackage Functions/Importer
 * @copyright  Copyright (c) 2020, Merv Barrett
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Importer Fields
 *
 * @since 1.0
 * @since 2.0.1 Removed global $epl_ai_meta_fields.
 * @since 2.0.5 Better support for extensions to only display its fields.
 * @since 2.0.6 Treat the core post types differently VS extension post types, loading all the meta fields of core if post type is from core EPL.
 * @since 2.0.7 Fixed the notice for $epl_meta_box['post_type'] when it's string by typecasting to array.
 */
function epl_wpimport_register_fields() {

	global $epl_wpimport;
	$epl_ai_meta_fields = epl_wpimport_get_meta_fields();

	// Initialize EPL WP All Import Pro add-on.
	$epl_wpimport        = new RapidAddon( 'Easy Property Listings Custom Fields', 'epl_wpimport_addon' );
	$post_type_to_import = '';
	$admin_screen        = ! empty( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore
	$import_page_id      = ! empty( $_GET['id'] ) ? intval( $_GET['id'] ) : 0; //phpcs:ignore

	if ( 'pmxi-admin-manage' === $admin_screen && $import_page_id > 0 ) {

		if ( class_exists( 'PMXI_Import_Record' ) ) {
			// PMXI_Import_Record gives info of a saved import.
			try {
				// Retrieve import object.
				$import_object           = new PMXI_Import_Record();
				$current_importer_object = $import_object->getById( $import_page_id );
				$post_type_to_import     = $current_importer_object->options['custom_type'];

			} catch ( Exception $e ) {
				// Don't do anything if there is an exception.
			}
		}
	} elseif ( 'pmxi-admin-import' === $admin_screen && class_exists( 'PMXI_Handler' ) ) {
			// PMXI_Handler gives data from all import pro session.
			$pxmi_session = new PMXI_Handler();
                         //phpcs:ignore
			$pxmi_action      = ! empty( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
			$pxmi_custom_type = $pxmi_session->get( 'custom_type' );

		if ( 'template' === $pxmi_action && ! empty( $pxmi_custom_type ) ) {
				$post_type_to_import = $pxmi_custom_type;
		}
	}

	if ( ! empty( $epl_ai_meta_fields ) ) {

		foreach ( $epl_ai_meta_fields as $epl_meta_box ) {

			$meta_box_post_types   = (array) $epl_meta_box['post_type'];
			$is_core_post_type     = in_array( $post_type_to_import, epl_get_core_post_types(), true ) ? true : false;
			$skip_continue         = true;
			$core_post_types_in_mb = array_intersect( $meta_box_post_types, epl_get_core_post_types() );

			if ( $is_core_post_type && ! empty( $core_post_types_in_mb ) ) {
					$skip_continue = false;
			}

			if ( $skip_continue && ! empty( $post_type_to_import ) && ! in_array( $post_type_to_import, $meta_box_post_types, true ) ) {
								continue;
			}

			if ( ! empty( $epl_meta_box['groups'] ) ) {
				foreach ( $epl_meta_box['groups'] as $group ) {
					$epl_wpimport->add_title( $group['label'], $group['label'] );
					$fields = (array) $group['fields'];
					$fields = array_filter( $fields );
					if ( ! empty( $fields ) ) {
						foreach ( $fields as $field ) {

							switch ( $field['type'] ) {

								case 'textarea':
									$epl_wpimport->add_field( $field['name'], $field['label'], 'textarea' );
									break;

								case 'url':
								case 'file':
								case 'date':
								case 'sold-date':
								case 'auction-date':
								case 'decimal':
								case 'number':
								case 'checkbox':
									$epl_wpimport->add_field( $field['name'], $field['label'], 'text' );
									break;
								case 'checkbox_single':
								case 'select':
								case 'radio':
									$opts = isset( $field['opts'] ) ? $field['opts'] : array();
									if ( ! empty( $opts ) ) {
										foreach ( $opts as $opt_key   => &$opt_value ) {
											if ( is_array( $opt_value ) ) {
												$opts[ $opt_key ] = $opt_value['label'];
											}
										}
									}
									$epl_wpimport->add_field( $field['name'], $field['label'], 'radio', array() );
									break;

								default:
									$type = in_array( $field['type'], array( 'text', 'hidden' ), true ) ? 'text' : $field['type'];
									$epl_wpimport->add_field( $field['name'], $field['label'], $type );
									break;
							}
						}
					}
				}
			}
		}

		// Register Import Function.
		$epl_wpimport->set_import_function( 'epl_wpimport_import_function' );

		// Display dismissable notice warning the user to install WP All Import to use the add-on.
		$epl_wpimport->admin_notice( __( "Easy Property Listings Importer plugin recommends you install <a href='http://www.wpallimport.com/'>WP All Import Pro</a>", 'epl-wpimport' ) );

		// The add-on will run for all themes/post types if no arguments are passed to run().
		$epl_wpimport->run(
			array(
				'post_types' => epl_wpimport_allowed_post_types(),
			)
		);
	}
}
add_action( 'init', 'epl_wpimport_register_fields' );

/**
 * Import Function
 *
 * @param string $post_id        Post ID.
 * @param string $data           Data.
 * @param array  $import_options Options.
 *
 * @since 1.0
 * @since 2.0.1 Removed global $epl_ai_meta_fields
 * @since 2.0.2 Fix: Fields can be updated with empty values( '', false, 0)
 * @since 2.0.3 Fix: Fields are now correctly skipping when they are unchecked to update.
 * @since 2.0.6 Treat the core post types differently VS extension post types, updating all the meta fields of core if post type is from core EPL.
 * @since 2.0.7 Fixed the notice for $epl_meta_box['post_type'] when it's string by typecasting to array.
 */
function epl_wpimport_import_function( $post_id, $data, $import_options ) {

		global $epl_wpimport;

		$post_type_to_import = $import_options['options']['custom_type'];

	$epl_ai_meta_fields = epl_wpimport_get_meta_fields();
	$imported_metas     = array();

	$live_import = function_exists( 'epl_get_option' ) ? epl_get_option( 'epl_wpimport_skip_update' ) : 'off';

	if ( 'yes' !== $import_options['options']['update_all_data'] && '1' !== $import_options['options']['is_update_epl'] ) {
		$epl_wpimport->log( '- ' . __( 'Preserve EPL Fields', 'epl-wpimport' ) );
		return;
	}

	if ( ! empty( $epl_ai_meta_fields ) ) {

		$epl_wpimport->log( '<b>' . __( 'EPL IMPORTER', 'epl-wpimport' ) . ': ' . __( 'UPDATING FIELDS', 'epl-wpimport' ) . '</b>' );

		foreach ( $epl_ai_meta_fields as $epl_meta_box ) {

			$meta_box_post_types   = (array) $epl_meta_box['post_type'];
			$is_core_post_type     = in_array( $post_type_to_import, epl_get_core_post_types(), true ) ? true : false;
			$skip_continue         = true;
			$core_post_types_in_mb = array_intersect( $meta_box_post_types, epl_get_core_post_types() );

			if ( $is_core_post_type && ! empty( $core_post_types_in_mb ) ) {
					$skip_continue = false;
			}

			if ( $skip_continue && ! empty( $post_type_to_import ) && ! in_array( $post_type_to_import, $meta_box_post_types, true ) ) {
					continue;
			}

			if ( ! empty( $epl_meta_box['groups'] ) ) {
				foreach ( $epl_meta_box['groups'] as $group ) {

					$fields = $group['fields'];
					$fields = array_filter( $fields );

					if ( ! empty( $fields ) ) {
						foreach ( $fields as $field ) {
							if ( pmai_is_epl_update_allowed( $field['name'], $import_options['options'] ) ) {

								if ( 'property_images_mod_date' === $field['name'] ) {

									$old_mod_date = get_post_meta( $post_id, 'property_images_mod_date', true );
									update_post_meta( $post_id, 'property_images_mod_date_old', $old_mod_date );

									$epl_wpimport->log( '- ' . __( 'Field Updated:', 'epl-wpimport' ) . '`property_images_mod_date_old`  POST: ' . $post_id . ': - ' . __( 'Images Modified Date: ', 'epl-wpimport' ) . '`' . $old_mod_date . '`' );
								}

								if ( epl_wpimport_is_field_skipped( $field ) ) {

									$epl_wpimport->log( '- ' . __( 'Field Skipped:', 'epl-wpimport' ) . '`' . $field['name'] . '` value `' . $data[ $field['name'] ] . '`' );

									continue;
								}

								// Field Import.
								if ( 'file' === $field['type'] ) {
									// If it is a file type do not save whole array as metadata, only URL.
									if ( is_array( $data[ $field['name'] ] ) ) {

										if ( ! empty( $data[ $field['name'] ] ) ) {
											$data[ $field['name'] ] = $data[ $field['name'] ]['image_url_or_path'];
										} else {
											$data[ $field['name'] ] = '';
										}
									}
								}

								update_post_meta( $post_id, $field['name'], $data[ $field['name'] ] );

								// Log.
								$log_value = is_array( $data[ $field['name'] ] ) ? serialize( $data[ $field['name'] ] ) : $data[ $field['name'] ];
								$epl_wpimport->log( '- ' . __( 'Field Updated:', 'epl-wpimport' ) . '`' . $field['name'] . '` value `' . $log_value . '`' );

								$imported_metas[] = $field['name'];
							} else {
								$epl_wpimport->log( '- ' . __( 'Field Skipped:', 'epl-wpimport' ) . '`' . $field['name'] . '` value `' . $data[ $field['name'] ] . '`' );
							}
						}
					}
				}
			}
		}

		if ( ! empty( $imported_metas ) ) {
			$epl_wpimport->log( '- ' . __( 'EPL Fields Updated', 'epl-wpimport' ) );
		} else {
			$epl_wpimport->log( '- ' . __( 'Preserve EPL Fields', 'epl-wpimport' ) );
		}
	}
}

/**
 * Notification that EPL Importer is Running Logging Output
 *
 * @param string $post_id Post ID.
 *
 * @since 1.0
 */
function epl_wpimport_log( $post_id ) {
	global $epl_wpimport;

	// Importer Title.
	$epl_wpimport_label = __( 'EPL IMPORTER', 'epl-wpimport' ) . ': ';

	// Live Import Status.
	$live_import = function_exists( 'epl_get_option' ) ? epl_get_option( 'epl_wpimport_skip_update' ) : 'off';

	// Live Import Label.
	$live_import_label = 'on' === $live_import ? __( 'Record Skipping Enabled', 'epl-wpimport' ) : __( 'Record Skipping Disabled', 'epl-wpimport' );

	// Log EPL All Importer Activation Status.
	$epl_wpimport->log( $epl_wpimport_label . '<b>' . $live_import_label . '</b>' );

}
add_action( 'pmxi_before_post_import', 'epl_wpimport_log', 10, 1 );

/**
 * Notification that EPL Importer is processing images logging output
 *
 * @param string $post_id Post ID.
 *
 * @since 1.0
 */
function epl_wpimport_log_pmxi_gallery_image( $post_id ) {
	/**
	 * Parameters
	 * $pid – the ID of the post/page/Custom Post Type that was just created.
	 * $attid – the ID of the attachment
	 * $image_filepath – the full path to the file: C:\path\to\wordpress\wp-content\uploads\2010\05\filename.png
	 */

	global $epl_wpimport;

	// Importer Title.
	$epl_wpimport_label = __( 'EPL IMPORTER', 'epl-wpimport' ) . ': ';

	// Live Import Status.
	$live_import = function_exists( 'epl_get_option' ) ? epl_get_option( 'epl_wpimport_skip_update' ) : 'off';

	// Live Import Label.
	$live_import_label = 'on' === $live_import ? __( 'Record Skipping Enabled for Images', 'epl-wpimport' ) : __( 'Record Skipping Disabled for Images', 'epl-wpimport' );

	// Log EPL All Importer Activation Status.
	$epl_wpimport->log( $epl_wpimport_label . '<b>' . $live_import_label . '</b>' );
}
add_action( 'pmxi_before_post_import', 'epl_wpimport_log_pmxi_gallery_image', 10, 1 );

/**
 * Update notification: Skipped
 *
 * @param string $vars Variables.
 *
 * @return string
 * @since  1.0
 */
function epl_wpimport_post_skipped_notification( $vars ) {
	global $epl_wpimport;

	$epl_wpimport->log( __( 'EPL IMPORTER', 'epl-wpimport' ) . ': ' . __( 'Record Skipped', 'epl-wpimport' ) );

	return $vars;
}

/**
 * Image loop
 *
 * @param string $unique_id Meta key property_unique_id.
 * @param string $mod_time  Modified date and time.
 * @param string $url       Url.
 * @param string $id        Image ID.
 *
 * @since 1.0
 */
function epl_wpimport_img_loop( $unique_id, $mod_time, $url, $id ) {
	$urls = array_unique( array_filter( explode( ',', $url ) ) );
	$len  = count( $urls );
	$i    = 0;
	foreach ( $urls as $index => $img_src ) {
		if ( ! empty( $img_src ) ) {
			echo esc_url( $url );
			if ( $i === $len - 1 ) { //phpcs:ignore
				// Last.
			} else {
				echo "\n";
			}
		}
		$i++;
	}
}

/**
 * Skip image uploading if mages mod date is not newer
 *
 * @param string $default     Default type.
 * @param object $post_object Post object.
 * @param object $xml_object  XML object.
 *
 * @return bool
 * @since  1.0
 * @since  2.0.3 Updated code for WP All Import Pro >= 4.6.1 with compatibility for lower versions.
 * @since  2.0.8 Fix fatal error for unrecognised formats & added support for jupix format.
 */
function epl_wpimport_is_image_to_update( $default, $post_object, $xml_object ) {
	if ( ! in_array( $post_object['post_type'], epl_wpimport_allowed_post_types(), true ) ) {
		return $default;
	}

	if ( empty( $post_object['ID'] ) ) {
		return $default;
	}

	$live_import = function_exists( 'epl_get_option' ) ? epl_get_option( 'epl_wpimport_skip_update' ) : 'off';

	if ( 'off' === $live_import ) {
		return $default;
	}
	global $epl_wpimport;

	$prop_img_mod_date = get_post_meta( $post_object['ID'], 'property_images_mod_date', true );
	// Only upload images which are recently modified.
	if ( ! empty( $prop_img_mod_date ) ) {

		if ( defined( 'PMXI_VERSION' ) && version_compare( PMXI_VERSION, '4.6.1', '<' ) ) :
			$new_mod_date = strtotime(
				epl_feedsync_format_date(
					get_post_meta( $post_object['ID'], 'property_images_mod_date', true )
				)
			);

			$old_mod_date = strtotime(
				epl_feedsync_format_date(
					get_post_meta( $post_object['ID'], 'property_images_mod_date_old', true )
				)
			);
		else :
			// Check if image mod time tag is present, use it.
			if ( isset( $xml_object['feedsyncImageModtime'] ) ) {
				$new_mod_date = $xml_object['feedsyncImageModtime'];
			} else {
				if ( function_exists( 'EPL_MLS' ) ) {
					$new_mod_date = $xml_object['images']['@attributes']['modTime'];
				} else {
					$new_mod_date = '';

                                        if( isset( $xml_object['images']['img'] ) ) {

                                                $new_mod_date = current( $xml_object['images']['img'][0]['modTime'] ); 
                                        }
                
                                        if( isset( $xml_object['objects']['img'] ) ) {
                                                
                                                $new_mod_date = current( $xml_object['objects']['img'][0]['modTime'] ); 
                                        }
                
                                        if( isset( $xml_object['images']['image'] ) ) {
                                
                                                if( !empty( $xml_object['images']['image'][0]['modified'] ) ) {
                                                        
                                                        $new_mod_date = current( $xml_object['images']['image'][0]['modified'] ); 
                                                }
                                                
                                        }
				}
			}
			$new_mod_date = apply_filters( 'epl_import_image_new_mod_date', $new_mod_date, $xml_object, $post_object );
			$new_mod_date = strtotime( epl_feedsync_format_date( $new_mod_date ) );

			$old_mod_date = strtotime(
				epl_feedsync_format_date(
					$prop_img_mod_date
				)
			);

		endif;

		$epl_wpimport->log( __( 'EPL IMPORTER', 'epl-wpimport' ) . ': ' . __( 'Image Updating process started: Old Modified Date: ', 'epl-wpimport' ) . $old_mod_date . ' - ' . __( 'New Modified Date:', 'epl-wpimport' ) . ' ' . $new_mod_date );

		if ( $old_mod_date < $new_mod_date ) {
			$epl_wpimport->log( __( 'EPL IMPORTER', 'epl-wpimport' ) . ': ' . __( 'Updated Images, Uploading', 'epl-wpimport' ) . '...' );
			return true;
		} else {

			$attachments = get_children(
				array(
					'post_parent' => $post_object['ID'],
					'post_type'   => 'attachment',
				)
			);
			$count       = count( $attachments );

			// If attachment count is 0 then maybe all attachments are deleted for this listings due to faulty old mod date.
			if ( 0 === absint( $count ) ) {
				if ( $old_mod_date > $new_mod_date ) {

					$epl_wpimport->log( __( 'EPL IMPORTER', 'epl-wpimport' ) . ': ' . __( 'Old Modified date greater than new modified date. Attachment Count: ', 'epl-wpimport' ) . $count );

				} else {

					$epl_wpimport->log( __( 'EPL IMPORTER', 'epl-wpimport' ) . ': ' . __( 'Old Modified date equals new modified date. Attachment Count: ', 'epl-wpimport' ) . $count );

				}

				$epl_wpimport->log( __( 'EPL IMPORTER', 'epl-wpimport' ) . ': ' . __( 'Insert & restore deleted attachments' ) );
				return true;
			}

			$epl_wpimport->log( __( 'EPL IMPORTER', 'epl-wpimport' ) . ': ' . __( 'No new images, skipping image update', 'epl-wpimport' ) );
			return false;
		}
	} else {
		$epl_wpimport->log( __( 'EPL IMPORTER', 'epl-wpimport' ) . ': ' . __( 'New Images, updating', 'epl-wpimport' ) );
		return true;
	}
}
add_filter( 'pmxi_is_images_to_update', 'epl_wpimport_is_image_to_update', 10, 3 );

/**
 * Skip old image deletion if images mod date is not newer
 *
 * @param string $default     Default type.
 * @param object $post_object Post object.
 * @param object $xml_object  XML object.
 *
 * @return bool
 * @since  1.0
 * @since 2.0.0 Added new filter epl_import_image_new_mod_date.
 * @since  2.0.8 Fix fatal error for unrecognised formats & added support for jupix format.
 */
function epl_wpimport_delete_images( $default, $post_object, $xml_object ) {
        
	if ( ! in_array( $post_object['post_type'], epl_wpimport_allowed_post_types(), true ) ) {
		return $default;
	}
	global $epl_wpimport;
	$live_import = function_exists( 'epl_get_option' ) ? epl_get_option( 'epl_wpimport_skip_update' ) : 'off';

	$prop_img_mod_date = get_post_meta( $post_object['ID'], 'property_images_mod_date', true );
	$mod_date          = '';
	if ( ! empty( $prop_img_mod_date ) ) {
		$mod_date = strtotime( epl_feedsync_format_date( $prop_img_mod_date ) );
	}

	// Check if image mod time tag is present, use it.
	if ( isset( $xml_object['feedsyncImageModtime'] ) ) {

		$new_mod_date = $xml_object['feedsyncImageModtime'];

	} else {

		if ( function_exists( 'EPL_MLS' ) ) {
			$new_mod_date = $xml_object['images']['@attributes']['modTime'];
		} else {
			$new_mod_date = '';

                        if( isset( $xml_object['images']['img'] ) ) {

                                $new_mod_date = current( $xml_object['images']['img'][0]['modTime'] ); 
                        }

                        if( isset( $xml_object['objects']['img'] ) ) {
                                
                                $new_mod_date = current( $xml_object['objects']['img'][0]['modTime'] ); 
                        }

                        if( isset( $xml_object['images']['image'] ) ) {
                                
                                if( !empty( $xml_object['images']['image'][0]['modified'] ) ) {

                                        $new_mod_date = current( $xml_object['images']['image'][0]['modified'] ); 
                                }
                                
                        }
		}
	}

	$new_mod_date = apply_filters( 'epl_import_image_new_mod_date', $new_mod_date, $xml_object, $post_object );

	$epl_wpimport->log( __( 'EPL IMPORTER', 'epl-wpimport' ) . ': ' . __( 'Image Processing Started: Old Modified Date: ', 'epl-wpimport' ) . $mod_date . ' - ' . __( 'New Modified Date: ', 'epl-wpimport' ) . $new_mod_date . ' ' . __( 'Live Import: ', 'epl-wpimport' ) . $live_import );

	if ( 'off' === $live_import ) {
		// if live update is off delete.
		$epl_wpimport->log( __( 'EPL IMPORTER', 'epl-wpimport' ) . ': ' . __( 'Live import off, default WP All Import functions', 'epl-wpimport' ) );
		return $default;
	} else {

                if( empty( $mod_date ) && empty( $new_mod_date ) ) {
                        return $default;
                }

                $new_mod_date = strtotime( epl_feedsync_format_date( $new_mod_date ) );

		// possible delete.
		if ( $mod_date === $new_mod_date ) {
			// DO not delete.
			$epl_wpimport->log( __( 'EPL IMPORTER', 'epl-wpimport' ) . ': ' . __( 'Images unchanged, skipping image deletion', 'epl-wpimport' ) );
			return false;
		} else {
			$epl_wpimport->log( __( 'EPL IMPORTER', 'epl-wpimport' ) . ': ' . __( 'Images changes detected, deleting images', 'epl-wpimport' ) );
			return true;

		}
	}
	// default filter values in WP All Import is: true.
	return true;
}
add_filter( 'pmxi_delete_images', 'epl_wpimport_delete_images', 10, 3 );

/**
 * Notification that EPL Importer is Running
 *
 * @param string      $notification Notification type.
 * @param bool|object $post_id      Post ID.
 *
 * @since 1.0
 */
function epl_wpimport_notification( $notification = 'skip', $post_id = false ) {
	global $epl_wpimport;

	// Importer Title.
	$epl_wpimport_label = __( 'EPL IMPORTER', 'epl-wpimport' );

	$notification_label = __( 'Record Skipped', 'epl-wpimport' );

	$post_title = '';

	if ( false !== $post_id ) {
		$post_title = get_the_title( $post_id );
		$post_title = ' `' . $post_title . '`';

	}

	if ( 'update' === $notification ) {
		$notification_label = __( 'Date modified, updating...', 'epl-wpimport' );
	}

	if ( 'modified' === $notification ) {
		$notification_label = __( 'Modified Listing, updating...', 'epl-wpimport' );
	}

	if ( 'update_field' === $notification ) {
		$notification_label = __( 'Updating Field...', 'epl-wpimport' );
	}

	if ( 'skip_unchanged' === $notification ) {
		$notification_label = __( 'Listing Modified Time Unchanged, Skipping Record Update.', 'epl-wpimport' );
	}

	if ( 'updating' === $notification ) {
		$notification_label = __( 'Updating Fields:', 'epl-wpimport' );
	}

	if ( 'skip' === $notification ) {
		$notification_label = __( 'Skipped, previously imported record found for:', 'epl-wpimport' );
	}

	// Output.
	$epl_wpimport->log( $epl_wpimport_label . ': ' . $notification_label . ' ' . $post_title );
}

/**
 * Only update post of mod date is newer
 *
 * @param string $pid      Post unique ID.
 * @param array  $xml_node XML node.
 *
 * @return bool
 * @since  1.0
 */
function epl_wpimport_is_post_to_update_depreciated( $pid, $xml_node ) {
	global $epl_wpimport;

	$epl_wpimport->log( __( 'EPL IMPORTER', 'epl-wpimport' ) . ': ' . __( 'Deprecated version running', 'epl-wpimport' ) );
	$live_import = function_exists( 'epl_get_option' ) ? epl_get_option( 'epl_wpimport_skip_update' ) : 'off';

	$prop_mod_date = get_post_meta( $pid, 'property_mod_date', true );

	if ( 'on' === $live_import && ! empty( $prop_mod_date ) ) {
		/**
*
	* Only update posts if new data is available
*/
		$postmodtime   = epl_feedsync_format_date( get_post_meta( $pid, 'property_mod_date', true ) );
		$updatemodtime = epl_feedsync_format_date( $xml_node['@attributes']['modTime'] );
		$updatemodtime = apply_filters( 'epl_import_mod_time', $updatemodtime, $xml_node, $pid );

		if ( strtotime( $updatemodtime ) > strtotime( $postmodtime ) ) {

			epl_wpimport_notification( 'update', $pid );

			// update.
			return true;
		}

		epl_wpimport_notification( 'skip_unchanged', $pid );

		// Don't update.
		return false;
	}

	epl_wpimport_notification( 'skip', $pid );

	// Don't update.
	return true;
}

/**
 * Only update post of mod date is newer for WP ALL Import Pro version > = 4.5.0
 *
 * @param string $continue_import Continue or not.
 * @param string $pid             Post ID.
 * @param string $xml_node        XML node.
 * @param string $import_id       Import ID.
 *
 * @return bool
 * @since  1.0
 */
function epl_wpimport_is_post_to_update( $continue_import, $pid, $xml_node, $import_id ) {
	global $epl_wpimport;

	$epl_wpimport->log( __( 'EPL IMPORTER', 'epl-wpimport' ) . ': ' . __( 'Active', 'epl-wpimport' ) );

	$live_import = function_exists( 'epl_get_option' ) ? epl_get_option( 'epl_wpimport_skip_update' ) : 'off';

	$prop_mod_date = get_post_meta( $pid, 'property_mod_date', true );

	if ( 'on' === $live_import && ! empty( $prop_mod_date ) ) {
		// Only update posts if new data is available.
		$postmodtime   = epl_feedsync_format_date( get_post_meta( $pid, 'property_mod_date', true ) );
		$updatemodtime = epl_feedsync_format_date( $xml_node['@attributes']['modTime'] );
		$updatemodtime = apply_filters( 'epl_import_mod_time', $updatemodtime, $xml_node, $pid );

		if ( strtotime( $updatemodtime ) > strtotime( $postmodtime ) ) {

			epl_wpimport_notification( 'update', $pid );

			// update.
			return true;
		}

		epl_wpimport_notification( 'skip_unchanged', $pid );

		// Don't update.
		return false;
	}

	epl_wpimport_notification( 'skip', $pid );

	// Don't update.
	return true;
}
if ( defined( 'PMXI_VERSION' ) && version_compare( PMXI_VERSION, '4.5.0', '<' ) ) {
	add_filter( 'wp_all_import_is_post_to_update', 'epl_wpimport_is_post_to_update_depreciated', 10, 2 );
} else {
	add_filter( 'wp_all_import_is_post_to_update', 'epl_wpimport_is_post_to_update', 10, 4 );
}

/**
 * Format Date function for EAC API
 *
 * @param string $date The date.
 * @param string $sep  The date separator.
 *
 * @return false|string|void
 * @since  1.0.7
 */
function epl_feedsync_format_date_eac( $date, $sep = '/' ) {
	if ( empty( $date ) ) {
		return;
	}

	$date_example = '4/11/2015';

	$tempdate = explode( $sep, $date );
	$date     = $tempdate[0] . '-' . $tempdate[1] . '-' . $tempdate[2] . ' ' . $tempdate[3];

	return date( 'Y-m-d H:i:s', strtotime( $date ) );
}
// Usage in import script: [epl_feedsync_format_date_eac({ORIG_LDATE[1]},'/')] - Will format the date.
