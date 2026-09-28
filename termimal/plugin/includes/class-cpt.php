<?php
/**
 * Register Custom Post Type: termimal_product
 *
 * @package Termimal_Core
 */

defined( 'ABSPATH' ) || exit;

class Termimal_CPT {

	/**
	 * Register the Product post type.
	 */
	public static function register() {
		$labels = array(
			'name'                  => _x( 'Products', 'Post type general name', 'termimal' ),
			'singular_name'         => _x( 'Product', 'Post type singular name', 'termimal' ),
			'menu_name'             => _x( 'Products', 'Admin Menu text', 'termimal' ),
			'name_admin_bar'        => _x( 'Product', 'Add New on Toolbar', 'termimal' ),
			'add_new'               => __( 'Add New', 'termimal' ),
			'add_new_item'          => __( 'Add New Product', 'termimal' ),
			'new_item'              => __( 'New Product', 'termimal' ),
			'edit_item'             => __( 'Edit Product', 'termimal' ),
			'view_item'             => __( 'View Product', 'termimal' ),
			'all_items'             => __( 'All Products', 'termimal' ),
			'search_items'          => __( 'Search Products', 'termimal' ),
			'parent_item_colon'     => __( 'Parent Products:', 'termimal' ),
			'not_found'             => __( 'No products found.', 'termimal' ),
			'not_found_in_trash'    => __( 'No products found in Trash.', 'termimal' ),
			'featured_image'        => _x( 'Product Cover Image', 'Overrides the “Featured Image” phrase', 'termimal' ),
			'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase', 'termimal' ),
			'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase', 'termimal' ),
			'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase', 'termimal' ),
			'archives'              => _x( 'Product archives', 'The post type archive label', 'termimal' ),
			'insert_into_item'      => _x( 'Insert into product', 'Overrides the “Insert into post” phrase', 'termimal' ),
			'uploaded_to_this_item' => _x( 'Uploaded to this product', 'Overrides the “Uploaded to this post” phrase', 'termimal' ),
			'filter_items_list'     => _x( 'Filter products list', 'Screen reader text', 'termimal' ),
			'items_list_navigation' => _x( 'Products list navigation', 'Screen reader text', 'termimal' ),
			'items_list'            => _x( 'Products list', 'Screen reader text', 'termimal' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'product', 'with_front' => false ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 20,
			'menu_icon'          => 'dashicons-portfolio',
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields' ),
			'show_in_rest'       => true,
			'rest_base'          => 'termimal-products',
		);

		register_post_type( 'termimal_product', $args );
	}
}
