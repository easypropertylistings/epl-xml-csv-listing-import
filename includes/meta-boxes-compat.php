<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Variables List required for meta boxes
 *
 * @since 1.0
 */
function epl_allimport_get_meta_fields() {
	global $epl_settings;
	$opts_property_status = apply_filters (  'epl_opts_property_status_filter', array(
			'current'	=>	__('Current', 'epl-wpimport'),
			'withdrawn'	=>	__('Withdrawn', 'epl-wpimport'),
			'offmarket'	=>	__('Off Market', 'epl-wpimport'),
			'sold'		=>	array(
				'label'		=>	apply_filters( 'epl_sold_label_status_filter' , __('Sold', 'epl-wpimport') ),
				'exclude'	=>	array('rental')
			),
			'leased'		=>	array(
				'label'		=>	apply_filters( 'epl_leased_label_status_filter' , __('Leased', 'epl-wpimport') ),
				'include'	=>	array('rental', 'commercial', 'commercial_land', 'business')
			)
		)
	);
	$opts_property_authority = apply_filters (  'epl_property_authority_filter', array(
			'exclusive'	=>	__('Exclusive', 'epl-wpimport'),
			'auction'	=>	__('Auction', 'epl-wpimport'),
			'multilist'	=>	__('Multilist', 'epl-wpimport'),
			'conjunctional'	=>	__('Conjunctional', 'epl-wpimport'),
			'open'		=>	__('Open', 'epl-wpimport'),
			'sale'		=>	__('Sale', 'epl-wpimport'),
			'setsale'	=>	__('Set Sale', 'epl-wpimport')
		)
	);
	$opts_property_exclusivity = apply_filters (  'epl_opts_property_exclusivity_filter', array(
			'exclusive'	=>	__('Exclusive', 'epl-wpimport'),
			'open'		=>	__('Open', 'epl-wpimport')
		)
	);
	$opts_property_com_authority = apply_filters (  'epl_opts_property_com_authority_filter', array(
			'Forsale'	=>	__('For Sale', 'epl-wpimport'),
			'auction'	=>	__('Auction', 'epl-wpimport'),
			'tender'	=>	__('Tender', 'epl-wpimport'),
			'eoi'		=>	__('EOI', 'epl-wpimport'),
			'Sale'		=>	__('Sale', 'epl-wpimport'),
			'offers'	=>	__('Offers', 'epl-wpimport')
		)
	);
	$opts_area_unit = apply_filters (  'epl_opts_area_unit_filter', array(
			'square'	=>	__('Square', 'epl-wpimport'),
			'squareMeter'	=>	__('Square Meter', 'epl-wpimport'),
			'acre'		=>	__('Acre', 'epl-wpimport'),
			'hectare'	=>	__('Hectare', 'epl-wpimport'),
			'sqft'		=>	__('Square Feet', 'epl-wpimport')
		)
	);
	$opts_rent_period = apply_filters (  'epl_opts_rent_period_filter', array(
			'day'		=>	__('Day', 'epl-wpimport'),
			'daily'		=>	__('Daily', 'epl-wpimport'),
			'week'		=>	__('Week', 'epl-wpimport'),
			'weekly'	=>	__('Weekly', 'epl-wpimport'),
			'month'		=>	__('Month', 'epl-wpimport'),
			'monthly'	=>	__('Monthly', 'epl-wpimport')
		)
	);
	$opts_property_com_listing_type = apply_filters (  'epl_opts_property_com_listing_type_filter', array(
			'sale'		=>	__('Sale', 'epl-wpimport'),
			'lease'		=>	__('Lease', 'epl-wpimport'),
			'both'		=>	__('Both', 'epl-wpimport')
		)
	);
	$opts_property_com_tenancy = apply_filters (  'epl_opts_property_com_tenancy_filter', array(
			'unknown'	=>	__('Unknown', 'epl-wpimport'),
			'vacant'	=>	__('Vacant', 'epl-wpimport'),
			'tenanted'	=>	__('Tenanted', 'epl-wpimport')
		)
	);
	$opts_property_com_property_extent = apply_filters (  'epl_opts_property_com_property_extent_filter', array(
			'whole'		=>	__('Whole', 'epl-wpimport'),
			'part'		=>	__('Part', 'epl-wpimport')
		)
	);

	global $epl_allimport_meta_boxes;
	$epl_allimport_meta_boxes = array(

		array(
			'id'		=>	'epl-property-listing-section-id',
			'label'		=>	__('Listing Details', 'epl-wpimport'),
			'post_type'	=>	array('property', 'rural', 'rental', 'land', 'commercial', 'commercial_land', 'business'),
			'context'	=>	'normal',
			'priority'	=>	'default',
			'groups'	=>	array(
				array(
					'id'		=>	'property_heading',
					'columns'	=>	'1',
					'label'		=>	'',
					'fields'	=>	array(
						array(
							'name'		=>	'property_heading',
							'label'		=>	__('Heading', 'epl-wpimport'),
							'type'		=>	'text',
							'maxlength'	=>	'200'
						)
					)
				),

				array(
					'id'		=>	'listing_agents',
					'columns'	=>	'1',
					'label'		=>	__('Listing Agent(s)', 'epl-wpimport'),
					'fields'	=>	array(
						array(
							'name'		=>	'property_office_id',
							'label'		=>	__('Office ID', 'epl-wpimport'),
							'type'		=>	'text',
							'maxlength'	=>	'50'
						),

						array(
							'name'		=>	'property_agent',
							'label'		=>	__('Listing Agent', 'epl-wpimport'),
							'type'		=>	'text',
							'maxlength'	=>	'40'
						),

						array(
							'name'		=>	'property_second_agent',
							'label'		=>	__('Second Listing Agent', 'epl-wpimport'),
							'type'		=>	'text',
							'maxlength'	=>	'40',
							'help'		=>	__('Search for secondary agent.','epl-wpimport')
						),

						array(
							'name'		=>	'property_agent_hide_author_box',
							'label'		=>	__('Hide Author Box', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'		=>	array(
								'yes'	=>	__('Hide Author Box', 'epl-wpimport'),
							)
						)
					)
				),

				array(
					'id'		=>	'listing_type',
					'columns'	=>	'2',
					'label'		=>	__('Listing Type', 'epl-wpimport'),
					'fields'	=>	array(
						array(
							'name'		=>	'property_status',
							'label'		=>	__('Property Status', 'epl-wpimport'),
							'type'		=>	'select',
							'opts'		=>	$opts_property_status
						),

						array(
							'name'		=>	'property_list_date',
							'label'		=>	__('Date Listed', 'epl-wpimport'),
							'type'		=>	'date',
							'maxlength'	=>	'100'
						),

						array(
							'name'		=>	'property_authority',
							'label'		=>	__('Authority', 'epl-wpimport'),
							'type'		=>	'select',
							'opts'		=>	$opts_property_authority,
							'exclude'	=>	array('rental', 'commercial', 'commercial_land')
						),

						array(
							'name'		=>	'property_category',
							'label'		=>	__('House Category', 'epl-wpimport'),
							'type'		=>	'select',
							'opts'		=>	epl_listing_load_meta_property_category(),
							'exclude'	=>	array('land', 'commercial', 'commercial_land', 'business', 'rural')
						),

						array(
							'name'		=>	'property_rural_category',
							'label'		=>	__('Rural Category', 'epl-wpimport'),
							'type'		=>	'select',
							'opts'		=>	epl_listing_load_meta_rural_category(),
							'include'	=>	array('rural')
						),

						array(
							'name'		=>	'property_unique_id',
							'label'		=>	__('Unique ID', 'epl-wpimport'),
							'type'		=>	'text',
							'maxlength'	=>	'50'
						),

						array(
							'name'		=>	'property_mod_date',
							'label'		=>	__('XML Importer Mod Date', 'epl-wpimport'),
							'type'		=>	'text',
							'maxlength'	=>	'60'
						),

						array(
							'name'		=>	'property_images_mod_date',
							'label'		=>	'',
							'type'		=>	'hidden',
							'maxlength'	=>	'60'
						),

						array(
							'name'		=>	'property_com_authority',
							'label'		=>	__('Commercial Authority', 'epl-wpimport'),
							'type'		=>	'select',
							'opts'		=>	$opts_property_com_authority,
							'include'	=>	array('commercial', 'commercial_land', 'business')
						),

						array(
							'name'		=>	'property_com_exclusivity',
							'label'		=>	__('Exclusivity', 'epl-wpimport'),
							'type'		=>	'select',
							'opts'		=>	$opts_property_exclusivity,
							'include'	=>	array('commercial', 'commercial_land', 'business')
						),

						array(
							'name'		=>	'property_com_listing_type',
							'label'		=>	__('Commercial Listing Type', 'epl-wpimport'),
							'type'		=>	'select',
							'opts'		=>	$opts_property_com_listing_type,
							'include'	=>	array('commercial', 'commercial_land' , 'business' )
						),

						array(
							'name'		=>	'property_commercial_category',
							'label'		=>	__('Commercial Category', 'epl-wpimport'),
							'type'		=>	'select',
							'opts'		=>	epl_listing_load_meta_commercial_category(),
							'include'	=>	array('commercial', 'commercial_land')
						),
					)
				),

				array(
					'id'		=>	'display_details',
					'columns'	=>	'2',
					'label'		=>	__('Display Details', 'epl-wpimport'),
					'fields'	=>	array(
						array(
							'name'		=>	'property_featured',
							'label'		=>	__('Featured', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_inspection_times',
							'label'		=>	__('Inspection Times ( one per line )', 'epl-wpimport'),
							'type'		=>	'textarea',
							'maxlength'	=>	'500'
						)
					)
				)
			)
		),

		array(
			'id'		=>	'epl-features-section-id',
			'label'		=>	__('Listing Features', 'epl-wpimport'),
			'post_type'	=>	array('property', 'rural', 'rental' ),
			'context'	=>	'normal',
			'priority'	=>	'default',
			'groups'	=>	array(
				array(
					'id'		=>	'house_features',
					'columns'	=>	'2',
					'label'		=>	__('House Features', 'epl-wpimport'),
					'fields'	=>	array(
						array(
							'name'		=>	'property_bedrooms',
							'label'		=>	__('Bedrooms', 'epl-wpimport'),
							'type'		=>	'text',
							'class'		=>	'validate[custom[bedroom]]'
						),

						array(
							'name'		=>	'property_bathrooms',
							'label'		=>	__('Bathrooms', 'epl-wpimport'),
							'type'		=>	'text',
							'maxlength'	=>	'4'
						),

						array(
							'name'		=>	'property_rooms',
							'label'		=>	__('Rooms', 'epl-wpimport'),
							'type'		=>	'number',
							'maxlength'	=>	'3'
						),

						array(
							'name'		=>	'property_ensuite',
							'label'		=>	__('Ensuite', 'epl-wpimport'),
							'type'		=>	'number',
							'maxlength'	=>	'2'
						),

						array(
							'name'		=>	'property_toilet',
							'label'		=>	__('Toilet', 'epl-wpimport'),
							'type'		=>	'text',
							'maxlength'	=>	'4'
						),

						array(
							'name'		=>	'property_garage',
							'label'		=>	__('Garage', 'epl-wpimport'),
							'type'		=>	'number',
							'maxlength'	=>	'2'
						),

						array(
							'name'		=>	'property_carport',
							'label'		=>	__('Carport', 'epl-wpimport'),
							'type'		=>	'number',
							'maxlength'	=>	'2'
						),

						array(
							'name'		=>	'property_open_spaces',
							'label'		=>	__('Open Spaces', 'epl-wpimport'),
							'type'		=>	'number',
							'maxlength'	=>	'2'
						),

						array(
							'name'		=>	'property_year_built',
							'label'		=>	__('Year Built', 'epl-wpimport'),
							'type'		=>	'text',
							'maxlength'	=>	'4'
						),

						array(
							'name'		=>	'property_new_construction',
							'label'		=>	__('New Construction', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
							'include'	=>	array('property', 'rental' )
						),

						array(
							'name'		=>	'property_pool',
							'label'		=>	__('Pool', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_air_conditioning',
							'label'		=>	__('Air Conditioning', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_security_system',
							'label'		=>	__('Security System', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_pet_friendly',
							'label'		=>	__('Pet Friendly', 'epl-wpimport'),
							'type'		=>	'radio',
							'include'	=>	array('rental'),
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						)
					)
				),

				array(
					'id'		=>	'land_details',
					'columns'	=>	'2',
					'label'		=>	__('Land Details', 'epl-wpimport'),
					'fields'	=>	array(
						array(
							'name'		=>	'property_land_area',
							'label'		=>	__('Land Area', 'epl-wpimport'),
							'type'		=>	'text',
							'maxlength'	=>	'50'
						),

						array(
							'name'		=>	'property_land_area_unit',
							'label'		=>	__('Land Unit', 'epl-wpimport'),
							'type'		=>	'select',
							'opts'		=>	$opts_area_unit
						),

						array(
							'name'		=>	'property_building_area',
							'label'		=>	__('Building Area', 'epl-wpimport'),
							'type'		=>	'text',
							'maxlength'	=>	'50'
						),

						array(
							'name'		=>	'property_building_area_unit',
							'label'		=>	__('Building Unit', 'epl-wpimport'),
							'type'		=>	'select',
							'opts'		=>	$opts_area_unit
						),

						array(
							'name'		=>	'property_land_fully_fenced',
							'label'		=>	__('Fully Fenced', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						)
					)
				)
			)
		),

		array( // Additional Features
			'id'		=>	'epl-additional-features-section-id',
			'label'		=>	__('Additional Features', 'epl-wpimport'),
			'post_type'	=>	array('property', 'rural', 'rental' ),
			'context'	=>	'normal',
			'priority'	=>	'default',
			'groups'	=>	array(
				array(
					'id'		=>	'internal',
					'columns'	=>	'3',
					'label'		=>	__('Internal', 'epl-wpimport'),
					'fields'	=>	array(

						array(
							'name'		=>	'property_remote_garage',
							'label'		=>	__('Remote Garage', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_secure_parking',
							'label'		=>	__('Secure Parking', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_study',
							'label'		=>	__('Study', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_dishwasher',
							'label'		=>	__('Dishwasher', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_built_in_robes',
							'label'		=>	__('Built In Robes', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_gym',
							'label'		=>	__('Gym', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_workshop',
							'label'		=>	__('Workshop', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_rumpus_room',
							'label'		=>	__('Rumpus Room', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_floor_boards',
							'label'		=>	__('Floor Boards', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_broadband',
							'label'		=>	__('Broadband', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_pay_tv',
							'label'		=>	__('Pay TV', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),
						array(
							'name'		=>	'property_vacuum_system',
							'label'		=>	__('Vacuum System', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_intercom',
							'label'		=>	__('Intercom', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_spa',
							'label'		=>	__('Spa', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						)
					)
				),

				array(
					'id'		=>	'external',
					'columns'	=>	'3',
					'label'		=>	__('External', 'epl-wpimport'),
					'fields'	=>	array(

						array(
							'name'		=>	'property_tennis_court',
							'label'		=>	__('Tennis Court', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_balcony',
							'label'		=>	__('Balcony', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_deck',
							'label'		=>	__('Deck', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_courtyard',
							'label'		=>	__('Courtyard', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_outdoor_entertaining',
							'label'		=>	__('Outdoor Entertaining', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_shed',
							'label'		=>	__('Shed', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						)
					)
				),

				array(
					'id'		=>	'heating_cooling',
					'columns'	=>	'3',
					'label'		=>	__('Heating & Cooling', 'epl-wpimport'),
					'fields'	=>	array(

						array(
							'name'		=>	'property_ducted_heating',
							'label'		=>	__('Ducted Heating', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_ducted_cooling',
							'label'		=>	__('Ducted Cooling', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_split_system_heating',
							'label'		=>	__('Split System Heating', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_hydronic_heating',
							'label'		=>	__('Hydronic Heating', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_split_system_aircon',
							'label'		=>	__('Split System Aircon', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_gas_heating',
							'label'		=>	__('Gas Heating', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_reverse_cycle_aircon',
							'label'		=>	__('Reverse Cycle Aircon', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_evaporative_cooling',
							'label'		=>	__('Evaporative Cooling', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_open_fire_place',
							'label'		=>	__('Open Fire Place', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						)
					)
				)
			)
		),

		array( //Repeating most from above "epl-features-section-id" because on land it will be single column
			'id'		=>	'epl-features-section-id-single-column',
			'label'		=>	__('Land Details', 'epl-wpimport'),
			'post_type'	=>	array('land', 'commercial', 'business'),
			'context'	=>	'normal',
			'priority'	=>	'default',
			'groups'	=>	array(
				array(
					'id'		=>	'land_details',
					'columns'	=>	'1',
					'label'		=>	'',
					'fields'	=>	array(
						array(
							'name'		=>	'property_land_area',
							'label'		=>	__('Land Area', 'epl-wpimport'),
							'type'		=>	'text',
							'maxlength'	=>	'50'
						),

						array(
							'name'		=>	'property_land_area_unit',
							'label'		=>	__('Land Unit', 'epl-wpimport'),
							'type'		=>	'select',
							'opts'		=>	$opts_area_unit
						),

						array(
							'name'		=>	'property_building_area',
							'label'		=>	__('Building Area', 'epl-wpimport'),
							'type'		=>	'number',
							'include'	=>	array('commercial','business'),
							'maxlength'	=>	'40'
						),

						array(
							'name'		=>	'property_building_area_unit',
							'label'		=>	__('Building Unit', 'epl-wpimport'),
							'type'		=>	'select',
							'opts'		=>	$opts_area_unit,
							'include'	=>	array('commercial','business')
						),

						array(
							'name'		=>	'property_land_category',
							'label'		=>	__('Land Category', 'epl-wpimport'),
							'type'		=>	'select',
							'opts'		=>	epl_listing_load_meta_land_category(),
							'include'	=>	array('land')
						),

						array(
							'name'		=>	'property_land_fully_fenced',
							'label'		=>	__('Fully Fenced', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
							'include'	=>	array('land')
						)
					)
				)
			)
		),

		array(
			'id'		=>	'epl-property-address-section-id',
			'label'		=>	__('Property Address', 'epl-wpimport'),
			'post_type'	=>	array('property', 'rural', 'rental', 'commercial', 'commercial_land', 'business', 'land','contact_listing'),
			'context'	=>	'side',
			'priority'	=>	'core',
			'groups'	=>	array(	apply_filters('epl_listing_meta_address_block',
					array(
						'id'		=>	'address_block',
						'columns'	=>	'1',
						'label'		=>	'',
						'fields'	=>	array(
							array(
								'name'		=>	'property_address_display',
								'label'		=>	__('Display Street Address?', 'epl-wpimport'),
								'type'		=>	'radio',
								'opts'		=>	array(
									'yes'	=>	__('Yes', 'epl-wpimport'),
								),
							),

							array(
								'name'		=>	'property_address_lot_number',
								'label'		=>	__('Lot', 'epl-wpimport'),
								'type'		=>	'text',
								'maxlength'	=>	'40',
								'include'	=>	array('land', 'commercial_land')
							),

							array(
								'name'		=>	'property_address_sub_number',
								'label'		=>	__('Unit', 'epl-wpimport'),
								'type'		=>	'text',
								'maxlength'	=>	'40',
								'exclude'	=>	array('land', 'commercial_land')
							),

							array(
								'name'		=>	'property_address_street_number',
								'label'		=>	__('Street Number', 'epl-wpimport'),
								'type'		=>	'text',
								'maxlength'	=>	'40'
							),

							array(
								'name'		=>	'property_address_street',
								'label'		=>	__('Street Name', 'epl-wpimport'),
								'type'		=>	'text',
								'maxlength'	=>	'80'
							),

							array(
								'name'		=>	'property_address_suburb',
								'label'		=>	epl_labels('label_suburb'),
								'type'		=>	'text',
								'maxlength'	=>	'80'
							),

							array(
								'name'		=>	'property_com_display_suburb',
								'label'		=>	__('Display', 'epl-wpimport') . ' ' .epl_labels('label_suburb'),
								'type'		=>	'radio',
								'opts'		=>	array(
									'yes'	=>	__('Yes', 'epl-wpimport'),
								),
								'include'	=>	array('commercial', 'commercial_land', 'business'),
							),

							( isset($epl_settings['epl_enable_city_field'] ) &&  $epl_settings['epl_enable_city_field'] == 'yes' ) ?
							array(
								'name'		=>	'property_address_city',
								'label'		=>	epl_labels('label_city'),
								'type'		=>	'text',
								'maxlength'	=>	'80'
							) : array(),

							array(
								'name'		=>	'property_address_state',
								'label'		=>	epl_labels('label_state'),
								'type'		=>	'text',
								'maxlength'	=>	'80'
							),

							array(
								'name'		=>	'property_address_postal_code',
								'label'		=>	epl_labels('label_postcode'),
								'type'		=>	'text',
								'maxlength'	=>	'30'
							),

							array(
								'name'		=>	'property_address_country',
								'label'		=>	__('Country', 'epl-wpimport'),
								'type'		=>	'text',
								'maxlength'	=>	'40'
							),

							array(
								'name'		=>	'property_address_coordinates',
								'label'		=>	__('Coordinates', 'epl-wpimport'),
								'type'		=>	'text',
								'help'		=>	__('Drag the pin to manually set listing coordinates', 'epl-wpimport'),
								'geocoder'	=>	'true',
								'maxlength'	=>	'40'
							),
							array(
								'name'		=>	'property_address_hide_map',
								'label'		=>	__('Hide Map', 'epl-wpimport'),
								'type'		=>	'radio',
								'opts'		=>	array(
									'yes'	=>	__('Yes', 'epl-wpimport'),
								)
							)
						)
					)
				)
			)
		),

		array(
			'id'		=>	'epl-pricing-section-id',
			'label'		=>	__('Pricing', 'epl-wpimport'),
			'post_type'	=>	array('property', 'rural', 'commercial', 'commercial_land', 'business', 'land'),
			'context'	=>	'side',
			'priority'	=>	'core',
			'groups'	=>	array(
				array(
					'id'		=>	'pricing',
					'columns'	=>	'1',
					'label'		=>	'',
					'fields'	=>	array(
						array(
							'name'		=>	'property_price',
							'label'		=>	__('Search Price', 'epl-wpimport'),
							'type'		=>	'text',
							'maxlength'	=>	'50'
						),

						array(
							'name'		=>	'property_price_view',
							'label'		=>	__('Price Text', 'epl-wpimport'),
							'type'		=>	'text',
							'maxlength'	=>	'50'
						),

						array(
							'name'		=>	'property_auction',
							'label'		=>	__('Auction Date', 'epl-wpimport'),
							'type'		=>	'auction-date',
							'maxlength'	=>	'100'
						),

						array(
							'name'		=>	'property_price_display',
							'label'		=>	__('Display Price?', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_under_offer',
							'label'		=>	epl_meta_under_offer_label(),
							'type'		=>	'radio',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_is_home_land_package',
							'label'		=>	__('House and Land Package', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
							'exclude'	=>	array('land', 'rural', 'commercial', 'commercial_land' , 'business')
						)
					)
				),

				array(
					'id'		=>	'sale_details',
					'columns'	=>	'1',
					'label'		=>	__('Sale Details', 'epl-wpimport'),
					'fields'	=>	array(
						array(
							'name'		=>	'property_sold_price',
							'label'		=>	__('Sale Price', 'epl-wpimport'),
							'type'		=>	'text',
							'maxlength'	=>	'50'
						),

						array(
							'name'		=>	'property_sold_date',
							'label'		=>	__('Sale Date', 'epl-wpimport'),
							'type'		=>	'sold-date',
							'maxlength'	=>	'100'
						),

						array(
							'name'		=>	'property_sold_price_display',
							'label'		=>	__('Display Sale Price', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						)
					)
				)
			)
		),

		array(
			'id'		=>	'epl-property-rent-id',
			'label'		=>	__('Rental Pricing', 'epl-wpimport'),
			'post_type'	=>	array('rental'),
			'context'	=>	'side',
			'priority'	=>	'core',
			'groups'	=>	array(
				array(
					'id'		=>	'rental_pricing',
					'columns'	=>	'1',
					'label'		=>	'',
					'fields'	=>	array(
						array(
							'name'		=>	'property_rent',
							'label'		=>	__('Rent Amount', 'epl-wpimport'),
							'type'		=>	'text',
							'maxlength'	=>	'40'
						),

						array(
							'name'		=>	'property_rent_period',
							'label'		=>	__('Rent Period', 'epl-wpimport'),
							'type'		=>	'select',
							'opts'		=>	$opts_rent_period
						),

						array(
							'name'		=>	'property_rent_view',
							'label'		=>	__('Rent Text', 'epl-wpimport'),
							'type'		=>	'text',
							'maxlength'	=>	'50'
						),

						array(
							'name'		=>	'property_rent_display',
							'label'		=>	__('Display Rent?', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_bond',
							'label'		=>	epl_labels('label_bond'),
							'type'		=>	'text',
							'maxlength'	=>	'40'
						),

						array(
							'name'		=>	'property_date_available',
							'label'		=>	__('Date Available', 'epl-wpimport'),
							'type'		=>	'date',
							'maxlength'	=>	'100'
						),

						array(
							'name'		=>	'property_furnished',
							'label'		=>	__('Furnished', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
						),

						array(
							'name'		=>	'property_holiday_rental',
							'label'		=>	__('Holiday Rental', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
							'exclude'	=>	array('rental')
						),
					)
				)
			)
		),

		array(
			'id'		=>	'epl-rural-features-id',
			'label'		=>	__('Rural Features', 'epl-wpimport'),
			'post_type'	=>	array('rural'),
			'context'	=>	'normal',
			'priority'	=>	'default',
			'groups'	=>	array(
				array(
					'id'		=>	'rural_features',
					'columns'	=>	'1',
					'label'		=>	'',
					'fields'	=>	array(
						array(
							'name'		=>	'property_rural_fencing',
							'label'		=>	__('Fencing', 'epl-wpimport'),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						),

						array(
							'name'		=>	'property_rural_annual_rainfall',
							'label'		=>	__('Annual Rainfall', 'epl-wpimport'),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						),

						array(
							'name'		=>	'property_rural_soil_types',
							'label'		=>	__('Soil Types', 'epl-wpimport'),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						),

						array(
							'name'		=>	'property_rural_improvements',
							'label'		=>	__('Improvements', 'epl-wpimport'),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						),

						array(
							'name'		=>	'property_rural_council_rates',
							'label'		=>	__('Council Rates', 'epl-wpimport'),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						),

						array(
							'name'		=>	'property_rural_irrigation',
							'label'		=>	__('Irrigation', 'epl-wpimport'),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						),

						array(
							'name'		=>	'property_rural_carrying_capacity',
							'label'		=>	__('Carrying Capacity', 'epl-wpimport'),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						),

						array(
							'name'		=>	'property_rural_services',
							'label'		=>	__('Services', 'epl-wpimport'),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						)
					)
				)
			)
		),

		array(
			'id'		=>	'epl-commercial-leasing-id',
			'label'		=>	__('Leasing', 'epl-wpimport'),
			'post_type'	=>	array('commercial', 'commercial_land' , 'business'),
			'context'	=>	'normal',
			'priority'	=>	'default',
			'groups'	=>	array(
				array(
					'id'		=>	'commercial_leasing',
					'columns'	=>	'2',
					'label'		=>	'',
					'fields'	=>	array(
						array(
							'name'		=>	'property_com_rent',
							'label'		=>	__('Commercial Rent', 'epl-wpimport'),
							'type'		=>	'text',
							'maxlength'	=>	'40',
							'help'		=>	__('Price Text in Pricing box over-rides displayed price' , 'epl-wpimport')
						),
						array(
							'name'		=>	'property_com_rent_period',
							'label'		=>	__('Lease Period', 'epl-wpimport'),
							'type'		=>	'select',
							'opts'		=>	epl_listing_load_meta_commercial_rent_period()
						),
						array(
							'name'		=>	'property_com_rent_range_min',
							'label'		=>	__('Rent Range Min', 'epl-wpimport'),
							'type'		=>	'number',
							'maxlength'	=>	'40'
						),

						array(
							'name'		=>	'property_com_rent_range_max',
							'label'		=>	__('Rent Range Max', 'epl-wpimport'),
							'type'		=>	'number',
							'maxlength'	=>	'40'
						),

						array(
							'name'		=>	'property_com_lease_end_date',
							'label'		=>	__('Lease End Date', 'epl-wpimport'),
							'type'		=>	'date',
							'maxlength'	=>	'100'
						),

						array(
							'name'		=>	'property_com_property_extent',
							'label'		=>	__('Property Extent', 'epl-wpimport'),
							'type'		=>	'select',
							'opts'		=>	$opts_property_com_property_extent
						)
					)
				),

				array(
					'id'		=>	'tenant_n_outgoings',
					'columns'	=>	'2',
					'label'		=>	'',
					'fields'	=>	array(
						array(
							'name'		=>	'property_com_tenancy',
							'label'		=>	__('Tenant Status', 'epl-wpimport'),
							'type'		=>	'select',
							'opts'		=>	$opts_property_com_tenancy,
							'include'	=>	array('commercial')
						),

						array(
							'name'		=>	'property_com_outgoings',
							'label'		=>	__('Commercial Outgoings', 'epl-wpimport'),
							'type'		=>	'number',
							'maxlength'	=>	'40',
							'exclude'	=>	array('business')
						),

						array(
							'name'		=>	'property_com_plus_outgoings',
							'label'		=>	__('Plus Outgoings', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
							'exclude'	=>	array('business')
						),

						array(
							'name'		=>	'property_bus_takings',
							'label'		=>	__('Takings', 'epl-wpimport'),
							'type'		=>	'number',
							'maxlength'	=>	'40',
							'include'	=>	array('business')
						),

						array(
							'name'		=>	'property_bus_franchise',
							'label'		=>	__('Franchise', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							),
							'include'	=>	array('business')
						),

						array(
							'name'		=>	'property_com_return',
							'label'		=>	__('Return', 'epl-wpimport'),
							'type'		=>	'text',
							'maxlength'	=>	'6'
						),

						array(
							'name'		=>	'property_bus_terms',
							'label'		=>	__('Terms', 'epl-wpimport'),
							'type'		=>	'textarea'
						)
					)
				)
			)
		),

		array(
			'id'		=>	'epl-commercial-features-id',
			'label'		=>	__('Commercial Features', 'epl-wpimport'),
			'post_type'	=>	array('commercial'),
			'context'	=>	'normal',
			'priority'	=>	'default',
			'groups'	=>	array(
				array(
					'id'		=>	'commerial_features',
					'columns'	=>	'1',
					'label'		=>	'',
					'fields'	=>	array(
						array(
							'name'		=>	'property_com_further_options',
							'label'		=>	__('Further Options', 'epl-wpimport'),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						),

						array(
							'name'		=>	'property_com_zone',
							'label'		=>	__('Zone', 'epl-wpimport'),
							'type'		=>	'textarea',
							'maxlength'	=>	'150'
						),

						array(
							'name'		=>	'property_com_car_spaces',
							'label'		=>	__('Car Spaces', 'epl-wpimport'),
							'type'		=>	'number',
							'type'		=>	'textarea',
							'maxlength'	=>	'5'
						),

						array(
							'name'		=>	'property_com_highlight_1',
							'label'		=>	__('Highlight 1', 'epl-wpimport'),
							'type'		=>	'text',
							'maxlength'	=>	'40'
						),

						array(
							'name'		=>	'property_com_highlight_2',
							'label'		=>	__('Highlight 2', 'epl-wpimport'),
							'type'		=>	'text',
							'maxlength'	=>	'40'
						),

						array(
							'name'		=>	'property_com_highlight_3',
							'label'		=>	__('Highlight 3', 'epl-wpimport'),
							'type'		=>	'text',
							'maxlength'	=>	'40'
						),

						array(
							'name'		=>	'property_com_parking_comments',
							'label'		=>	__('Parking Comments', 'epl-wpimport'),
							'type'		=>	'text',
							'maxlength'	=>	'150'
						),

						array(
							'name'		=>	'property_com_is_multiple',
							'label'		=>	__('Is Multiple', 'epl-wpimport'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl-wpimport'),
							)
						)
					)
				)
			)
		),

		array(
			'id'		=>	'epl-business-features-id',
			'label'		=>	__('Business Categories', 'epl-wpimport'),
			'post_type'	=>	array('business'),
			'context'	=>	'normal',
			'priority'	=>	'default',
			'groups'	=>	array(
				array(
					'id'		=>	'business_categories',
					'columns'	=>	'1',
					'label'		=>	'',
					'fields'	=>	array(
						array(
							'name'		=>	'property_bus_category',
							'label'		=>	__('Business Category', 'epl-wpimport'),
							'type'		=>	'select',
							'opt_args'	=>	array(
								'type'	=>	'taxonomy',
								'slug'	=>	'tax_business_listing'
							)
						),

						array(
							'name'		=>	'property_bus_sub_category',
							'label'		=>	__('Business Sub Category', 'epl-wpimport'),
							'type'		=>	'select',
							'opt_args'	=>	array(
								'type'	=>	'taxonomy',
								'slug'	=>	'tax_business_listing',
								'parent'=>	'property_bus_category'
							)
						),

						array(
							'name'		=>	'property_bus_category_2',
							'label'		=>	__('Business Category 2', 'epl-wpimport'),
							'type'		=>	'select',
							'opt_args'	=>	array(
								'type'	=>	'taxonomy',
								'slug'	=>	'tax_business_listing'
							)
						),

						array(
							'name'		=>	'property_bus_sub_category_2',
							'label'		=>	__('Business Sub Category 2', 'epl-wpimport'),
							'type'		=>	'select',
							'opt_args'	=>	array(
								'type'	=>	'taxonomy',
								'slug'	=>	'tax_business_listing',
								'parent'=>	'property_bus_category_2'
							)
						),

						array(
							'name'		=>	'property_bus_category_3',
							'label'		=>	__('Business Category 3', 'epl-wpimport'),
							'type'		=>	'select',
							'opt_args'	=>	array(
								'type'	=>	'taxonomy',
								'slug'	=>	'tax_business_listing'
							)
						),

						array(
							'name'		=>	'property_bus_sub_category_3',
							'label'		=>	__('Business Sub Category 3', 'epl-wpimport'),
							'type'		=>	'select',
							'opt_args'	=>	array(
								'type'	=>	'taxonomy',
								'slug'	=>	'tax_business_listing',
								'parent'=>	'property_bus_category_3'
							)
						),
					)
				)
			)
		),

		array(
			'id'		=>	'epl-attachments-section-id',
			'label'		=>	__('Files and Links', 'epl-wpimport'),
			'post_type'	=>	array('property', 'rural', 'commercial', 'commercial_land', 'business', 'rental', 'land'),
			'context'	=>	'normal',
			'priority'	=>	'default',
			'groups'	=>	array(
				array(
					'id'		=>	'filen_n_links',
					'columns'	=>	'1',
					'label'		=>	'',
					'fields'	=>	array(
						array(
							'name'		=>	'property_video_url',
							'label'		=>	__('Video URL', 'epl-wpimport'),
							'type'		=>	'url'
						),

						array(
							'name'		=>	'property_floorplan',
							'label'		=>	__('Floorplan', 'epl-wpimport'),
							'type'		=>	'url'
						),
						array(
							'name'		=>	'property_floorplan_2',
							'label'		=>	__('Floorplan 2', 'epl-wpimport'),
							'type'		=>	'url'
						),

						array(
							'name'		=>	'property_external_link',
							'label'		=>	__('External Link', 'epl-wpimport'),
							'type'		=>	'url'
						),

						array(
							'name'		=>	'property_external_link_2',
							'label'		=>	__('External Link 2', 'epl-wpimport'),
							'type'		=>	'url'
						),
						array(
							'name'		=>	'property_external_link_3',
							'label'		=>	__('External Link 3', 'epl-wpimport'),
							'type'		=>	'url',
							'include'	=>	array('commercial', 'business', 'commercial_land'),
						),

						array(
							'name'		=>	'property_com_mini_web',
							'label'		=>	__('Mini Website URL', 'epl-wpimport'),
							'type'		=>	'url',
							'include'	=>	array('commercial', 'business', 'commercial_land'),
						),
						array(
							'name'		=>	'property_com_mini_web_2',
							'label'		=>	__('Mini Website URL 2', 'epl-wpimport'),
							'type'		=>	'url',
							'include'	=>	array('commercial', 'business', 'commercial_land'),
						),
						array(
							'name'		=>	'property_com_mini_web_3',
							'label'		=>	__('Mini Website URL 3', 'epl-wpimport'),
							'type'		=>	'url',
							'include'	=>	array('commercial', 'business', 'commercial_land'),
						),
					)
				)
			)
		),

		array(
			'id'		=>	'epl-owner-listings-section-id',
			'label'		=>	__('Linked Contact', 'epl-wpimport'),
			'post_type'	=>	array('property', 'rural', 'commercial', 'commercial_land', 'business', 'rental', 'land'),
			'context'	=>	'side',
			'priority'	=>	'default',
			'groups'	=>	array(
				array(
					'id'		=>	'owner_details',
					'columns'	=>	'1',
					'label'		=>	'',
					'fields'	=>	array(
						array(
							'name'		=>	'property_owner',
							'label'		=>	__('Contact ID','epl-wpimport'),
							'type'		=>	'text',
							'help'		=>	__('Search for contact and update to save.','epl-wpimport')
						),
					)
				)
			)
		),
	);

	if(!empty($epl_allimport_meta_boxes)) {
		foreach($epl_allimport_meta_boxes as &$epl_meta_box) {
			$meta_box_block_id = str_replace("-","_",$epl_meta_box['id']);
			$epl_meta_box = apply_filters('epl_meta_box_block_'.$meta_box_block_id,$epl_meta_box);
			if(!empty($epl_meta_box['groups'])) {
				foreach($epl_meta_box['groups'] as &$group) {
					$group = apply_filters('epl_meta_groups_'.$group['id'], $group);
					if(!empty($group['fields'])) {
						$group['fields'] = array_filter($group['fields']);
						foreach($group['fields'] as &$fieldvalue) {

							$fieldvalue = apply_filters('epl_meta_'.$fieldvalue['name'], $fieldvalue);
						}
					}

				}
			}
		}
		return $epl_allimport_meta_boxes = apply_filters('epl_listing_meta_boxes', $epl_allimport_meta_boxes);
	}
}
