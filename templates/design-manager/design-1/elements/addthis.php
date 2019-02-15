<?php 
global $redux_builder_amp;
$data_pub_id = ampforwp_get_setting('add-this-pub-id');
$data_widget_id = ampforwp_get_setting('add-this-widget-id');
if ( is_single() || (is_page() && ampforwp_get_setting('ampforwp-page-social')) ) { 
 	if( ampforwp_get_setting('enable-add-this-option') ) {
		$amp_addthis = '<amp-addthis width="320" height="92" data-pub-id="'.$data_pub_id.'" data-widget-id="'.$data_widget_id.'"></amp-addthis>';
		do_action('ampforwp_before_social_icons_hook',$this);
		echo $amp_addthis;
		do_action('ampforwp_after_social_icons_hook',$this);
	}
}
?>
