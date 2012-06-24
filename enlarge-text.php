<?php
/*
Plugin Name: Enlarge Text
Plugin URI: http://wordpress.org/extend/plugins/enlarge-text/
Description: Give your users a widget to enlarge the text on your site.  Is there some text that you don't want to enlarge?  Just declare a size for it in pixels in your stylesheet and this plugin will not affect it.
Author: Scott Fennell
Version: 1.0
Author URI: www.scottfennell.com/wordpress
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












add_action(wp_head, enlarge_text);


function enlarge_text() {?>
	<script type="text/javascript">
function set_cookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function get_cookie(name) {
    var name_eq = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(name_eq) == 0) return c.substring(name_eq.length,c.length);
    }
    return null;
}


if(get_cookie("page_size") != null){   
    document.write('<style>');
    document.write('body{');
    document.write('font-size:'+ get_cookie("page_size") + 'em');
    document.write('}');
    document.write('</style>')
}else{
    document.write('<style>');
    document.write('body{');
    document.write('font-size: 1em');
    document.write('}');
    document.write('</style>')   
}
	</script>
	<?php
}







//let's use jQuery
function ill_called_it() {
	if( !is_admin()){
		wp_deregister_script('jquery');
		wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"), false, '1.7.1');
		wp_enqueue_script('jquery');
		wp_deregister_script('cookie');
		wp_register_script('cookie', plugins_url( '/cookie.js', __FILE__), 'jQuery');
		wp_enqueue_script('cookie');
	}

}

add_action('wp_enqueue_scripts', 'ill_called_it');













function enlarge_text_links(){ ?>

<div id="textsize_wrapper">
<span>Text Size:</span>	
	<a class="changer" id="make_small" href="#" onclick="javascript:body.style.fontSize='1em'; set_cookie('page_size', '1', 30);">M</a>
	<a class="changer" id="make_medium" href="#" onclick="javascript:body.style.fontSize='1.2em'; set_cookie('page_size', '1.2', 30);">L</a>
	<a class="changer" id="make_large" href="#" onclick="javascript:body.style.fontSize='1.4em'; set_cookie('page_size', '1.4', 30);">X</a>
</div>			

<script type="text/javascript">

if(jQuery.cookie("page_size") != null){
if(jQuery.cookie('page_size')==1){
jQuery('#make_small').addClass('active');
}
if(jQuery.cookie('page_size')==1.2){
jQuery('#make_medium').addClass('active');
}
if(jQuery.cookie('page_size')==1.4){
jQuery('#make_large').addClass('active');
}
}

jQuery('.changer').click(function(){
	jQuery('.changer').removeClass('active');
	jQuery(this).addClass('active');
});

</script>

<style>

#text_size_wrapper a {float: left;}

</style>

<?php
}

add_shortcode ('enlarge_text','enlarge_text_links');



































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

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		?>
		
<?php echo do_shortcode('[enlarge_text]'); ?>
		
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
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}

} // class text_size_Widget


// register Foo_Widget widget
add_action( 'widgets_init', create_function( '', 'register_widget( "text_size_widget" );' ) );





?>