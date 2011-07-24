<?php
/*
	Plugin Name: WP Count Down
	Plugin URI: http://premiumcoding.com
	Description: Flash Count Down for WordPress
	Version: 1.03
	Author: Gljivec & Zdrifko
	Author URI: http://premiumcoding.com
	
	Copyright 2011, Gljivec & Zdrifko
*/

// check for WP context
if ( !defined('ABSPATH') ){ die(); }
//set install options
function wp_CountDown_install () {
	$newoptionsCountDown = get_option('wpCountDown_options');
	$newoptionsCountDown['numberMargin'] = '0';
	$newoptionsCountDown['scaleSize'] = '1';		
	$newoptionsCountDown['ContainerX'] = '0';	
	$newoptionsCountDown['ContainerY'] = '0';	
	$newoptionsCountDown['positionY'] = '65';			
	$newoptionsCountDown['reflection'] = '1';
	$newoptionsCountDown['year'] = '';
	$newoptionsCountDown['month'] = '';
	$newoptionsCountDown['day'] = '';
	$newoptionsCountDown['hours'] = '';
	$newoptionsCountDown['minute']	='';
	$newoptionsCountDown['width'] = '250';
	$newoptionsCountDown['height'] = '100';	
	$newoptionsCountDown['imageBackground'] = 'true';		
	$newoptionsCountDown['daystext']	='Day';
	$newoptionsCountDown['hourstext'] = 'Hour';
	$newoptionsCountDown['minutestext'] = 'Minute';	
	$newoptionsCountDown['secondstext'] = 'Secund';
	$newoptionsCountDown['textUnder'] = 'True';
	

	add_option('wpCountDown_options', $newoptionsCountDown);
}


// add the admin page
function wp_CountDown_add_pages() {
	add_options_page('Count Down', 'Count Down', 8, __FILE__, 'wp_CountDown_options');
}

if (isset($_GET['page']) && $_GET['page'] == 'wp-flash-countdown/countdown.php'){
	wp_enqueue_script('jquery');
	wp_register_script('drag', plugins_url("wp-flash-countdown/jquery-ui-1.7.1.custom.min.js"), array('jquery','media-upload','thickbox'));
	wp_enqueue_script('drag');		
	wp_register_script('my-upload', plugins_url("wp-flash-countdown/script.js"), array('jquery','media-upload','thickbox'));
	wp_enqueue_script('my-upload');
	wp_enqueue_style('thickbox');
	wp_register_style('myStyleSheets', plugins_url("wp-flash-countdown/style.css"));
    wp_enqueue_style( 'myStyleSheets');

}

function wp_xmlURLpath_countDown($file){
	//path for xml file
	$blogUrl  = explode('/', get_bloginfo('home'));
	$urlServer = str_replace($blogUrl[3],"",$_SERVER['DOCUMENT_ROOT']);
	$urlPlugin = str_replace("http://".$blogUrl[2],"", plugins_url('wp-flash-countdown/'));
	$url = $urlServer.$urlPlugin.$file;
	return $url;
}



function widgetCountDown($wNumber, $name, $customOption, $positionX , $positionY ,$width , $height){
	//$flashtag .= '';
	$mainoptions = get_option('wpCountDown_options');
	if($customOption == '1')
		$xmlpath  = plugins_url("wp-flash-countdown/xml_option_".$wNumber.".xml");
		$positionXN = $positionX;
		$positionYN = $positionY;			
	if($customOption == '0'){
		$xmlpath  = plugins_url("wp-flash-countdown/xml_option_".$wNumber.".xml");
		$positionXN = $mainoptions['ContainerX'];
		$positionYN = $mainoptions['ContainerY'];		
		}
	$widthIn = (int)$width;
	$heightIn = $widthIn / 1.8 ;
		
	$flashtag = '<div style = "position:relative; width:'.$widthIn.'px; height:'.$heightIn.'px; margin-left:'.$positionXN.'px; margin-top:'.$positionYN.'px; text-align:center;"><script type="text/javascript" src="'.plugins_url("wp-flash-countdown/swfobject/swfobject.js").'" charset="utf-8"></script><script type="text/javascript" src="'.plugins_url("wp-flash-countdown/swfobject/swfaddress.js").'" charset="utf-8"></script><script type="text/javascript">
								var flashvars = {
								xmlPath:          "'.$xmlpath.'",	
								cssPath:          "'.plugins_url("wp-flash-countdown/css/style.css").'",
								fontPath:          "'.plugins_url("wp-flash-countdown/fonts/Font.swf").'"};			
								var params = {};
								var attributes = {};
								params.wmode = "transparent";
								swfobject.embedSWF("'.plugins_url("wp-flash-countdown/counter_cs3_v2_reflect.swf").'", "'.$name.'-'.$wNumber.'", "'.$widthIn.'px", "'.$heightIn.'px", "9.0.0", "'.plugins_url("wp-flash-countdown/swfobject/expressInstall.swf").'", flashvars, params, attributes);
							</script>
							<div id="'.$name.'-'.$wNumber.'"></div></div>';
							
	return	$flashtag;						
}
	
function XMLCountDown($wNumber,$scalesize,$containerx ,$containery  ,$numbermargin ,$reflection ,$year,$month,$day,$hours,$minute, $imagebackground,$textunder){
		$mainoptions = get_option('wpCountDown_options');
		$xml = '';
		$xml .= "<?xml version='1.0' encoding='utf-8'?>";
		$xml .= "<?xml-stylesheet type='text/css' href='text.css'?>";
		$xml .=  "<menu ";
		$xml .= " scaleSize = '1'
			ContainerX= '".$containerx ."'
			ContainerY = '".$containery ."'
			numberMargin = '0'
			reflection = '".$reflection ."'	
			reflectionAlpha = '60' 
			reflectionDistance = '0' 
			reflectionRatio = '250'
			textUnder = '".$textunder ."'
			imageBackground = '".$imagebackground ."'>";
		$xml .= "<countdown>
					<year>". $year ."</year>
					<month>". $month ."</month>
					<day>". $day  ."</day>
					<hours>". $hours  ."</hours>
					<minute>". $minute  ."</minute>
					<daysText>".$mainoptions['daystext']."</daysText>
					<hoursText>".$mainoptions['hourstext']."</hoursText>
					<minutesText>".$mainoptions['minutestext']."</minutesText>
					<secondsText>".$mainoptions['secondstext']."</secondsText>
				</countdown>";
		$xml .=  "</menu>";


		$file =  wp_xmlURLpath_countDown("xml_option_".$wNumber.".xml");
		$fh = fopen($file, 'w');
		fwrite($fh, $xml);
		fclose($fh);

}
// create banner widget
function wp_CountDown_createflashcode( $widget=false, $wNumber, $height, $width, $addSettings ,$scalesize ,$containerx ){
	$mainoptions = get_option('wpCountDown_options');
	// write flash 
	if($addSettings == '0'){
	$flashtag = widgetCountDown($wNumber, 'CountDown', $addSettings, $mainoptions['ContainerX'], $mainoptions['ContainerY'] , $width, $height );	
	}
	if($addSettings == '1'){
	$flashtag = widgetCountDown($wNumber, 'CountDown', $addSettings, $containerx , $containery  , $width, $height );	
	}
	
	return $flashtag;
}


//shortcode function
function wp_CountDown_short($atts){
// use [wp-flash-countdown id=1 width=300 height=300] 
//      id must be unique value
//      width and height without px only number
	$mainoptions = get_option('wpCountDown_options');
	extract(shortcode_atts(array(
	'id' => 1,
	'height' => $mainoptions['height'],
	'width' => $mainoptions['width'],
	'scalesize' => 1,
	'containerx' => $mainoptions['ContainerX'],
	'containery' => $mainoptions['ContainerY'],
	'custom_widget' => '0',
	'numbermargin' => 0,
	'reflection' => $mainoptions['reflection'],
	'imagebackground' => $mainoptions['imagebackground'],
	'year' => '2020',
	'month' => '12',
	'day' => '31',	
	'hours' => '10',
	'minute' => '5',
	'textunder' => $mainoptions['textUnder']	
	), $atts));
	


	if($custom_widget == '0'){
		XMLCountDown($id, $scalesize, $containerx , $containery  , $numbermargin , $reflection, $year,$month,$day,$hours,$minute,$imagebackground,$textunder);
		$flashtagShort = widgetCountDown($id, 'CountDownShort', $custom_widget, $containerx, $containery , $width, $height );	
	}
	if($custom_widget == '1'){
		XMLCountDown($id, $scalesize, $containerx ,$containery  ,$numbermargin ,$reflection, $year,$month,$day,$hours,$minute,$imagebackground,$textunder);
		$flashtagShort = widgetCountDown($id, 'CountDownShort', $custom_widget, $containerx , $containery  , $width, $height );	
	}

							
return $flashtagShort;
}



function wp_CountDown_options() {	
	$optionsCountDown = $newoptionsCountDown = get_option('wpCountDown_options');
	// if submitted, process results
	if (!empty($_POST['wpCountDown_submit']))  {
		$newoptionsCountDown['ContainerX'] = strip_tags(stripslashes($_POST["ContainerX"]));
		$newoptionsCountDown['ContainerY'] = strip_tags(stripslashes($_POST["ContainerY"]));
		$newoptionsCountDown['imagebackground'] = strip_tags(stripslashes($_POST["imagebackground"]));		
		$newoptionsCountDown['reflection'] = strip_tags(stripslashes($_POST["reflection"]));	
		$newoptionsCountDown['daystext']	= strip_tags(stripslashes($_POST["daystext"]));
		$newoptionsCountDown['hourstext'] = strip_tags(stripslashes($_POST["hourstext"]));
		$newoptionsCountDown['minutestext'] = strip_tags(stripslashes($_POST["minutestext"]));	
		$newoptionsCountDown['secondstext'] = strip_tags(stripslashes($_POST["secondstext"]));
		$newoptionsCountDown['textunder'] = strip_tags(stripslashes($_POST["textunder"]));

	// if changes save!
	if ( $optionsCountDown != $newoptionsCountDown ) {
		$optionsCountDown = $newoptionsCountDown;
		update_option('wpCountDown_options', $optionsCountDown);
	}		
	}
	
?>
	<div class="allBanner">
	<div class = "buttons">
	<div class= "settingsB" id = "settingsB"><a href="" onClick="return false;">Settings</a></div>
	<div class = "helpB" id = "helpB"><a href="" onClick="return false;">Help</a></div>	
	</div>
	<input type="hidden" id="path" value="<?php echo plugins_url('wp-flash-countdown/')?>">
<?php


	
?> 

	<div id="help"><h2 >Help</h2>
	ShortCode:<br>
	If you want to use custom settings use following ShortCode:<br>
    <b><font size="1">[CountDown id=1 width=250 custom_widget=1 containerx=0 containerxy=0 imagebackground=true reflection=true year=2011 month=7 day=25 hours=5 minute=10 textunder=true]</font></b><br><br>
    If you want to use default settings use following ShortCode:<br>
    <b><font size="1">[CountDown id=1 width=250 custom_widget=0 year=2011 month=7 day=25 hours=5 minute=10]</font></b><br><br>
    Visit our support site for more help <a href="http://premiumcoding.com">PremiumCoding</a>.<br>
    In case you need additional support please contact us at <a href="mailto:info@premiumcoding.com">info@premiumcoding.com</a>	
	<br><br>
	Support our work :
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
	<input type="hidden" name="cmd" value="_s-xclick">
	<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
	<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
	<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
	</form>
	</div>
	<div id="settings"><?php
	// options form
	echo '<form name="settings" method="post">';
	echo "<div class=\"wrap\"><h2>Count Down Settings</h2>";
	echo '<table class="form-table">';
	// ContainerX
	echo '<tr valign="top"><th scope="row">X Position</th>';
	echo '<td><input type="text" name="ContainerX" value="'.$optionsCountDown['ContainerX'].'" size="5"></input><br />Position of countdown on X axis.</td></tr>';
	// ContainerY
	echo '<tr valign="top"><th scope="row">Y Position</th>';
	echo '<td><input type="text" name="ContainerY" value="'.$optionsCountDown['ContainerY'].'" size="5"></input><br />Position of countdown on Y axis.</td></tr>';	
	//reflection
	echo '<tr valign="top"><th scope="row">Reflection</th>';	
	echo '<td><input type="radio" name="reflection" value="true"';
	if( $optionsCountDown['reflection'] == 'true' ){ echo ' checked="checked" '; }
	echo '></input> True (if you select true reflection will be visible)<br /><input type="radio" name="reflection" value="false"';
	if( $optionsCountDown['reflection'] == 'false' ){ echo ' checked="checked" '; }
	echo '></input> False (if you select false reflection will not be visible)<br /></td></tr>';
	echo '<tr valign="top"><th scope="row">Use Background Image</th>';	
	echo '<td><input type="radio" name="imagebackground" value="true"';
	if( $optionsCountDown['imagebackground'] == 'true' ){ echo ' checked="checked" '; }
	echo '></input> True (if you select true default background image will be visible)<br /><input type="radio" name="imagebackground" value="false"';
	if( $optionsCountDown['imagebackground'] == 'false' ){ echo ' checked="checked" '; }
	echo '></input> False (if you select false default background image will be disabled and background will be transparent)<br /></td></tr>';	
	//	textUnder
	echo '<tr valign="top"><th scope="row">Show text on CountDown</th>';	
	echo '<td><input type="radio" name="textunder" value="true"';
	if( $optionsCountDown['textunder'] == 'true' ){ echo ' checked="checked" '; }
	echo '></input> True (if you select true text will be visible)<br /><input type="radio" name="textunder" value="false"';
	if( $optionsCountDown['textunder'] == 'false' ){ echo ' checked="checked" '; }
	echo '></input> False (if you select false dtext will be disabled.)<br /></td></tr>';
	// daystext
	echo '<tr valign="top"><th scope="row">Value for Days text</th>';
	echo '<td><input type="text" name="daystext" value="'.$optionsCountDown['daystext'].'" size="5"></input><br />Enter value for day text.</td></tr>';
	// hourstext
	echo '<tr valign="top"><th scope="row">Value for Hours text</th>';
	echo '<td><input type="text" name="hourstext" value="'.$optionsCountDown['hourstext'].'" size="5"></input><br />Enter value for hour text.</td></tr>';	
	// minutestext
	echo '<tr valign="top"><th scope="row">Value for Minutes text</th>';
	echo '<td><input type="text" name="minutestext" value="'.$optionsCountDown['minutestext'].'" size="5"></input><br />Enter value for minute text.</td></tr>';
	// secondstext
	echo '<tr valign="top"><th scope="row">Value for Seconds text</th>';
	echo '<td><input type="text" name="secondstext" value="'.$optionsCountDown['secondstext'].'" size="5"></input><br />Enter value for secund text.</td></tr>';
	echo '</table>';
	echo '<input type="hidden" name="wpCountDown_submit" value="true"></input>';
	echo '<p class="submit"><input type="submit" value="Update settings &raquo;"></input></p>';
	echo "</div>";
	echo "</div>";
	echo '</form>';
	echo "</div>";
	}
	
//uninstall all options
function wp_CountDown_uninstall () {
	delete_option('wpCountDown_options');
}


add_action('init', 'widget_CountDown_register');

function widget_CountDown_register() {
 
	$prefix = 'countdown'; // $id prefix
	$name = __('Count Down');
	$widget_ops = array('classname' => 'widget_CountDown', 'description' => __('Count Down'));
	$control_ops = array('width' => 400, 'height' => 200, 'id_base' => $prefix);
 
	$options = get_option('widget_CountDown');
	if(isset($options[0])) unset($options[0]);
 
	if(!empty($options)){
		foreach(array_keys($options) as $widget_number){
			wp_register_sidebar_widget($prefix.'-'.$widget_number, $name, 'widget_CountDown', $widget_ops, array( 'number' => $widget_number ));
			wp_register_widget_control($prefix.'-'.$widget_number, $name, 'widget_CountDown_control', $control_ops, array( 'number' => $widget_number ));
		}
	} else{
		$options = array();
		$widget_number = 1;
		wp_register_sidebar_widget($prefix.'-'.$widget_number, $name, 'widget_CountDown', $widget_ops, array( 'number' => $widget_number ));
		wp_register_widget_control($prefix.'-'.$widget_number, $name, 'widget_CountDown_control', $control_ops, array( 'number' => $widget_number ));
	}
}


function widget_CountDown($args, $vars = array()) {
	extract($args);
	// get widget saved options
	$widget_number = (int)str_replace('countdown-', '', @$widget_id);
	$options = get_option('widget_CountDown');
	if(!empty($options[$widget_number])){
		$vars = $options[$widget_number];
	}
	// widget open tags
	echo $before_widget;
 
	// print title from admin 
	if(!empty($vars['title'])){
		echo $before_title . $vars['title'] . $after_title;
	} 
	if( !stristr( $_SERVER['PHP_SELF'], 'widgets.php' ) ){
		echo wp_CountDown_createflashcode(true,$vars['id'],$vars['height'],$vars['width'],$vars['addSettings'] ,$vars['scalesize'], $vars['containerx'] ,$vars['containery'] );

		}
	echo $after_widget;
	

}


function widget_CountDown_control($args) {
	
	$prefix = 'countdown'; // $id prefix
 
	$options = get_option('widget_CountDown');
	if(empty($options)) $options = array();
	if(isset($options[0])) unset($options[0]);
 
	// update options array
	if(!empty($_POST[$prefix]) && is_array($_POST)){
		foreach($_POST[$prefix] as $widget_number => $values){
			if(empty($values) && isset($options[$widget_number])) // user clicked cancel
				continue;
 
			if(!isset($options[$widget_number]) && $args['number'] == -1){
				$args['number'] = $widget_number;
				$options['last_number'] = $widget_number;
			}
			$options[$widget_number] = $values;

		}
 
		// update number
		if($args['number'] == -1 && !empty($options['last_number'])){
			$args['number'] = $options['last_number'];
		}
 
		// clear unused options and update options in DB. return actual options array
		$options = bf_smart_multiwidget_update($prefix, $options, $_POST[$prefix], $_POST['sidebar'], 'widget_CountDown');
	}
 
	// $number - is dynamic number for multi widget, gived by WP
	// by default $number = -1 (if no widgets activated). In this case we should use %i% for inputs
	//   to allow WP generate number automatically
	
	$number = ($args['number'] == -1)? '%i%' : $args['number'];
	
	// now we can output control
	$opts = @$options[$number];
 
	$title = @$opts['title'];
	$id = @$opts['id'];
	$addSettings = @$opts['addSettings'];
	$containerx = @$opts['containerx'];
	$containery = @$opts['containery'];
	$numbermargin = @$opts['numbermargin'];
	$reflection = @$opts['reflection'];
	$year = @$opts['year'];
	$month = @$opts['month'];
	$day = @$opts['day'];
	$hours = @$opts['hours'];	
	$minute = @$opts['minute'];	
	$height = @$opts['height'];
	$width = @$opts['width'];
	$scalesize = @$opts['scalesize'];
	$imagebackground = @$opts['imagebackground'];
	$textunder = @$opts['textunder '];	
	
	$mainoptions = get_option('wpCountDown_options');
	// write flash 
	if($addSettings == '0'){	
		XMLCountDown($id, $mainoptions['scaleSize'], $mainoptions['ContainerX'] , $mainoptions['ContainerY']  , $mainoptions['numberMargin'] , $mainoptions['reflection'] , $year,$month,$day,$hours,$minute,$imagebackground,$textunder  );
	}
	if($addSettings == '1'){	
		XMLCountDown($id, $scalesize, $containerx ,$containery  ,$numbermargin ,$reflection, $year,$month,$day,$hours,$minute,$imagebackground,$textunder );
	}
			

	?>
	<table class="form-table"><tr valign="top"><tr><td>
    Title of countdown <br/><input type="text" name="<?php echo $prefix; ?>[<?php echo $number; ?>][title]" value="<?php echo $title; ?>" /><br/></td>
	<td>ID Count Down	<br/><input type="text" name="<?php echo $prefix; ?>[<?php echo $number; ?>][id]" value="<?php echo $id; ?>" /><br/></td></tr>
    <?php
	// width
	echo '<tr valign="top"><th scope="row">Width</th>';
	echo '<td><input type="text" name="'. $prefix.'['. $number .'][width]" value="'.$width.'" size="5"></input><br />Width of countdown in pixels (200 or more is recommended)</td></tr>';
	//year
	echo '<tr valign="top"><th scope="row">Year</th>';
	echo '<td><input  type="text" name="'. $prefix.'['. $number .'][year]" value="'.$year.'" size="8"></input><br />Year</td></tr>';	
	// month
	echo '<tr valign="top"><th scope="row">Month</th>';
	echo '<td><input  type="text" name="'. $prefix.'['. $number .'][month]" value="'.$month.'" size="8"></input><br />Month</td></tr>';	
		// day
	echo '<tr valign="top"><th scope="row">Day</th>';
	echo '<td><input  type="text" name="'. $prefix.'['. $number .'][day]" value="'.$day.'" size="8"></input><br />Day</td></tr>';
	//hours
	echo '<tr valign="top"><th scope="row">Hours</th>';
	echo '<td><input  type="text" name="'. $prefix.'['. $number .'][hours]" value="'.$hours.'" size="8"></input><br />Hours</td></tr>';	
	// minute
	echo '<tr valign="top"><th scope="row">Minute</th>';
	echo '<td><input  type="text" name="'. $prefix.'['. $number .'][minute]" value="'.$minute.'" size="8"></input><br />Minutes</td></tr></table>';	
	echo '<hr><table class="form-table"><tr valign="top"><th scope="row">Custom settings (if set to true settings below will be applied otherwise default settings will be used.</th>';
	echo '<td><input type="radio" name="'. $prefix.'['. $number .'][addSettings]" id="addSettings" value="1"';
	if( $addSettings == '1' ){ echo ' checked="checked" '; }
	echo 'onclick="document.getElementById(\'customSettings\').style.display = \'\'"> True<br /><input type="radio" name="'. $prefix.'['. $number .'][addSettings]" id="addSettings" value="0"';
	if( $addSettings == '0' ){ echo ' checked="checked" '; }
	echo 'onclick="document.getElementById(\'customSettings\').style.display = \'none\'"> False<br /></td></tr></table>';	
	echo '<table class="form-table">';
	// ContainerX
	echo '<tr valign="top"><th scope="row">X Position</th>';
	echo '<td><input type="text" name="'. $prefix.'['. $number .'][containerx]" value="'.$containerx.'" size="5"></input><br />Position of countdown on X axis.</td></tr>';
	// ContainerY
	echo '<tr valign="top"><th scope="row">Y Position</th>';
	echo '<td><input type="text" name="'. $prefix.'['. $number .'][containery]" value="'.$containery.'" size="5"></input><br />Position of countdown on Y axis.</td></tr>';	
	//reflection
	echo '<tr valign="top"><th scope="row">Reflection</th>';	
	echo '<td><input type="radio" name="'. $prefix.'['. $number .'][reflection]" value="true"';
	if( $reflection == 'true' ){ echo ' checked="checked" '; }
	echo '></input>  True (if you select true reflection will be enabled)<br /><input type="radio" name="'. $prefix.'['. $number .'][reflection]" value="false"';
	if( $reflection == 'false' ){ echo ' checked="checked" '; }
	echo '></input>  False (if you select false reflection will be disabled)<br /></td></tr>';	
		//reflection
	echo '<tr valign="top"><th scope="row">Use Background Image</th>';	
	echo '<td><input type="radio" name="'. $prefix.'['. $number .'][imagebackground]" value="true"';
	if( $imagebackground == 'true' ){ echo ' checked="checked" '; }
	echo '></input> True (if you select true default background image will be visible)<br /><input type="radio" name="'. $prefix.'['. $number .'][imagebackground]" value="false"';
	if( $imagebackground == 'false' ){ echo ' checked="checked" '; }
	echo '></input> False (if you select false default background image will be disabled and background will be transparent)<br /></td></tr>';	
	//	textUnder
	echo '<tr valign="top"><th scope="row">Show text on CountDown</th>';	
	echo '<td><input type="radio" name="'. $prefix.'['. $number .'][textunder ]" value="true"';
	if( $textunder == 'true' ){ echo ' checked="checked" '; }
	echo '></input> True (if you select true text will be visible)<br /><input type="radio" name="'. $prefix.'['. $number .'][textunder ]" value="false"';
	if( $textunder == 'false' ){ echo ' checked="checked" '; }
	echo '></input> False (if you select false dtext will be disabled.)<br /></td></tr>';
	echo '</table>';


}





// helper function can be defined in another plugin
if(!function_exists('bf_smart_multiwidget_update')){
	function bf_smart_multiwidget_update($id_prefix, $options, $post, $sidebar, $option_name = ''){
		global $wp_registered_widgets;
		static $updated = false;
 
		// get active sidebar
		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( isset($sidebars_widgets[$sidebar]) )
			$this_sidebar =& $sidebars_widgets[$sidebar];
		else
			$this_sidebar = array();
 
		// search unused options
		foreach ( $this_sidebar as $_widget_id ) {
			if(preg_match('/'.$id_prefix.'-([0-9]+)/i', $_widget_id, $match)){
				$widget_number = $match[1];
 
				// $_POST['widget-id'] contain current widgets set for current sidebar
				// $this_sidebar is not updated yet, so we can determine which was deleted
				if(!in_array($match[0], $_POST['widget-id'])){
					unset($options[$widget_number]);
				}
			}
		}
 
		// update database
		if(!empty($option_name)){
			update_option($option_name, $options);
			$updated = true;
		}
 
		// return updated array
		return $options;
	}
}


// add the actions
add_action('admin_menu', 'wp_CountDown_add_pages');
register_activation_hook( __FILE__, 'wp_CountDown_install' );
register_deactivation_hook( __FILE__, 'wp_CountDown_uninstall' );
	
add_shortcode('CountDown', 'wp_CountDown_short');
	


?>