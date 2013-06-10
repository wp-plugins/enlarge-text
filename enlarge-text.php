<?php
/*
Plugin Name: Enlarge Text
Plugin URI: http://wordpress.org/extend/plugins/enlarge-text/
Description: Give your users a widget to enlarge the text on your site.  Is there some text that you don't want to enlarge?  Just declare a size for it in pixels in your stylesheet and this plugin will not affect it.
Author: Scott Fennell
Version: 1.4
Author URI: www.scottfennell.com/wordpress
License: GPL2
*/

/*  Copyright 2012  Scott Fennell  (email : scofennell@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/















function sjf_et_enlarge_text() {
?>
	<script>
		function sjf_et_set_cookie(name,value,days) {
    		if (days) {
        		var date = new Date();
        		date.setTime(date.getTime()+(days*24*60*60*1000));
        		var expires = "; expires="+date.toGMTString();
    		} else {
    			var expires = "";
    		}
		    document.cookie = name+"="+value+expires+"; path=/";
		}

		function sjf_et_get_cookie(name) {
    		var name_eq = name + "=";
    		var ca = document.cookie.split(';');
    		for(var i=0;i < ca.length;i++) {
    		    var c = ca[i];
    		    while (c.charAt(0)==' ') c = c.substring(1,c.length);
        		if (c.indexOf(name_eq) == 0) return c.substring(name_eq.length,c.length);
    		}
    		return null;
		}

		if(sjf_et_get_cookie("page_size") != null){   
    		jQuery('body').css('fontSize', sjf_et_get_cookie("page_size") + 'em');
		} else {
			jQuery('body').css('fontSize', '1em');
		}
	</script>
<?php
}
add_action('wp_footer', 'sjf_et_enlarge_text');













//let's use jQuery
function sjf_et_called_it() {
	if( !is_admin()){
		wp_register_script('jquery', '/wp-includes/js/jquery/jquery.js');
		wp_enqueue_script('jquery');
		wp_deregister_script('cookie');
		wp_register_script('cookie', plugins_url( '/cookie.js', __FILE__), 'jQuery');
		wp_enqueue_script('cookie');
	}
}
add_action('wp_enqueue_scripts', 'sjf_et_called_it');













function sjf_et_enlarge_text_links($atts){ 
	extract( shortcode_atts( array(
		'small' => 'M',
		'medium' => 'L',
		'large' => 'X',
		'default_value' => 'small'
	), $atts ) );

	$default_size='1';
	if($default_value== 'medium'){$default_size = '1.2';}
	if($default_value== 'large'){$default_size = '1.4';}
?>
	<div id="textsize_wrapper">
		<span class="sjf_et_title">Text Size:</span>	
		<a class="changer" id="make_small" href="#" onclick="javascript:body.style.fontSize='1em'; sjf_et_set_cookie('page_size', '1', 30);"><?php echo $small; ?></a>
		<a class="changer" id="make_medium" href="#" onclick="javascript:body.style.fontSize='1.2em'; sjf_et_set_cookie('page_size', '1.2', 30);"><?php echo $medium; ?></a>
		<a class="changer" id="make_large" href="#" onclick="javascript:body.style.fontSize='1.4em'; sjf_et_set_cookie('page_size', '1.4', 30);"><?php echo $large; ?></a>
	</div>			

	<script>
		if(jQuery.cookie("page_size") != null){
			if(jQuery.cookie('page_size')==1){
				jQuery('#make_small').addClass('sjf_et_active active');
			}
			if(jQuery.cookie('page_size')==1.2){
				jQuery('#make_medium').addClass('sjf_et_active active');
			}
			if(jQuery.cookie('page_size')==1.4){
				jQuery('#make_large').addClass('sjf_et_active active');
			}
		} else {
			jQuery('#make_<?php echo esc_attr($default_value); ?>').addClass('sjf_et_active active');
			jQuery('body').css('fontSize', '<?php echo $default_size; ?>em');
		}

		jQuery('.changer').click(function(){
			jQuery('.changer').removeClass('sjf_et_active active');
			jQuery(this).addClass('sjf_et_active active');
		});

</script>

<style>
	#text_size_wrapper a {float: left;}
</style>

<?php
}

add_shortcode ('enlarge_text','sjf_et_enlarge_text_links');



































//text sizer widget 
/**
 * Adds text_size_Widget widget.
 */
class Text_Size_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'text_size_widget', // Base ID
			'Text Size Widget', // Name
			array( 'description' => __( 'A text sizer widget', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		
		if(isset($instance['default_value'])){
			$default_value = strip_tags($instance['default_value']);
		} else {
			$default_value = 'small';
		}
		
		if(isset($instance['small'])){
			$small = strip_tags($instance['small']);
		} else {
			$small = 'M';
		}
		
		if(isset($instance['medium'])){
			$medium = strip_tags($instance['medium']);
		} else {
			$medium = 'L';
		}

		if(isset($instance['large'])){
			$large = strip_tags($instance['large']);
		} else {
			$large = 'X';
		}


		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		?>
		
<?php echo do_shortcode("[enlarge_text small='$small' medium='$medium' large='$large' default_value='$default_value']"); ?>
		
		<?php
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['default_value'] = strip_tags( $new_instance['default_value'] );
		$instance['small'] = strip_tags( $new_instance['small'] );
		$instance['medium'] = strip_tags( $new_instance['medium'] );
		$instance['large'] = strip_tags( $new_instance['large'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'text_domain' );
		}

		if ( isset( $instance[ 'default_value' ] ) ) {
			$default_value = $instance[ 'default_value' ];
		}
		else {
			$default_value = 'small';
		}


		if ( isset( $instance[ 'small' ] ) ) {
			$small = $instance[ 'small' ];
		}
		else {
			$small = __( 'M' );
		}
		
		if ( isset( $instance[ 'medium' ] ) ) {
			$medium = $instance[ 'medium' ];
		}
		else {
			$medium = __( 'L', 'text_domain' );
		}
		
		if ( isset( $instance[ 'large' ] ) ) {
			$large = $instance[ 'large' ];
		}
		else {
			$large = __( 'X', 'text_domain' );
		}
		?>
		
		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'default_value' )); ?>"><?php _e( 'Default Value:' ); ?></label> 
		<select id="<?php echo esc_attr($this->get_field_id( 'default_value' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'default_value' )); ?>">		
		<option <?php selected($default_value, 'small'); ?> value="small">Small</option>
		<option <?php selected($default_value, 'medium'); ?> value="medium" >Medium</option>
		<option <?php selected($default_value, 'large'); ?> value="large" >Large</option>
		</select>
		</p>


		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'small' )); ?>"><?php _e( 'Small:' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'small' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'small' )); ?>" type="text" value="<?php echo esc_attr( $small ); ?>" />
		</p>

		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'medium' )); ?>"><?php _e( 'Medium:' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'medium' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'medium' )); ?>" type="text" value="<?php echo esc_attr( $medium ); ?>" />
		</p>

		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'large' )); ?>"><?php _e( 'Large:' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'large' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'large' )); ?>" type="text" value="<?php echo esc_attr( $large ); ?>" />
		</p>

		
		<?php 
	}

} // class text_size_Widget


// register Foo_Widget widget
add_action( 'widgets_init', create_function( '', 'register_widget( "text_size_widget" );' ) );





?>