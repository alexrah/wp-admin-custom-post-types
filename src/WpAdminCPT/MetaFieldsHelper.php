<?php
namespace WpAdminCPT;

use DateTimeZone;
use DateTime;
use WP_Post;
use Exception;

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


	/**
	 * @param $sValidation string
	 *
	 * @return string
	 */
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


	/**
	 * @param string|int $mDateTime can be a string in 2022-02-01 format or an int timestamp
	 * @param string $sOutFormat either date as 2022-02-01 or timestamp or text as localized string 13 Febbraio 2022
	 * @return string see $sOutFormat
	 */
	public static function convertDateTimeZone($mDateTime, $sOutFormat = 'date'){

		try {

			$oDateTime = new DateTime();
			$oDateTime->setTimezone(new DateTimeZone('Europe/Rome'));

			if(is_numeric($mDateTime)){

				$oDateTime->setTimestamp($mDateTime);

			} elseif(!empty($mDateTime)) {

				$aDateTime = explode('-',$mDateTime);
				$oDateTime->setDate($aDateTime[0],$aDateTime[1],$aDateTime[2]);
				$oDateTime->setTime(0,0);

			} else {

				return '';

			}

			switch ($sOutFormat){
				case 'timestamp':
					return $oDateTime->format('U');
				case 'text':
					return mysql2date("d F Y", $oDateTime->format('Y-m-d'));
				case 'date':
				default:
					return $oDateTime->format('Y-m-d');
			}

		}  catch (Exception $e){
//	        print_r($e->getMessage());
			return '';
		}

	}

}