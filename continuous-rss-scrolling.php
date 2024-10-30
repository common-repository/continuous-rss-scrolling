<?php
/*
Plugin Name: Continuous rss scrolling
Plugin URI: http://www.gopiplus.com/work/2010/09/05/continuous-rss-scrolling/
Description: This plugin will scroll the RSS title continuously in the wordpress website, we can use this plugin as a widget.
Author: Gopi Ramasamy
Version: 11.2
Author URI: http://www.gopiplus.com/work/2010/09/05/continuous-rss-scrolling/
Donate link: http://www.gopiplus.com/work/2010/09/05/continuous-rss-scrolling/
Tags: Continuous, announcement, scroller, message, rss
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: continuous-rss-scrolling
Domain Path: /languages
*/

global $wpdb, $wp_version;

function crs_shortcode( $atts ) {
	$crs_html = "";
	$crs_x = "";
	
	//[continuous-rss-scrolling url="http://www.gopiplus.com/work/category/word-press-plug-in/feed/" height="40" count="5" length="200" speed="2" wait="2"]
	if (!is_array($atts)) {
		return '';
	}
	$url = $atts['url'];
	$crs_record_height = $atts['height'];
	$crs_display_count = $atts['count'];
	$crs_display_width = $atts['length'];
	$speed = $atts['speed'];
	$waitseconds = $atts['wait'];
	
	if(!is_numeric($crs_display_width)) {
		$crs_display_width = 200;
	} 
	
	if(!is_numeric($crs_record_height)) {
		$crs_record_height = 30;
	}
	
	if(!is_numeric($crs_display_count)) {
		$crs_display_count = 5;
	}	

	if(!is_numeric($speed)) { 
		$speed = 2; 
	}
	
	if(!is_numeric($waitseconds)) { 
		$waitseconds = 2; 
	}
	
	if( $url == "" ) { 
		$url = get_option('crs_rss_url'); 
	}
	
	$crsout = "";
	$cnt = 0;
	$crs_count = 0;
	$maxitems = 0;
	include_once( ABSPATH . WPINC . '/feed.php' );
	$rss = fetch_feed($url);
	
	if (!is_wp_error($rss)) {
		$cnt = 0;
		$maxitems = $rss->get_item_quantity( 10 ); 
		$rss_items = $rss->get_items( 0, $maxitems );
		if ( $maxitems > 0 ) {
			foreach ( $rss_items as $item ) {
				$get_permalink = $item->get_permalink();
				$crs_post_title = $item->get_title();
				
				$crs_post_title = substr($crs_post_title, 0, $crs_display_width);
				$dis_height = $crs_record_height."px";
				$crs_html = $crs_html . "<div class='crs_div' style='height:$dis_height;padding:2px 0px 2px 0px;'>"; 
				$crs_html = $crs_html . "<a target='_blank' href='$get_permalink'>$crs_post_title</a>";
				$crs_html = $crs_html . "</div>";
				
				$crs_post_title = trim($crs_post_title);
				$get_permalink = $get_permalink;
				$crs_x = $crs_x . "crs_array[$crs_count] = '<div class=\'crs_div\' style=\'height:$dis_height;padding:2px 0px 2px 0px;\'><a target=\'_blank\' href=\'$get_permalink\'>$crs_post_title</a></div>'; ";	
				$crs_count++;
				$cnt++;
			}
			
			$crs_record_height = $crs_record_height + 0;
			if($crs_count >= $crs_display_count) {
				$crs_count = $crs_display_count;
				$crs_height = ($crs_record_height * $crs_display_count);
			}
			else {
				$crs_height = ($crs_count * $crs_record_height);
			}
			$crs_height1 = $crs_record_height."px";
				
			$crsout = '<div style="padding-top:8px;padding-bottom:8px;">';
				$crsout .= '<div style="text-align:left;vertical-align:middle;text-decoration: none;overflow: hidden; position: relative; margin-left: 1px; height:' . $crs_height1. ';" id="crs_Holder">';
					$crsout .= $crs_html;
				$crsout .= '</div>';
			$crsout .= '</div>';
			$crsout .= '<script type="text/javascript">';
			$crsout .= 'var crs_array = new Array();';
			$crsout .= "var crs_obj	= '';";
			$crsout .= "var crs_scrollPos = '';";
			$crsout .= "var crs_numScrolls= '';";
			$crsout .= "var crs_heightOfElm = '" . $crs_record_height . "';";
			$crsout .= "var crs_numberOfElm = '" . $crs_count . "';";
			$crsout .= "var crs_speed = '" . $speed . "';";
			$crsout .= "var crs_waitseconds = '" . $waitseconds . "';";
			$crsout .= "var crs_scrollOn = 'true';";
			$crsout .= 'function crs_createscroll()';
			$crsout .= '{';
				$crsout .= $crs_x;
				$crsout .= "crs_obj	= document.getElementById('crs_Holder');";
				$crsout .= "crs_obj.style.height = (crs_numberOfElm * crs_heightOfElm) + 'px';";
				$crsout .= 'crs_content();';
			$crsout .= '}';
			$crsout .= '</script>';
			$crsout .= '<script type="text/javascript">';
			$crsout .= 'crs_createscroll();';
			$crsout .= '</script>';
		}
		else {
			$crsout = 'No data available';
		}
	}
	else {
		$crsout = 'No data available';
	}
	
	return $crsout;
}

function crs() 
{
	global $wpdb;
	$crs_html = "";
	$crs_x = "";
	$crs_display_width = get_option('crs_display_width');
	$crs_display_count = get_option('crs_display_count');
	$crs_record_height = get_option('crs_record_height');
	
	if(!is_numeric($crs_display_width)) {
		$crs_display_width = 200;
	} 
	if(!is_numeric($crs_record_height)) {
		$crs_record_height = 30;
	}
	if(!is_numeric($crs_display_count)) {
		$crs_display_count = 5;
	}
	
	$crs_speed = get_option('crs_speed');
	$crs_waitseconds = get_option('crs_waitseconds');
	if(!is_numeric($crs_speed)) { $crs_speed = 2; }
	if(!is_numeric($crs_waitseconds)) { $crs_waitseconds = 2; }
	
	if(get_option('crs_rss_url') <> "") {
		$url = get_option('crs_rss_url');
	}
	else {
		$url = "http://www.wordpress.org/news/feed/";
	}
	
	$arr = array();
	$arr["length"] 	= $crs_display_width;
	$arr["count"] 	= $crs_display_count;
	$arr["height"] 	= $crs_record_height;
	$arr["url"] 	= $url;
	$arr["speed"] 	= $crs_speed;
	$arr["wait"] 	= $crs_waitseconds;
	//[continuous-rss-scrolling url="http://www.gopiplus.com/work/category/word-press-plug-in/feed/" height="40" count="5" length="200" speed="2" wait="2"]
	echo crs_shortcode($arr);
}

function crs_install() 
{
	global $wpdb;
	add_option('crs_title', "RSS scroller");
	add_option('crs_display_width', "200");
	add_option('crs_display_count', "5");
	add_option('crs_record_height', "40");
	add_option('crs_rss_url', "http://www.gopiplus.com/work/category/word-press-plug-in/feed/");
	add_option('crs_speed', "2");
	add_option('crs_waitseconds', "2");
}

function crs_admin_options() 
{
	?>
	<div class="wrap">
	  <div class="form-wrap">
		<div id="icon-edit" class="icon32 icon32-posts-post"></div>
		<h2><?php _e('Continuous rss scrolling', 'continuous-rss-scrolling'); ?></h2>
		<?php
		$crs_title 			= get_option('crs_title');
		$crs_display_width 	= get_option('crs_display_width');
		$crs_display_count 	= get_option('crs_display_count');
		$crs_record_height 	= get_option('crs_record_height');
		$crs_rss_url 		= get_option('crs_rss_url');
		$crs_speed 			= get_option('crs_speed');
		$crs_waitseconds 	= get_option('crs_waitseconds');
		
		if (isset($_POST['crs_form_submit']) && $_POST['crs_form_submit'] == 'yes')
		{
			//	Just security thingy that wordpress offers us
			check_admin_referer('crs_form_setting');
			
			$crs_title 			= stripslashes(sanitize_text_field($_POST['crs_title']));
			$crs_display_width 	= stripslashes(intval($_POST['crs_display_width']));
			$crs_display_count 	= stripslashes(intval($_POST['crs_display_count']));
			$crs_record_height 	= stripslashes(intval($_POST['crs_record_height']));
			$crs_rss_url 		= stripslashes(esc_url_raw($_POST['crs_rss_url']));
			$crs_speed 			= stripslashes(intval($_POST['crs_speed']));
			$crs_waitseconds 	= stripslashes(intval($_POST['crs_waitseconds']));
			
			update_option('crs_title', $crs_title );
			update_option('crs_display_width', $crs_display_width );
			update_option('crs_display_count', $crs_display_count );
			update_option('crs_record_height', $crs_record_height );
			update_option('crs_rss_url', $crs_rss_url );
			update_option('crs_speed', $crs_speed );
			update_option('crs_waitseconds', $crs_waitseconds );
			?>
			<div class="updated fade">
				<p><strong><?php _e('Details successfully updated.', 'continuous-rss-scrolling'); ?></strong></p>
			</div>
			<?php
		}
		?>
		<h3><?php _e('Plugin setting', 'continuous-rss-scrolling'); ?></h3>
		<form name="crs_form" method="post" action="#">
		
			<label for="tag-title"><?php _e('Title', 'continuous-rss-scrolling'); ?></label>
			<input name="crs_title" type="text" value="<?php echo $crs_title; ?>"  id="crs_title" size="70" maxlength="200">
			<p><?php _e('Please enter your widget title.', 'continuous-rss-scrolling'); ?></p>
			
			<label for="tag-title"><?php _e('Scroll height', 'continuous-rss-scrolling'); ?></label>
			<input name="crs_record_height" type="text" value="<?php echo $crs_record_height; ?>"  id="crs_record_height" maxlength="3">
			<p><?php _e('If any overlap in the announcement text at front end, <br>you should arrange(increase/decrease) the above height.', 'continuous-rss-scrolling'); ?></p>
			
			<label for="tag-title"><?php _e('Display count', 'continuous-rss-scrolling'); ?></label>
			<input name="crs_display_count" type="text" value="<?php echo $crs_display_count; ?>"  id="crs_display_count" maxlength="3">
			<p><?php _e('Please enter number of records you want to display at the same time in scroll.', 'continuous-rss-scrolling'); ?></p>
			
			<label for="tag-title"><?php _e('Display length', 'continuous-rss-scrolling'); ?></label>
			<input name="crs_display_width" type="text" value="<?php echo $crs_display_width; ?>"  id="crs_display_width" maxlength="3">
			<p><?php _e('Please enter max number of character to display in the scroll.', 'continuous-rss-scrolling'); ?></p>
			
			<label for="tag-title"><?php _e('RSS url', 'continuous-rss-scrolling'); ?></label>
			<input name="crs_rss_url" type="text" value="<?php echo $crs_rss_url; ?>"  id="crs_rss_url" size="70">
			<p><?php _e('Please enter your RSS url.', 'continuous-rss-scrolling'); ?></p>
		
			<label for="crs_speed"><?php _e('Scrolling speed', 'continuous-rss-scrolling'); ?></label>
			<?php _e( 'Slow', 'continuous-rss-scrolling' ); ?> 
			<input name="crs_speed" type="range" value="<?php echo $crs_speed; ?>"  id="crs_speed" min="1" max="10" /> 
			<?php _e( 'Fast', 'continuous-rss-scrolling' ); ?> 
			<p><?php _e('Select how fast you want the to scroll the items.', 'continuous-rss-scrolling'); ?></p>
			
			<label for="crs_waitseconds"><?php _e( 'Seconds to wait', 'continuous-rss-scrolling' ); ?></label>
			<input name="crs_waitseconds" type="text" value="<?php echo $crs_waitseconds; ?>" id="crs_waitseconds" maxlength="4" />
			<p><?php _e( 'How many seconds you want the wait to scroll', 'continuous-rss-scrolling' ); ?> (<?php _e( 'Example', 'continuous-rss-scrolling' ); ?>: 5)</p>
		
			<div style="height:10px;"></div>
			<input type="hidden" name="crs_form_submit" value="yes"/>
			<input name="crs_submit" id="crs_submit" class="button" value="Submit" type="submit" />
			<a class="button" target="_blank" href="http://www.gopiplus.com/work/2010/09/05/continuous-rss-scrolling/"><?php _e('Help', 'continuous-rss-scrolling'); ?></a>
			<?php wp_nonce_field('crs_form_setting'); ?>
		</form>
		</div>
		<h3><?php _e('Plugin configuration option', 'continuous-rss-scrolling'); ?></h3>
		<ol>
			<li><?php _e('Drag and drop the widget to your sidebar.', 'continuous-rss-scrolling'); ?></li>
			<li><?php _e('Add directly in to the theme using PHP code.', 'continuous-rss-scrolling'); ?></li>
		</ol>
	<p class="description"><?php _e('Check official website for more information', 'continuous-rss-scrolling'); ?> 
	<a target="_blank" href="http://www.gopiplus.com/work/2010/09/05/continuous-rss-scrolling/"><?php _e('click here', 'continuous-rss-scrolling'); ?></a></p>
	</div>
	<?php
}

function crs_add_to_menu() {
	add_options_page(__('Continuous rss scrolling', 'continuous-rss-scrolling'), 
			__('Continuous rss scrolling', 'continuous-rss-scrolling'), 'manage_options', 'continuous-rss-scrolling', 'crs_admin_options' );
}

if (is_admin()) {
	add_action('admin_menu', 'crs_add_to_menu');
}

function crs_deactivation() {
	// No action required.
}

function crs_add_javascript_files() {
	if (!is_admin()) {
		wp_enqueue_script( 'continuous-rss-scrolling', plugins_url().'/continuous-rss-scrolling/continuous-rss-scrolling.js');
	}
}

function crs_textdomain() {
	  load_plugin_textdomain( 'continuous-rss-scrolling', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

class crs_widget_register extends WP_Widget 
{
	function __construct() 
	{
		$widget_ops = array('classname' => 'widget_text crs-widget', 
						'description' => __('Create the vertical scroll in the widget using given rss feed', 
							'continuous-rss-scrolling'), 'continuous-rss-scrolling');
		parent::__construct('ContinuousRssScrolling', __('Continuous rss scrolling', 'continuous-rss-scrolling'), $widget_ops);
	}
	
	function widget( $args, $instance ) 
	{
		extract( $args, EXTR_SKIP );

		$title 	= apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$width	= $instance['width'];
		$count	= $instance['count'];
		$height	= $instance['height'];
		$url	= $instance['url'];		
		$speed			= $instance['speed'];
		$waitseconds	= $instance['waitseconds'];
		echo $args['before_widget'];
		if ( ! empty( $title ) )
		{
			echo $args['before_title'] . $title . $args['after_title'];
		}
		// Call widget method
		$arr = array();
		$arr["length"] 	= $width;
		$arr["count"] 	= $count;
		$arr["height"] 	= $height;
		$arr["url"] 	= $url;
		$arr["speed"] 	= $speed;
		$arr["wait"] = $waitseconds;
		//[continuous-rss-scrolling url="http://www.gopiplus.com/work/category/word-press-plug-in/feed/" height="40" count="5" length="200" speed="2" wait="2"]
		echo crs_shortcode($arr);
		// Call widget method
		echo $args['after_widget'];
	}
	
	function update( $new_instance, $old_instance ) 
	{
		$instance 			= $old_instance;
		$instance['title'] 	= ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['width'] 	= ( ! empty( $new_instance['width'] ) ) ? strip_tags( $new_instance['width'] ) : '';
		$instance['count'] 	= ( ! empty( $new_instance['count'] ) ) ? strip_tags( $new_instance['count'] ) : '';
		$instance['height'] = ( ! empty( $new_instance['height'] ) ) ? strip_tags( $new_instance['height'] ) : '';
		$instance['url'] 	= ( ! empty( $new_instance['url'] ) ) ? strip_tags( $new_instance['url'] ) : '';
		$instance['speed'] 			= ( ! empty( $new_instance['speed'] ) ) ? strip_tags( $new_instance['speed'] ) : '';
		$instance['waitseconds'] 	= ( ! empty( $new_instance['waitseconds'] ) ) ? strip_tags( $new_instance['waitseconds'] ) : '';
		return $instance;
	}
	
	function form( $instance ) 
	{
		$defaults = array(
			'title' 	=> '',
            'width' 	=> '',
            'count' 	=> '',
            'height' 	=> '',
			'url' 		=> '',
			'speed' 		=> '',
			'waitseconds' 	=> ''
        );
		
		$instance 	= wp_parse_args( (array) $instance, $defaults);
        $title 		= $instance['title'];
        $width 		= $instance['width'];
        $count 		= $instance['count'];
        $height 	= $instance['height'];
		$url 		= $instance['url'];
		$speed 			= $instance['speed'];
		$waitseconds 	= $instance['waitseconds'];
		?>
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'continuous-rss-scrolling'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
		<p>
		<label for="<?php echo $this->get_field_id('url'); ?>"><?php _e('RSS url', 'continuous-rss-scrolling'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('url'); ?>" name="<?php echo $this->get_field_name('url'); ?>" type="text" value="<?php echo $url; ?>" />
		Enter your rss url.
        </p>
		<p>
		<label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Display length', 'continuous-rss-scrolling'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $width; ?>" />
		Enter max number of character to display in the scroll.
        </p>
		<p>
		<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Display count', 'continuous-rss-scrolling'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo $count; ?>" />
		Enter number of records you want to display at the same time in scroll.
        </p>
		<p>
		<label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height', 'continuous-rss-scrolling'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $height; ?>" />
		If any overlap in the scroll at front end, you should arrange(increase/decrease) this height.
        </p>
		
		<p>
            <label for="<?php echo $this->get_field_id('speed'); ?>"><?php _e('Scrolling speed', 'information-reel'); ?></label><br />
			<select class="" id="<?php echo $this->get_field_id('speed'); ?>" name="<?php echo $this->get_field_name('speed'); ?>" style="width:130px;">
				<option value="">Select</option>
				<option value="1" <?php $this->crs_render_selected($speed=='1'); ?>>1</option>
				<option value="2" <?php $this->crs_render_selected($speed=='2'); ?>>2</option>
				<option value="3" <?php $this->crs_render_selected($speed=='3'); ?>>3</option>
				<option value="4" <?php $this->crs_render_selected($speed=='4'); ?>>4</option>
				<option value="5" <?php $this->crs_render_selected($speed=='5'); ?>>5</option>
				<option value="6" <?php $this->crs_render_selected($speed=='6'); ?>>6</option>
				<option value="7" <?php $this->crs_render_selected($speed=='7'); ?>>7</option>
				<option value="8" <?php $this->crs_render_selected($speed=='8'); ?>>8</option>
				<option value="9" <?php $this->crs_render_selected($speed=='9'); ?>>9</option>
				<option value="10" <?php $this->crs_render_selected($speed=='10'); ?>>10</option>
			</select>
			<?php _e('Select how fast you want the to scroll the items.', 'information-reel'); ?>
        </p>
		<p>
            <label for="<?php echo $this->get_field_id('waitseconds'); ?>"><?php _e('Seconds to wait', 'information-reel'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('waitseconds'); ?>" name="<?php echo $this->get_field_name('waitseconds'); ?>" type="text" value="<?php echo $waitseconds; ?>" maxlength="3" />
			<?php _e('How many seconds you want to wait to scroll. Enter only number.', 'information-reel'); ?>
        </p>
		
		<p><a target="_blank" href="http://www.gopiplus.com/work/2010/09/05/continuous-rss-scrolling/"><?php _e('click here', 'continuous-rss-scrolling'); ?></a></p>
		<?php
	}
	
	function crs_render_selected($var) 
	{
		if ($var==1 || $var==true) 
		{
			echo 'selected="selected"';
		}
	}
}

function crs_widget_loading() {
	register_widget( 'crs_widget_register' );
}

add_shortcode('continuous-rss-scrolling', 'crs_shortcode');
add_action('plugins_loaded', 'crs_textdomain');
add_action('init', 'crs_add_javascript_files');
add_action( 'widgets_init', 'crs_widget_loading');
register_activation_hook(__FILE__, 'crs_install');
register_deactivation_hook(__FILE__, 'crs_deactivation');
?>