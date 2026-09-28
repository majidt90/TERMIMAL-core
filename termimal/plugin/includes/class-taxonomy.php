<?php
/**
 * Product category taxonomy.
 *
 * @package Termimal_Core
 */

defined( 'ABSPATH' ) || exit;

class Termimal_Taxonomy {

	const TAXONOMY = 'product_category';

	public static function register() {
		$labels = array(
			'name'              => _x( 'Categories', 'taxonomy general name', 'termimal' ),
			'singular_name'     => _x( 'Category', 'taxonomy singular name', 'termimal' ),
			'search_items'      => __( 'Search Categories', 'termimal' ),
			'all_items'         => __( 'All Categories', 'termimal' ),
			'parent_item'       => __( 'Parent Category', 'termimal' ),
			'parent_item_colon' => __( 'Parent Category:', 'termimal' ),
			'edit_item'         => __( 'Edit Category', 'termimal' ),
			'update_item'       => __( 'Update Category', 'termimal' ),
			'add_new_item'      => __( 'Add New Category', 'termimal' ),
			'new_item_name'     => __( 'New Category Name', 'termimal' ),
			'menu_name'         => __( 'Categories', 'termimal' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'product-category', 'with_front' => false ),
			'show_in_rest'      => true,
			'rest_base'         => 'product-categories',
		);

		register_taxonomy( self::TAXONOMY, array( 'termimal_product' ), $args );
	}
}
