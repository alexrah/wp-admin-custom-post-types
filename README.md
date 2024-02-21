## About
Helper class to register custom post_types and relative post_metas to Wordpress

## Install using composer:
```bash
composer require alexrah/wp-admin-custom-post-types
```

published on packagist at: [https://packagist.org/packages/alexrah/wp-admin-custom-post-types](https://packagist.org/packages/alexrah/wp-admin-custom-post-types)

## Usage
```php
use WpAdminCPT\RegisterTypes;
use WpAdminCPT\MetaFieldsManager;
use WpAdminCPT\MetaFieldsAdmin;

const META_BOX_NONCE = 'meta_box_nonce';

new RegisterTypes('user-paid-content',
	[
		'label_singular' => "My Post",
		'label_plural' => "My Posts"
	],
	true,
	[
		'label_singular' => "Categoria Contenuto",
		'label_plural' => "Categorie Contenuto"
	]
);

function vn_fields(): MetaFieldsManager {

	$sPrefix = 'user-paid-';

	$aFields = [
		[
			"Name"        => "type",
			"Label"       => "Tipo Contenuto",
			"LabelPublic" => "",
			"Placeholder" => "Seleziona Tipo",
			"Type"        => "select",
			"Validation"  => '',
			"Value"       => ['job-listing','event'],
			'Group'       => 'global'
		]
	];

	return new MetaFieldsManager($sPrefix,$aFields);

}

$oUserPaidContent = new MetaFieldsAdmin(vn_fields()->getFields(),META_BOX_NONCE,['user-paid-content'],'Dati');
$oUserPaidContent->init();



```

### RegisterTypes options

* ``string $screen`` post_type screen IDs
* ``array $args_post`` arguments to pass for registering post_types
	 * ``label_singular`` string used for labels and define capability
	 * ``label_plural`` string used for labels and define capability
	 * ``menu_icon`` string dashicons class
	 * ``slug`` string for custom rewrite rule
	 * ``taxonomies`` array of taxonomy slugs
	 * ``show_in_menu`` bool|string ie. edit.php?post_type=evento
	 * ``supports`` array ie. 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'
	 * ``has_archive`` bool|string - Whether there should be post type archives, or if a string, the archive slug to use. Will generate the proper rewrite rules if $rewrite is enabled. Default false
	 * ``bool $register_tax`` whether use screens IDs to register taxonomy
	 * ``array $args_tax`` arguments to pass for registering taxonomy
	 * ``label_singular`` string used for labels
	 * ``label_plural`` string used for labels
	 * ``cat_name`` string used to register taxonomy, if not specified use cat-$screens
	 * ``linked_types`` (array|string) object types with which the taxonomy should be associated, Default to $screen
	 * ``hierarchical`` bool Whether the taxonomy is hierarchical. Default true
	 * ``slug`` string Customize the permalink slug. Default to cat-$screen 
* ``bool $register_tax`` whether use screens IDs to register taxonomy
* ``array $args_tax`` arguments to pass for registering taxonomy
	 * ``label_singular`` string used for labels
	 * ``label_plural`` string used for labels
	 * ``cat_name`` string used to register taxonomy, if not specified use cat-$screens
	 * ``linked_types`` (array|string) object types with which the taxonomy should be associated, Default to $screen
	 * ``hierarchical`` bool Whether the taxonomy is hierarchical. Default true
	 * ``slug`` string Customize the permalink slug. Default to cat-$screen 


### MetaFieldsAdmin options
* ``array $aMetaFields`` array of meta_fields as returned by ``MetaFieldsManager::getFields()``
* ``string $sNonce`` a unique string used to validate requests
* ``array $aScreens`` an array of post_type IDs to register with 
* ``string $sBoxTitle`` a label for the meta_box
   
#### NB: go to /wp-admin/users.php?page=users-user-role-editor.php to update permissions



## Changelog
#### version 1.0.0
* helper classes to register post_types
* supports creating custom taxonomies
* support creating post_metas

#### version 2.0.0
change root dir for PHP classes from src to backend

#### version 2.1.0
add support to Rest API 
