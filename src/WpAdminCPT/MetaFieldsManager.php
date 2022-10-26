<?php
namespace WpAdminCPT;

use WP_Post;

class MetaFieldsManager{

	private $aMetaFields;

	private $sPrefix;

	/**
	 * @param string $sPrefix a global prefix to prepend to all meta Name
	 * @param array{Name:string,Label:string,LabelPublic:string,Placeholder:string,Type:string,Validation:string,Group:string} $aArgsArr[]
	 */
	public function __construct($sPrefix,$aArgsArr) {

		$this->sPrefix = $sPrefix;

		foreach ($aArgsArr as $aArgs){
			$this->setField($aArgs);
		}
	}

	/**
	 * Sets a field
	 *
	 * @param array{Name:string,Label:string,LabelPublic:string,Placeholder:string,Type:string,Validation:string,Group:string} $aArgs
	 * @return bool true if field successfully pushed to aMetaFields, false otherwise
	 *
	 */
	private function setField($aArgs){

		if( empty($aArgs['Name']) || empty($aArgs['Label']) ) return false;

		// add a global prefix to all field names for uniformity
		if ( substr($aArgs['Name'],0,count($this->sPrefix) ) !== $this->sPrefix ){
			$aArgs['Name'] = $this->sPrefix . '-' . $aArgs['Name'];
		}

		$aArgsDefaults = [
			"Name"        => "",
			"Label"       => "",
			"LabelPublic" => "",
			"Placeholder" => "",
			"Type"        => "text",
			"Validation"  => '',
			"Value"       => [],
			'Group'       => 'global'
		];

		$aArgsComputed = array_merge($aArgsDefaults,$aArgs);
		$iMetaFieldsCount = count($this->aMetaFields);
		$this->aMetaFields[] = $aArgsComputed;
		return ( $iMetaFieldsCount < count($this->aMetaFields));

	}

	/**
	 *
	 * @param string $sGroup
	 * @param false $bPublic
	 *
	 * @return array Returns an array of fields
	 */
	public function getFields($sGroup = 'all',$bPublic = false){

		if($sGroup == 'all'){
			return $this->aMetaFields;
		}

		$aMetaFields = [];
		foreach ($this->aMetaFields as $aMetaField){

			if($aMetaField['Group'] == 'global' || $aMetaField['Group'] == $sGroup){

				if( $bPublic ){
					if(!empty($aMetaField['LabelPublic'])){
						array_push($aMetaFields,$aMetaField);
					}

				} else {
					array_push($aMetaFields,$aMetaField);
				}


			}
		}

		return  $aMetaFields;

	}


	/**
	 * @return array list of Groups names registered
	 */
	public function getGroups(){
		$aGroups = array_column($this->aMetaFields,'Group');
		return array_unique($aGroups);
	}

	/**
	 * @param string $sGroup
	 * @param int|WP_Post $mPost
	 *
	 * @return array key = $aMetaField['Name'] => value = get_post_meta()
	 */
	public function getFieldsData( $sGroup = 'all', $mPost = 0 ){

		$oPost = get_post($mPost);

		$aMetaFields = $this->getFields($sGroup);
		$aMetaFieldsData = [];
		foreach ($aMetaFields as $aMetaField){

			$aMetaFieldsData[$aMetaField['Name']] = get_post_meta($oPost->ID,$aMetaField['Name'],true);

		}

		return $aMetaFieldsData;

	}

}