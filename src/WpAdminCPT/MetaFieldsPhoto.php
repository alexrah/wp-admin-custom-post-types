<?php
namespace WpAdminCPT;

use WP_Post;
use WpAdminCPT\MetaFieldsHelper as MH;

class MetaFieldsPhoto {


	/**
	 * array of post_meta linked to the post
	 * @var array
	 */
	private $aMetaFields;

	/**
	 * Nonce to check for the current form
	 * @var string
	*/
	private $sNonceBase;

	/**
	 * Nonce action to give context to Nonce
	 * @var string
	 */
	private $sNonceAction = 'vn_meta_box-photo';

	/**
	 * array of screens to register for the current meta-box
	 * @var array
	*/
	private $aScreens;


	/**
	 * @param array $aMetaFields
	 * @param string $sNonceBase
	 * @param array $aScreens
	 */
	public function __construct($aMetaFields,$sNonceBase,$aScreens) {

		$this->aMetaFields = $aMetaFields;

//        echo 'XXX';
//        print_r($this->aMetaFields);

		$this->sNonceBase = $sNonceBase;
		$this->aScreens = $aScreens;

	}

	/**
	 * Init save_post & add_meta_boxes hooks
	 */
	public function init(){
		add_action( 'save_post', [$this,'saveData'] );
		add_action('add_meta_boxes',[$this,'addMetaBox']);
    }

	/**
	 * Add meta box to screens
	 * */
	public function addMetaBox() {

		foreach ( $this->aScreens as $screen ) {

            /**
             * @var array{Name:string,Label:string} $aMetaField
            */
            foreach (  $this->aMetaFields as $aMetaField ){

	            $id = $screen.'-metabox-photo-'.$this->getNonce($aMetaField['Name']);

	            $sBoxTitle = (empty($aMetaField['Label'])) ? 'Foto '.$aMetaField['Name'] : $aMetaField['Label'];

	            add_meta_box(
		            $id,
		            $sBoxTitle,
		            [$this,'renderPhoto'],
		            $screen,
                    'side',
                    'default',
		            $aMetaField
	            );

            }

		}
	}

    private function getNonce($sFieldName = ''){
//        return $this->sNonceBase.'-'.$sFieldName;
        return $this->sNonceBase;
    }

	/**
	 * @param WP_Post $oPost
	 * @param array{Name:string,Label:string} $aMetaField
	 */
	public function renderPhoto($oPost,$aArgs){

//        echo 'YYY';
//        print_r($aArgs);
		$aMetaField = $aArgs['args'];

		// Add a nonce field so we can check for it later.
		wp_nonce_field( $this->sNonceAction, $this->getNonce( $aMetaField['Name'] ) );

		$mMetaValue = MH::getMetaData($aMetaField['Name'],$oPost);
		$sImageURL = wp_get_attachment_image_url( $mMetaValue, 'medium' )
        ?>

        <div id="container-photo-upload-<?php echo $aMetaField['Name']; ?>"></div>
        <script type="module"
                src="<?php echo get_stylesheet_directory_uri() . '/vendor/alexrah/wp-admin-custom-post-types/src/assets/js/photoManager.jsx' ?>"
                data-value='<?php echo $sImageURL??''; ?>'
                data-name="<?php echo $aMetaField['Name']?>"
                data-label="<?php echo $aMetaField['Label']?>"
            >
        </script>
        <?php

	}

	/**
	 * @param int $iPostId
	 */
	public function saveData($iPostId){

		// Check if our nonce is set.
		if ( ! isset( $_POST[$this->sNonce] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST[$this->getNonce()], $this->sNonceAction ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $iPostId ) ) {
				return;
			}

		} else {

			if ( ! current_user_can( 'edit_post', $iPostId ) ) {
				return;
			}
		}

		foreach($this->aMetaFields as $aMetaField)
		{
			if ( empty($_POST[$aMetaField["Name"]]) )
			{
				//campo non impostato dovrei rimuovere o annullare o ?
				delete_post_meta( $iPostId, $aMetaField["Name"] );
			}
			else
			{
                // Sanitize text field
                $currentData = (is_array($_POST[$aMetaField["Name"]]))?$_POST[$aMetaField["Name"]]:sanitize_text_field($_POST[$aMetaField["Name"]]);
				// Update the meta field in the database.
				update_post_meta( $iPostId, $aMetaField["Name"], $currentData );


			}
		}

	}


}