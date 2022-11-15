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
	 * @param array $args_post arguments to pass for registering post_types
	 * <p><b>label_singular</b> string used for labels and define capability </p>
	 * <p><b>label_plural</b> string used for labels and define capability</p>
	 * <p><b>menu_icon</b> string dashicons class</p>
	 * <p><b>slug</b> string for custom rewrite rule</p>
	 * <p><b>taxonomies</b> array of taxonomy slugs</p>
	 * <p><b>show_in_menu</b> bool|string ie. edit.php?post_type=evento</p>
	 * <p><b>supports</b> array ie. 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'</p>
	 * @param bool $register_tax whether use screens IDs to register taxonomy
	 * @param array $args_tax arguments to pass for registering taxonomy
	 * <p><b>label_singular</b> string used for labels</p>
	 * <p><b>label_plural</b> string used for labels</p>
	 * <p><b>cat_name</b> string used to register taxonomy, if not specified use cat-$screens</p>
	 * <p><b>linked_types</b> (array|string) object types with which the taxonomy should be associated, Default to $screen</p>
	 * <p><b>hierarchical</b> bool Whether the taxonomy is hierarchical. Default true</p>
	 * <p><b>slug</b> string Customize the permalink slug. Default to cat-$screen </p>
	 *
	 */
	public function __construct( $screen, $args_post = array(), $register_tax = false, $args_tax = array() ) {

//		if(is_string($screens)) $screens = array($screens);
		$this->screen = $screen;

		$this->register_ptype($args_post);

		if($register_tax){
			$this->register_tax($args_tax);
		}

	}


	/**
	 * @param array $args
	 * <p><b>menu_icon</b> string dashicons class</p>
	 * <p><b>slug</b> string for custom rewrite rule</p>
	 * <p><b>taxonomies</b> array of taxonomy IDs</p>
	 * <p><b>label_singular</b> string </p>
	 * <p><b>label_plural</b> string </p>
	*/
	private function register_ptype($args){

//		foreach ( $this->screen as $this->screen ) {

			add_action( 'init', function () use ($args) {

				$label_singular = (isset($args['label_singular']))?$args['label_singular']:ucfirst($this->screen);
				$label_plural = (isset($args['label_plural']))?$args['label_plural']:substr($label_singular,0,-1).'i';
				$show_in_menu = (isset($args['show_in_menu']))?$args['show_in_menu']:true;
				$aSupports = $args['supports'] ?? [ 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ];

				register_post_type( $this->screen,
					array(
						'public'          => true,
						//'has_archive' => true,
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

			}, 1 );
//		}

	}

	/**
	 * @param array $args
	 * <p><b>hierarchical</b> </p>
	 * <p><b>slug</b> string for custom rewrite rule</p>
	 * <p><b>linked_types</b> array of post_type to link</p>
	 * <p><b>label_singular</b> string </p>
	 * <p><b>label_plural</b> string </p>
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

