<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Realia_Api
 *
 * @class Realia_Api
 * @package Realia/Classes
 * @author Pragmatic Mates
 */
class Realia_Api {
	/**
	 * Initialize API
	 *
	 * @access public
	 * @return void
	 */
	public static function init() {
		add_filter( 'pre_get_posts', array( __CLASS__, 'filter_fields' ) );
		add_filter( 'json_prepare_post', array( __CLASS__, 'add_fields' ), 10, 3 );

	}

	public static function add_fields( $post_response, $post, $context ) {
		if ( $post['post_type'] != 'property' ) {
			return $post_response;
		}

		// Property ID
		$post_response['property_id'] = get_post_meta( $post['ID'], REALIA_PROPERTY_PREFIX . 'id', true );

		// Year built
		$post_response['year_built'] = get_post_meta( $post['ID'], REALIA_PROPERTY_PREFIX . 'year_built', true );

		// Reduced
		$reduced = get_post_meta( $post['ID'], REALIA_PROPERTY_PREFIX . 'reduced', true );
		if ( $reduced == 'on' ) {
			$post_response['reduced'] = true;
		} else {
			$post_response['reduced'] = false;
		}

		// Featured
		$featured = get_post_meta( $post['ID'], REALIA_PROPERTY_PREFIX . 'featured', true );
		if ( $featured == 'on' ) {
			$post_response['featured'] = true;
		} else {
			$post_response['featured'] = false;
		}

		// Sticky
		$sticky = get_post_meta( $post['ID'], REALIA_PROPERTY_PREFIX . 'sticky', true );
		if ( $sticky == 'on' ) {
			$post_response['sticky'] = true;
		} else {
			$post_response['sticky'] = false;
		}

		// Sold
		$sold = get_post_meta( $post['ID'], REALIA_PROPERTY_PREFIX . 'sold', true );
		if ( $sold == 'on' ) {
			$post_response['sold'] = true;
		} else {
			$post_response['sold'] = false;
		}

		// Contract
		$post_response['contract'] = get_post_meta( $post['ID'], REALIA_PROPERTY_PREFIX . 'contract', true );

		// Gallery
		$post_response['gallery'] = get_post_meta( $post['ID'], REALIA_PROPERTY_PREFIX . 'gallery', true );

		// Price
		$post_response['price'] = Realia_Price::get_property_price( $post['ID'] );

		// Rooms
		$post_response['rooms'] = get_post_meta( $post['ID'], REALIA_PROPERTY_PREFIX . 'attributes_rooms', true );

		// Beds
		$post_response['beds'] = get_post_meta( $post['ID'], REALIA_PROPERTY_PREFIX . 'attributes_beds', true );

		// Baths
		$post_response['baths'] = get_post_meta( $post['ID'], REALIA_PROPERTY_PREFIX . 'attributes_baths', true );

		// Garages
		$post_response['garages'] = get_post_meta( $post['ID'], REALIA_PROPERTY_PREFIX . 'attributes_garages', true );

		// Home area
		$home_area = get_post_meta( $post['ID'], REALIA_PROPERTY_PREFIX . 'home_area', true );
		if ( ! empty( $home_area ) ) {
			$post_response['home_area'] = $home_area . ' ' . get_theme_mod( 'realia_measurement_area_unit', 'sqft' );
		} else {
			$post_response['home_area'] = '';
		}

		// Lot dimensions
		$lot_dimensions = get_post_meta( $post['ID'], REALIA_PROPERTY_PREFIX . 'lot_dimensions', true );
		if ( ! empty( $lot_area ) ) {
			$post_response['lot_dimensions'] = $lot_dimensions . ' ' . get_theme_mod( 'realia_measurement_distance_unit', 'ft' );
		} else {
			$post_response['lot_dimensions'] = '';
		}

		// Lot area
		$lot_area = get_post_meta( $post['ID'], REALIA_PROPERTY_PREFIX . 'lot_area', true );
		if ( ! empty( $lot_area ) ) {
			$post_response['lot_area'] = $lot_area . ' ' . get_theme_mod( 'realia_measurement_area_unit', 'sqft' );
		} else {
			$post_response['lot_area'] = '';
		}

		// Map location
		$location = get_post_meta( $post['ID'], REALIA_PROPERTY_PREFIX . 'map_location', true );

		$post_response['map_location'] = array(
			'latitude'  => ! empty( $location['latitude'] ) ? floatval( $location['latitude'] ) : null,
			'longitude' => ! empty( $location['longitude'] ) ? floatval( $location['longitude'] ) : null,
		);

		return $post_response;
	}

	/**
	 * Adds filtering option into WP API
	 *
	 * @access public
	 * @param $query
	 * @return mixed
	 */
	public static function filter_fields( $query ) {
		if ( defined( 'JSON_REQUEST' ) && JSON_REQUEST ) {
			$query = Realia_Filter::filter_query( $query );
		}

		return $query;
	}
}

Realia_Api::init();