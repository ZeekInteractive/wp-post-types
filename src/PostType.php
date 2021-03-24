<?php

namespace Zeek\WpPostTypes;

use Zeek\Modernity\Support\Str;
use Zeek\Modernity\Traits\Singleton;

abstract class PostType {
	use Singleton;

	protected string $slug;
	protected string $singular;

	protected bool $gutenberg = false;

	protected bool $public = true;
	protected bool $publicly_queryable = true;
	protected bool $exclude_from_search = false;
	protected bool $show_ui = true;
	protected bool $show_in_nav_menus = true;
	protected bool $query_var = true;
	protected bool $hierarchical = true;
	protected $has_archive = true;
	protected ?int $menu_position = 27;
	protected bool $show_in_rest = true;
	protected string $menu_icon = 'dashicons-pin';
	protected array $supports = [
		'title',
		'editor',
	];

	protected function __construct() {
		add_action( 'init', [ $this, 'init' ], 5 );
		$this->handleGutenberg();
	}

	public function init() {
		register_post_type( $this->slug, [
			'labels'              => $this->postTypeLabels( $this->singular ),
			'public'              => $this->public,
			'publicly_queryable'  => $this->publicly_queryable,
			'exclude_from_search' => $this->exclude_from_search,
			'show_ui'             => $this->show_ui,
			'show_in_nav_menus'   => $this->show_in_nav_menus,
			'query_var'           => $this->query_var,
			'hierarchical'        => $this->hierarchical,
			'has_archive'         => $this->has_archive,
			'menu_position'       => $this->menu_position,
			'show_in_rest'        => $this->show_in_rest,
			'menu_icon'           => $this->menu_icon,
			'supports'            => $this->supports,
		] );
	}

	protected function postTypeLabels( string $singular ) : array {
		$plural = Str::pluralize( $singular );

		return [
			'name'               => $plural,
			'singular_name'      => $singular,
			'add_new_item'       => 'Add New ' . $singular,
			'edit_item'          => 'Edit ' . $singular,
			'new_item'           => 'New ' . $singular,
			'view_item'          => 'View ' . $singular,
			'search_items'       => 'Search ' . $plural,
			'not_found'          => 'No ' . $plural . ' found',
			'not_found_in_trash' => 'No ' . $plural . ' found in Trash',
			'parent_item_colon'  => '',
			'all_items'          => $plural,
			'menu_name'          => $plural,
		];
	}

	private function handleGutenberg() {
		if ( ! $this->gutenberg ) {
			add_filter( 'use_block_editor_for_post', function ( bool $enabled, \WP_Post $postType ) {
				if ( $postType->post_type !== $this->slug ) {
					return $enabled;
				}

				return false;
			}, 10, 2 );
		}
	}
}
