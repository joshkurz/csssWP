<?php  
/* 
    Plugin Name: Csss
    Description: Csss slideshow on any Wordpress page
    Author: Josh Kurz
    Version: 0.1.2
*/ 

/*
* Extend WP_widget
*  
*
*/  
class csssp_Widget extends WP_Widget {  
  
    public function __construct() {  
        parent::__construct('csssp_Widget', 'CSSS Slideshow', array('description' => __('A CSSS Slideshow Widget', 'text_domain')));  
    }  

    public function form($instance) {  
        if (isset($instance['title'])) {  
            $title = $instance['title'];  
        }  
        else {  
            $title = __('training', 'text_domain');  
        }  
        ?>  
            <p>  
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>  
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />  
            </p>  
        <?php  
    } 

    public function update($new_instance, $old_instance) {  
        $instance = array();  
        $instance['title'] = strip_tags($new_instance['title']);  
      
        return $instance;  
    } 

    public function widget($args, $instance) {  
        extract($args);  
        // the title  
        $title = apply_filters('widget_title', $instance['title']);  
        echo $before_widget;  
        if (!empty($title))  
            echo $before_title . $title . $after_title;  
        echo csssp_function('csssp_widget');  
        echo $after_widget;  
    } 
} 

function np_register_scripts() {  
    if (!is_admin()) {  
        // register  
        wp_register_script('csssp-jQuery', plugins_url('js/jQuery.js', __FILE__), array( 'jquery' )); 
        wp_register_script('csssp_script', plugins_url('js/slideshow.js', __FILE__));   
        //wp_register_script('csssp_script_preefix', plugins_url('js/preefixfree.min.js', __FILE__));   
        wp_register_script('csssp_script_classList', plugins_url('js/classList.js', __FILE__));   

        //register plugins
        wp_register_script('csssp_plugin_highlights', plugins_url('js/plugins/code-highlights.js', __FILE__));  
        wp_register_script('csssp_plugin_controls', plugins_url('js/plugins/css-controls.js', __FILE__)); 
        wp_register_script('csssp_plugin_edit', plugins_url('js/plugins/css-edit.js', __FILE__)); 
        wp_register_script('csssp_plugin_snippets', plugins_url('js/plugins/css-snippets.js', __FILE__)); 
        wp_register_script('csssp_plugin_incrementable', plugins_url('js/plugins/incrementable.js', __FILE__)); 
        wp_register_script('csssp_script_angular', plugins_url('js/plugins/angular.js', __FILE__));  
  
        // enqueue  
        wp_enqueue_script('csssp_jQuery'); 
        wp_enqueue_script('csssp_script'); 
        wp_enqueue_script('csssp_script_classList');   
        wp_enqueue_script('csssp_plugin_highlights');  
        wp_enqueue_script('csssp_plugin_controls'); 
        wp_enqueue_script('csssp_plugin_edit'); 
        wp_enqueue_script('csssp_plugin_snippets'); 
        wp_enqueue_script('csssp_plugin_incrementable'); 
        wp_enqueue_script('csssp_script_angular'); 
    }  
}  
  
function np_register_styles() {  
    // register  
    wp_register_style('csssp_styles_reusable', plugins_url('css/reusable.css', __FILE__)); 
    wp_register_style('csssp_styles_slideshow', plugins_url('css/slideshow.css', __FILE__)); 
    wp_register_style('csssp_styles_talk', plugins_url('css/talk.css', __FILE__));  
    wp_register_style('csssp_styles_theme', plugins_url('css/theme.css', __FILE__));  
  
    // enqueue  
    wp_enqueue_style('csssp_styles_reusable');  
    wp_enqueue_style('csssp_styles_slideshow'); 
    wp_enqueue_style('csssp_styles_talk');  
    wp_enqueue_style('csssp_styles_theme');   
} 

function csssp_widgets_init() {  
    register_widget('csssp_Widget');  
} 

function csssp_init() {  

    //add shorcode for csssp-shortcode
    add_shortcode('csssp-shortcode', 'csssp_function');  

    $args = array(  
        'public' => true,  
        'label' => 'Csss Pages',  
        'supports' => array(  
            'title',  
            'thumbnail',
            'editor',
            'excerpt',
            'ID'  
        )  
    );  
    register_post_type('csssp_slides', $args);  
} 

function csssp_function($atts) { 
    
    //extract the shortcode params
    extract(shortcode_atts(array(
        'id' => ''
    ), $atts));
    //the post  
    $post = get_post($id);
    $result = '<div id="csssSlider">'; 
    $result .= $post->post_content; 
    $result .= '<script>setTimeout( function(){ var slideshow = new SlideShow(); },400)</script>';  
    $result .= '</div>';
    return $result;  
} 

function modify_post_table( $column ) {
    $column['id'] = 'ID';
 
    return $column;
}

function pA_manage_posts_custom_column($column_name, $id) {

    switch ($column_name) {
    case 'id':
        echo $id;
            break;
    } // end switch
}

//hooks
add_theme_support( 'post-thumbnails' ); 
add_action('init', 'csssp_init');
//add_action('widgets_init', 'csssp_widgets_init');  
add_action('wp_print_scripts', 'np_register_scripts');  
add_action('wp_print_styles', 'np_register_styles'); 
add_filter( 'manage_posts_columns', 'modify_post_table' );
add_action('manage_posts_custom_column', 'pA_manage_posts_custom_column', 10, 2);
?>