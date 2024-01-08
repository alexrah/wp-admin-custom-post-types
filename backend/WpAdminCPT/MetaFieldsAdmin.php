<?php
namespace WpAdminCPT;

use WP_Post;
use WpAdminCPT\MetaFieldsRender as MR;
use WpAdminCPT\MetaFieldsHelper as MH;

class MetaFieldsAdmin {


	/**
	 * array of post_meta linked to the post
	 * @var array
	 */
	private $aMetaFields;

	/**
	 * Nonce to check for the current form
	 * @var string
	*/
	private $sNonce;

	/**
	 * Nonce action to give context to Nonce
	 * @var string
	 */
	private $sNonceAction = 'vn_meta_box';

	/**
	 * array of screens to register for the current meta-box
	 * @var array
	*/
	private $aScreens;

	private $sBoxTitle;

	private $sScriptValidation;

	/**
	 * @param array $aMetaFields
	 * @param string $sNonce
	 * @param array $aScreens
	 */
	public function __construct($aMetaFields,$sNonce,$aScreens,$sBoxTitle = '') {

		$this->aMetaFields = $aMetaFields;
		$this->sNonce = $sNonce;
		$this->aScreens = $aScreens;
		$this->sBoxTitle = $sBoxTitle;
		$this->sScriptValidation = '';

	}

	/**
	 * Init save_post & add_meta_boxes hooks
	 */
	public function init(){
		add_action( 'save_post', [$this,'saveData'] );
		add_action('add_meta_boxes',[$this,'addMetaBox']);
        add_action('admin_enqueue_scripts',function(){
	        wp_enqueue_style( 'wpadmincpt-css', get_stylesheet_directory_uri() . '/vendor/alexrah/wp-admin-custom-post-types/backend/assets/css/admin.css', [], '1.0' );
        });
		add_action( 'rest_api_init', [$this,'registerRestFields'] );
	}

	/**
	 * Add meta box to screens
	 * */
	public function addMetaBox() {



		foreach ( $this->aScreens as $screen ) {

			$id = $screen.'-metabox-'.$this->sNonce;
			$sBoxTitle = (empty($this->sBoxTitle))?'Dettagli '.$screen:$this->sBoxTitle;

			add_meta_box(
				$id,
				$sBoxTitle,
				[$this,'renderFields'],
				$screen
			);

		}
	}

	/**
	 * @param WP_Post $oPost
	 */
	public function renderFields($oPost){
		// Add a nonce field so we can check for it later.
		wp_nonce_field( $this->sNonceAction, $this->sNonce );
		?>

		<ul class="wrap-cf">
		<?php
		foreach ($this->aMetaFields as $aMetaField){

            echo MR::formField($aMetaField,$oPost);

		}
		?>
		</ul>
		<?php
        echo $this->getCustomValidation();
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
		if ( ! wp_verify_nonce( $_POST[$this->sNonce], $this->sNonceAction ) ) {
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
				if( $aMetaField["Type"] == "date" || $aMetaField["Type"] == "datetime-local" ) {

					$currentData = MH::convertDateTimeZone($_POST[ $aMetaField["Name"] ],'timestamp');

				} else {
					// Sanitize user input.

					$currentData = (is_array($_POST[$aMetaField["Name"]]))?$_POST[$aMetaField["Name"]]:sanitize_text_field($_POST[$aMetaField["Name"]]);

				}

				// Update the meta field in the database.
				update_post_meta( $iPostId, $aMetaField["Name"], $currentData );

				if ( $aMetaField["Type"] == "address" ) {

					update_post_meta( $iPostId, 'location-latitudine', $_POST['location-latitudine'] );
					update_post_meta( $iPostId, 'location-longitudine', $_POST['location-longitudine'] );

				}

			}
		}

	}

	/**
	 * @param string $sType title, content, tag, cat
	 * @param string $sTaxonomy optional taxonomy slug to use for some validation types
	 *
	 * @return void
	 */
	public function setCustomValidation($sType,$sTaxonomy = ''){

		ob_start();

		switch ($sType){
            case 'title':
                ?>
                <script>
                    window.addEventListener('DOMContentLoaded',()=>{
                        document.getElementById('title').setAttribute('required','');
                    });
                </script>
                <?php
                break;
			case 'excerpt':
				?>
                <script>
                    window.addEventListener('DOMContentLoaded',()=>{
                        document.getElementById('excerpt').setAttribute('required','');
                    });
                </script>
				<?php
				break;
            case 'content':
                ?>
                <script>

                    const editorContainer = document.getElementById("wp-content-editor-container");
                    const editorLabel = document.createElement('h2');
                    editorLabel.classList.add('validation-label','content-validation-label');
                    editorLabel.innerText = 'INSERIRE UNA TESTO';
                    editorContainer.insertBefore(editorLabel,editorContainer.firstChild);

                    document.forms.post.addEventListener('submit',(e) => {

                        if( tinyMCE.get('content').getContent() === '' ) {

                            e.preventDefault();
                            editorLabel.style.display = 'block';

                        } else {
                            editorLabel.style.display = 'none';
                        }
                    });

                </script>
                <?php
                break;
            case 'tag':
                if(empty($sTaxonomy)) return;
                ?>
                <script>
                    window.addEventListener('DOMContentLoaded',()=>{

                        const eTagBox = document.querySelector('#tagsdiv-<?php echo $sTaxonomy?>')

                        const eInputPostTag = eTagBox.querySelector('.newtag');
                        const eListTags  = eTagBox.querySelector('.tagchecklist');

                        eTagBox.querySelector('.button.tagadd').addEventListener('click',(e)=>{
                            validate_<?php echo $sTaxonomy?>(e,eInputPostTag)
                        } );
                        document.getElementById('post').addEventListener('submit',(e)=>{
                            validate_<?php echo $sTaxonomy?>(e,eInputPostTag,eListTags)
                        } );

                    });



                    function validate_<?php echo $sTaxonomy?>(e,eInputPostTag,eListTags){

                        if(eListTags.children.length === 0){
                            console.log('validate_post_tag_citta not valid');
                            e.preventDefault();
                            document.getElementById('publish').classList.remove('disabled');
                            document.querySelector('#publishing-action .spinner').classList.remove('is-active');

                            eInputPostTag.setCustomValidity('Inserire una citta');
                        } else {
                            console.log('validate_post_tag_citta valid');
                            eInputPostTag.setCustomValidity('');
                        }

                        eInputPostTag.reportValidity();

                    }
                </script>
                <?php
                break;
            case 'cat':
	            if(empty($sTaxonomy)) return;
                ?>
                <script>
                    window.addEventListener('DOMContentLoaded',()=>{

                        const eTipologiaLocationBox = document.querySelector('#taxonomy-<?php echo $sTaxonomy?>')

                        const eValidationLabel = document.createElement('h4');
                        eValidationLabel.classList.add('validation-label','taxonomy-validation-label');
                        eValidationLabel.textContent = 'SI PREGA DI SPUNTARE ALMENO UN TERMINE';

                        eTipologiaLocationBox.insertBefore(eValidationLabel,eTipologiaLocationBox.children[0]);

                        refreshTerms(eTipologiaLocationBox,eValidationLabel);

                        document.getElementById('<?php echo $sTaxonomy; ?>-add-submit').addEventListener('click',(e)=>{

                            const observeNewTerm = new MutationObserver((mutationsList, observeNewTerm) => {
                                // Use traditional 'for loops' for IE 11
                                for(const mutation of mutationsList) {

                                    console.log('mutation',mutation);

                                    if (mutation.type === 'childList') {

                                        console.log('A child node has been added or removed.');

                                        refreshTerms(eTipologiaLocationBox,eValidationLabel);

                                        observeNewTerm.disconnect();

                                    }
                                }
                            });

                            observeNewTerm.observe(document.getElementById('<?php echo $sTaxonomy?>checklist'),{ childList: true })


                        })

                    });

                    function validate_<?php echo $sTaxonomy?>(e,lTipologiaLocationList,eValidationLabel){
                        const aTipologiaLocationList = Array.from(lTipologiaLocationList);
                        let sValidity;
                        sValidity = aTipologiaLocationList.some(elem => {
                            console.log('elem.checked',elem.checked);
                            return elem.checked === true;
                        });

                        console.log('aTipologiaLocationList',aTipologiaLocationList);
                        console.log('sValidity',sValidity);

                        if(sValidity){
                            console.log('validate_tipologia valid');
                            console.log('event',e);
                            eValidationLabel.style.display = 'none';

                            if(e.type === 'submit'){
                                document.getElementById('post').submit();
                            }

                        } else {
                            console.log('validate_tipologia not valid');
                            console.log('event type',e);
                            if(e.type === 'submit'){
                                e.preventDefault();
                            }
                            document.getElementById('publish').classList.remove('disabled');
                            document.querySelector('#publishing-action .spinner').classList.remove('is-active');
                            eValidationLabel.style.display = 'block';
                            eValidationLabel.scrollIntoView({behavior: "smooth", block: "center"});
                        }
                    }

                    function refreshTerms(eContainer,eValidationLabel){
                        const lTipologiaLocationList = eContainer.querySelectorAll('#<?php echo $sTaxonomy?>checklist li input')

                        const clickCallback = (e) => {
                            validate_<?php echo $sTaxonomy?>(e,lTipologiaLocationList,eValidationLabel)
                        }

                        lTipologiaLocationList.forEach(elem => {
                            elem.removeEventListener('click', clickCallback);
                            elem.addEventListener('click', clickCallback);
                        })

                        document.getElementById('post').removeEventListener('submit', clickCallback);
                        document.getElementById('post').addEventListener('submit', clickCallback);

                        return lTipologiaLocationList;
                    }

                </script>
	            <?php
                break;
        }

		$this->sScriptValidation .= ob_get_contents();
		ob_end_clean();

    }

	/**
	 * @return string
	 */
	private function getCustomValidation(){
	    return $this->sScriptValidation;
    }

	public function registerRestFields(){

		foreach ($this->aScreens as $sScreen){

			register_rest_field( $sScreen, 'postmeta', array(
				'get_callback' => function ( $object ) {

					$aPostMetas = [];
					$aPostMetasRaw = get_post_meta( $object['id'] );
					foreach ($aPostMetasRaw as $sPostMetaKey => $aPostMetaValue){
						if(str_starts_with($sPostMetaKey, '_')) continue;
						$aPostMetas[$sPostMetaKey] = $aPostMetaValue[0];
					}

					return $aPostMetas;

				}, ));

		}

	}

}