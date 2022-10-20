<?php
namespace WpAdminCPT;

class MetaFieldsHelper{

	/**
	 * @param $sMetaFieldName
	 * @param null|WP_Post $oPost
	 *
	 * @return string
	 */
	public static function getMetaData($sMetaFieldName,$oPost = null){
		$sMetaValue = '';
		$oPost = get_post($oPost);
		if($oPost){
			$sMetaValue = get_post_meta( $oPost->ID, $sMetaFieldName, true );

			if(!$sMetaValue) {
				$sMetaValue = '';
			}
		}

		return maybe_unserialize($sMetaValue);
	}


	public static function getChoicesClassName(){

		return (is_admin())?'choices_admin':'choices_frontend';

	}



	public static function getValidation($sValidation){
		if(empty($sValidation)){
			return '';
		}

		switch ($sValidation){
			default:
				return $sValidation;
		}

	}


	/**
	 * @return array
	 */
	public static function getLocationIds(){
		$aLocations  =  get_posts( 'post_type=location&posts_per_page=-1' );
		return wp_list_pluck($aLocations,'ID');
	}

	/**
	 * @return array
	 */
	public static function getOrganizationIds(){
		$aOrganizations  =  get_posts( 'post_type=organization&posts_per_page=-1' );
		return wp_list_pluck($aOrganizations,'ID');
	}

}