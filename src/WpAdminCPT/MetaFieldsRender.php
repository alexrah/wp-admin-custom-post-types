<?php
namespace WpAdminCPT;

use WP_Post;
use WpAdminCPT\MetaFieldsHelper as MH;

class MetaFieldsRender{


	/**
	 * @param array $aMetaField
	 * @param string|array $mMetaValue
	 * @param string $sValidation
	 * @param false $bMulti
	 *
	 * @return string HTML output
	 */
	private static function selectLocation($aMetaField,$mMetaValue,$sValidation,$bMulti = false){

		$aMetaValue = (is_array($mMetaValue))?$mMetaValue:[$mMetaValue];
		$sFieldName = ($bMulti)?$aMetaField['Name'].'[]':$aMetaField['Name'];

		ob_start();
		?>
		<select class="form-control" style="display: none;" data-validation="<?php echo $sValidation; ?>" <?php echo $sValidation; ?> <?php echo $bMulti?'multiple':''; ?> id="<?php echo $aMetaField['Name'] ?>" name="<?php echo $sFieldName; ?>">

		<option value=""><?php echo $aMetaField['Placeholder']; ?></option>
		<option value="0">Non in elenco</option>
		<?php
		foreach($aMetaField["Value"] as $sMetaFieldValue){

			$terms  = get_the_terms((int)$sMetaFieldValue, 'post_tag_citta');
			$sCity = '';
			if ( $terms && ! is_wp_error( $terms ) ) {

				$sCity = $terms[0]->name;

			}

			$sSelected  = "";
			if( in_array($sMetaFieldValue,$aMetaValue) ) $sSelected = "selected";
		?>
			<option <?php echo $sSelected?> value="<?php echo $sMetaFieldValue; ?>"><?php echo get_the_title( $sMetaFieldValue  ) . " - ". $sCity ?></option>
		<?php } ?>

		</select>
        <?php if(is_admin()) { ?>
            <a class="btn" target="_blank" title="Aggiungi location" href="/wp-admin/post-new.php?post_type=location">Aggiungi location</a>
        <?php } ?>

		<?php
        echo self::scriptChoicesJsInit($aMetaField['Name']);

		$sOutput = ob_get_contents();
		ob_end_clean();
		return $sOutput;
	}


	/**
	 * @param array $aMetaField
	 * @param string $sMetaValue
	 * @param string $sValidation
	 *
	 * @return false|string
	 */
	private static function selectAddress($aMetaField,$sMetaValue,$sValidation){

		ob_start();
		?>
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places&key=AIzaSyDiwYVGJb4fDMlWfqXEUtsIKvTwxGAve2o"></script>
        <script>
            window.onload = function () {
                initialize();
            }

            function initialize() {
                const geocoder = new google.maps.Geocoder();
                var mapOptions = {
                    center: new google.maps.LatLng(45.817631, 8.826380),
                    zoom: 13
                };


                var map = new google.maps.Map(document.getElementById('map-canvas'),mapOptions);

                // var geocoder, map;


                geocoder.geocode( { 'address': '<?php echo  esc_attr($sMetaValue)  ?  esc_attr($sMetaValue)  : 'Varese'; ?>'}, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        map.setCenter(results[0].geometry.location);
                        var marker = new google.maps.Marker({
                            map: map,
                            position: results[0].geometry.location
                        });
                    } else {
                        alert('Geocode was not successful for the following reason: ' + status);
                    }
                });


                var input = /** @type {HTMLInputElement} */document.getElementById('<?php echo $aMetaField['Name'] ?>');

                var types = document.getElementById('type-selector');
                map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
                map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

                var autocomplete = new google.maps.places.Autocomplete(input);
                autocomplete.bindTo('bounds', map);

                var infowindow = new google.maps.InfoWindow();
                var marker = new google.maps.Marker({
                    map: map,
                    anchorPoint: new google.maps.Point(0, -29)
                });

                google.maps.event.addListener(autocomplete, 'place_changed', function(e) {
                    infowindow.close();
                    marker.setVisible(false);
                    var place = autocomplete.getPlace();
                    document.getElementById('location-latitudine').value = place.geometry.location.lat();
                    document.getElementById('location-longitudine').value = place.geometry.location.lng()
                    if (!place.geometry) {
                        return;
                    }

                    // If the place has a geometry, then present it on a map.
                    if (place.geometry.viewport) {
                        map.fitBounds(place.geometry.viewport);
                    } else {
                        map.setCenter(place.geometry.location);
                        map.setZoom(17);  // Why 17? Because it looks good.
                    }
                    marker.setIcon(/** @type {google.maps.Icon} */({
                        url: place.icon,
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(35, 35)
                    }));
                    marker.setPosition(place.geometry.location);
                    marker.setVisible(true);

                    var address = '';
                    if (place.address_components) {
                        address = [
                            (place.address_components[0] && place.address_components[0].short_name || ''),
                            (place.address_components[1] && place.address_components[1].short_name || ''),
                            (place.address_components[2] && place.address_components[2].short_name || '')
                        ].join(' ');
                    }

                    infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
                    infowindow.open(map, marker);
                });

            }

            google.maps.event.addDomListener(window, 'load', initialize);

        </script>

        <div class="form-field">
            <input type="text" name="<?php echo $aMetaField['Name']?>" id="<?php echo $aMetaField['Name']?>" <?php echo $sValidation; ?>  class="controls" value="<?php echo  esc_attr($sMetaValue)  ?  esc_attr($sMetaValue)  : ''; ?>">

            <input type="hidden" name="location-latitudine" id="location-latitudine" value="<?php echo MH::getMetaData('location-latitudine')?>">
            <input type="hidden" name="location-longitudine" id="location-longitudine" value="<?php echo MH::getMetaData('location-longitudine')?>">

            <div id="map-canvas"></div>
            <style type="text/css">
                #map-canvas { height: 400px; margin: 0; padding: 0; }
            </style>
        </div>

		<?php
		$sOutput = ob_get_contents();
		ob_end_clean();
		return $sOutput;

	}

	/**
	 * @param array $aMetaField
     * @param string $sMetaValue
     * @param string $sValidation
	 * @param bool $bMulti
     * @param bool $bAjax whether to use admin-ajax.php to fetch <option> data;
     *                      $aMetaField['Value'] must contains the action param value to pass to admin-ajax.php request
	 *
	 * @return string
	 */
	private static function select($aMetaField,$sMetaValue,$sValidation, $bMulti = false, $bAjax = false){

		$sFieldName = ($bMulti)?$aMetaField['Name'].'[]':$aMetaField['Name'];
		$sAjaxAction = ($bAjax)?$aMetaField["Value"][0]:'';

		ob_start();

		echo apply_filters('meta_field_render_select_before','',$aMetaField);
		?>

        <select class="form-control" <?php echo $sValidation; ?> <?php echo $bMulti?'multiple':''; ?> id="<?php echo $aMetaField['Name']?>" name="<?php echo $sFieldName?>">

            <option value=""><?php echo $aMetaField['Placeholder']?></option>

            <?php
            if ( !$bAjax ){
                foreach($aMetaField["Value"] as $sMetaFieldValue){

                $sSelected  = ($sMetaValue == $sMetaFieldValue)?'selected':'';

                $sOptionTitle = (is_numeric($sMetaFieldValue))?get_the_title( $sMetaFieldValue  ):$sMetaFieldValue;
                ?>
                    <option <?php echo $sSelected?>  value="<?php echo $sMetaFieldValue; ?>"><?php echo $sOptionTitle; ?></option>';
                <?php
                }
            }
            ?>
        </select>
		<?php
        echo apply_filters('meta_field_render_select_after','',$aMetaField);
		echo self::scriptChoicesJsInit($aMetaField['Name'], $sAjaxAction, $sMetaValue);

		$sOutput = ob_get_contents();
		ob_end_clean();
		return $sOutput;
    }


	/**
     *
     * A generic form field renderer
     *
	 * @param array $aMetaField
	 * @param WP_Post|null $oPost
	 *
	 * @return string
	 */
	public static function formField($aMetaField,$oPost = null){

	    $mMetaValue = MH::getMetaData($aMetaField['Name'],$oPost);
		$sValidation = MH::getValidation($aMetaField['Validation']);
		$sLabel = is_admin()?$aMetaField['Label']:$aMetaField['LabelPublic'];
		ob_start();
		?>
        <div class="form-group">
            <label for="<?php echo $aMetaField['Name']?>"><?php echo $sLabel; ?></label>
            <?php switch ($aMetaField['Type']){
                case 'checkbox':
                    echo self::checkbox($aMetaField,$mMetaValue,$sValidation);
                    break;
                case 'date':
                case 'datetime-local':
                    echo self::date($aMetaField,$mMetaValue,$sValidation);
                    break;
                case 'select-location':
                    echo self::selectLocation($aMetaField,$mMetaValue,$sValidation);
                    break;
	            case 'select-location-multi':
		            echo self::selectLocation($aMetaField,$mMetaValue,$sValidation, true);
		            break;
                case 'select':
                    echo self::select($aMetaField,$mMetaValue,$sValidation);
                    break;
	            case 'select-multi':
		            echo self::select($aMetaField,$mMetaValue,$sValidation,true);
		            break;
                case 'select-ajax':
	                echo self::select($aMetaField,$mMetaValue,$sValidation,false,true);
	                break;
                case 'address':
                    echo self::selectAddress($aMetaField,$mMetaValue,$sValidation);
                    break;
                default:
	                echo self::text($aMetaField,$mMetaValue,$sValidation);
	                break;
            } ?>
        </div>
        <?php
		$sOutput = ob_get_contents();
		ob_end_clean();
		return $sOutput;


    }

	private static function text($aMetaField,$sMetaValue,$sValidation){

		ob_start();
		?>
        <input class="form-control" <?php echo $sValidation; ?> type="<?php echo $aMetaField['Type']?>" id="<?php echo $aMetaField['Name']?>" name="<?php echo $aMetaField['Name']?>" value="<?php echo $sMetaValue?>" placeholder="<?php echo $aMetaField['Placeholder']?>" >
		<?php
		$sOutput = ob_get_contents();
		ob_end_clean();
		return $sOutput;

	}

	private static function date($aMetaField,$sMetaValue,$sValidation){

		ob_start();
		?>
        <input class="form-control" <?php echo $sValidation; ?> type="<?php echo $aMetaField['Type']?>" id="<?php echo $aMetaField['Name']?>" name="<?php echo $aMetaField['Name']?>" value="<?php echo MH::convertDateTimeZone($sMetaValue,$aMetaField['Type']); ?>" >
		<?php
		$sOutput = ob_get_contents();
		ob_end_clean();
		return $sOutput;

	}

	private static function checkbox($aMetaField,$sMetaValue,$sValidation){

		ob_start();
		?>
        <input class="form-control" <?php echo $sValidation; ?> type="<?php echo $aMetaField['Type']?>" id="<?php echo $aMetaField['Name']?>" name="<?php echo $aMetaField['Name']?>" value="1" <?php echo ( $sMetaValue == 1)?'checked':''; ?> >
		<?php
		$sOutput = ob_get_contents();
		ob_end_clean();
		return $sOutput;

	}

	/**
	 * @param string $sMetaFieldName
     * @param string $sAjaxAction if defined, call admin-ajax.php to fetch <option> data
     * @param string $sMetaValue in case of Ajax call, use this arg to set <option> selected
	 *
	 * @return false|string
	 */
	private static function scriptChoicesJsInit($sMetaFieldName,$sAjaxAction = '',$sMetaValue){

		$sUniqueId = substr(md5(mt_rand(0,100)),0,8) ;
		ob_start();
		?>
        <script>

            if(document.getElementById('choice-js')){
                console.log('%c Choice JS defined','font-size:20px; color: green');
                document.getElementById('choice-js').addEventListener('load',initChoiceLocation<?php echo $sUniqueId ?>);
            } else {
                window.addEventListener("DOMContentLoaded",initChoiceLocation<?php echo $sUniqueId ?>)
            }

            function initChoiceLocation<?php echo $sUniqueId ?>(){
                console.log('%c Choice JS loaded','font-size:20px; color: green');
                const oChoiceLocation = new Choices('#<?php echo $sMetaFieldName ?>',{
                    removeItemButton: true,
                    classNames: {
                        containerOuter: 'choices <?php echo MH::getChoicesClassName(); ?> choices_<?php echo $sMetaFieldName ?>',
                    }
                });

                <?php if($sAjaxAction){ ?>

                    const setChoicesLocation = () => {

                        oChoiceLocation.setChoices(async () => {
                                try {
                                    const sFetchUrl = '<?php echo admin_url("admin-ajax.php"); ?>';
                                    const oPostData = new FormData();
                                    oPostData.append('action','<?php echo $sAjaxAction; ?>');
                                    oPostData.append('sMetaValue','<?php echo $sMetaValue; ?>')
                                    const oFetchParams = {
                                        method: 'POST',
                                        body: oPostData
                                    }
                                    const items = await fetch(sFetchUrl,oFetchParams);
                                    return items.json();
                                } catch (err) {
                                    console.error('%coChoiceLocation.setChoices error','color: yellow', err);
                                }
                            },
                            'value',
                            'label',
                            true
                        );
                    }

                    setChoicesLocation();

                    oChoiceLocation.passedElement.element.addEventListener('showDropdown',(e)=>{

                        setChoicesLocation();

                    }, false)

                <?php } ?>

            }
        </script>
		<?php
		$sOutput = ob_get_contents();
		ob_end_clean();
		return $sOutput;
	}


	/**
	 * @return void echo JSON string representation, then exits script execution
	 */
	public static function choices_ajax_locations(){

		$aData = [];
	    $aLocationIds = MH::getLocationIds();

		$aData[] = [
			'value' => 0,
			'label' => 'NON IN ELENCO',
			'selected' => false
		];

	    foreach ($aLocationIds as $sId){

		    $terms  = get_the_terms((int)$sId, 'post_tag_citta');
		    $sCity = '';
		    if ( $terms && ! is_wp_error( $terms ) ) {

			    $sCity = $terms[0]->name;

		    }

		    $bSelected = !empty($_POST['sMetaValue']) && $_POST['sMetaValue'] == $sId;

	        $aData[] = [
                'value' => $sId,
                'label' => get_the_title( $sId  ) . " - ". $sCity,
                'selected' => $bSelected
            ];
        }

		die(json_encode($aData));

    }

	private static function stub($aMetaField,$sMetaValue,$sValidation){

	    ob_start();
        ?>

        <?php
	    $sOutput = ob_get_contents();
	    ob_end_clean();
	    return $sOutput;

    }

}