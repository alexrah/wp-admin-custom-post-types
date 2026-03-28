<?php
/**
 * Created by PhpStorm.
 * User: ale
 * Date: 2019-09-01
 * Time: 15:51
 */

namespace WpAdminCPT;

class RegisterTypes {

	/**
	 * @var string post_type slug
	 * */
	private $screen;

	/**
	 * @param string $screen string $screens screen IDs
	 * @param array{label_singular: string, label_plural: string, menu_icon: string, slug: string, taxonomies: array, show_in_menu: bool|string, supports: array, has_archive: bool|string} $args_post
	 * arguments to pass for registering post_types
	 * * **label_singular** - string used for labels and define capability
	 * * **label_plural** - string used for labels and define capability
	 * * **menu_icon** - string dashicons class
	 * * **slug** - string for custom rewrite rule
	 * * **taxonomies** - array of taxonomy slugs
	 * * **show_in_menu** - bool|string ie. edit.php?post_type=evento
	 * * **supports** - array ie. 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'
	 * * **has_archive** - bool|string - Whether there should be post type archives, or if a string, the archive slug to use. Will generate the proper rewrite rules if $rewrite is enabled. Default false
	 * @param bool $register_tax whether use screens IDs to register taxonomy
	 * @deprecated Use separate taxonomy registration instead
	 * @param array{label_singular: string, label_plural: string, cat_name: string, linked_types: array|string, hierarchical:bool, slug:string} $args_tax_one 
	 * arguments to pass for registering taxonomy
	 * * **label_singular** - string used for labels
	 * * **label_plural** - string used for labels
	 * * **tax_name** - string used to register taxonomy, if not specified use cat-$screens
	 * * **linked_types** - (array|string) object types with which the taxonomy should be associated, Default to $screen
	 * * **hierarchical** - bool Whether the taxonomy is hierarchical. Default true
	 * * **slug** - string Customize the permalink slug. Default to cat-$screen
	 * @param array{label_singular: string, label_plural: string, cat_name: string, linked_types: array|string, hierarchical:bool, slug:string} $args_tax_two
	 * arguments to pass for registering taxonomy
	 * * **label_singular** - string used for labels
	 * * **label_plural** - string used for labels
	 * * **tax_name** - string used to register taxonomy, if not specified use cat-$screens
	 * * **linked_types** - (array|string) object types with which the taxonomy should be associated, Default to $screen
	 * * **hierarchical** - bool Whether the taxonomy is hierarchical. Default true
	 * * **slug** - string Customize the permalink slug. Default to cat-$screen
	 */
	public function __construct( $screen, $args_post = array(), $register_tax = false, $args_tax_one = array(), $args_tax_two = array() ) {

//		if(is_string($screens)) $screens = array($screens);
		$this->screen = $screen;

		$this->register_ptype($args_post);

		if($register_tax){
			trigger_error('The $register_tax parameter is deprecated. Use separate taxonomy registration instead.', E_USER_DEPRECATED);
		}

		if(!empty($args_tax_one)){
            $this->register_tax($args_tax_one);
        }

		if(!empty($args_tax_two)){
            $this->register_tax($args_tax_two);
        }

	}


	/**
	 * @param array $args
	 * * **menu_icon** - string dashicons class
	 * * **slug** - string for custom rewrite rule
	 * * **taxonomies** - array of taxonomy IDs
	 * * **label_singular** - string
	 * * **label_plural** - string
	*/
	private function register_ptype($args){

//		foreach ( $this->screen as $this->screen ) {

			add_action( 'init', function () use ($args) {

				$label_singular = (isset($args['label_singular']))?$args['label_singular']:ucfirst($this->screen);
				$label_plural = (isset($args['label_plural']))?$args['label_plural']:substr($label_singular,0,-1).'i';
				$show_in_menu = (isset($args['show_in_menu']))?$args['show_in_menu']:true;
				$aSupports = $args['supports'] ?? [ 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ];
				$bHasArchive = $args['has_archive'] ?? false;

				register_post_type( $this->screen,
					array(
						'public'          => true,
						'has_archive'     => $bHasArchive,
						'show_in_menu'    => $show_in_menu,
						'supports'        => $aSupports,
						'capability_type' => array(strtolower(str_replace(' ','-',$label_singular)),
													strtolower(str_replace(' ','-',$label_plural))),
						'map_meta_cap'    => true,
						'menu_position'   => 12, // places menu item directly below Posts
						'menu_icon'       => (isset($args['menu_icon']))?$args['menu_icon']: 'dashicons-welcome-write-blog',
						'rewrite'         => array( 'slug' => (isset($args['slug']))?$args['slug']:$this->screen ),
						'taxonomies'      => (isset($args['taxonomies']))?$args['taxonomies']:array( 'post_tag', 'category' ),
						'show_in_rest' => true,
						'labels'          => array(
							'name'               => __( $label_plural ),
							'singular_name'      => __( $label_singular ),
							'add_new'            => __( 'Aggiungi Nuovo' ),
							'add_new_item'       => __( 'Aggiungi Nuovo '.$label_singular ),
							'edit'               => __( 'Modifica' ),
							'edit_item'          => __( 'Modifica '.$label_singular ),
							'new_item'           => __( 'Nuovo '.$label_singular ),
							'view'               => __( 'Guarda '.$label_plural ),
							'view_item'          => __( 'Guarda '.$label_singular ),
							'search_items'       => __( 'Cerca '.$label_plural ),
							'not_found'          => __( 'Nessuno '. $label_singular .' Trovato' ),
							'not_found_in_trash' => __( 'Nessuno '. $label_singular .' nel cestino' ),
							'parent'             => __( 'Genitore '.$label_singular ),
						),
					)
				);

			}, 10 );
//		}

	}

	/**
	 * @param array $args
	 * * **hierarchical** - bool
	 * * **slug** - string for custom rewrite rule
	 * * **linked_types** - array of post_type to link
	 * * **label_singular** - string
	 * * **label_plural** - string
	 */
	private function register_tax($args){

		/*TODO: tax capabilities?? */
//		foreach ( $this->screen as $screen ) {

			add_action( 'init', function () use ($args) {

				$label_singular = (isset($args['label_singular']))?$args['label_singular']:ucfirst($this->screen);
				$label_plural = (isset($args['label_plural']))?$args['label_plural']:substr($label_singular,0,-1).'i';
				$tax_slug = (isset($args['slug']))?$args['slug']:'cat_'.$this->screen;
				$linked_types = (isset($args['linked_types']))?$args['linked_types']:$this->screen;
				$tax_name = (isset($args['tax_name']))?$args['tax_name']:$tax_slug;

				$options = array(
					'hierarchical'      => (isset($args['hierarchical']))?$args['hierarchical']:true,
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array( 'slug' => $tax_slug ),
					'show_in_rest'      => true,
					'labels'            => array(
						'name'              => __( 'Cat. '.$label_plural ),
						'singular_name'     => __( 'Cat. '.$label_singular ),
						'search_items'      => __( 'Cerca '.$label_plural ),
						'all_items'         => __( 'Tutti '.$label_plural ),
						'parent_item'       => __( 'Genitore '.$label_singular ),
						'parent_item_colon' => __( 'Genitore '.$label_singular.':' ),
						'edit_item'         => __( 'Modifica '.$label_singular ),
						'update_item'       => __( 'Aggiorna '.$label_singular ),
						'add_new_item'      => __( 'Aggiungi Nuovo '.$label_singular ),
						'new_item_name'     => __( 'Nuovo Nome '.$label_singular ),
						'menu_name'         => __( 'Cat. ' . $label_plural ),
					)
				);

				register_taxonomy( $tax_name, $linked_types, $options );


			},1);
//		}
	}

}

