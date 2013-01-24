<?php  
/* 
    Plugin Name: Csss
    Description: Csss slideshow on any Wordpress page
    Author: Josh Kurz
    Version: 0.0.1
*/  

function csss_init() {  
    //add shorcode for cssp-shortcode
    add_shortcode('cssp-shortcode', 'csssp_function');  

    $args = array(  
        'public' => true,  
        'label' => 'Csss Pages',  
        'supports' => array(  
            'title',  
            'thumbnail',
            'editor',
            'excerpt'  
        )  
    );  
    register_post_type('csss_slides', $args);  
}  

function np_register_scripts() {  
    if (!is_admin()) {  
        // register  
        //may need to include jQuery wp_register_script('wp_csss-script', plugins_url('js/slideshow.js', __FILE__), array( 'jquery' )); 
        wp_register_script('cssp_script', plugins_url('js/slideshow.js', __FILE__));   
        wp_register_script('cssp_script_preefix', plugins_url('js/preefixfree.min.js', __FILE__));   
        wp_register_script('cssp_script_classList', plugins_url('js/classList.js', __FILE__));   

        //register plugins
        wp_register_script('cssp_plugin_highlights', plugins_url('js/plugins/code-highlights.js', __FILE__));  
        wp_register_script('cssp_plugin_controls', plugins_url('js/plugins/css-controls.js', __FILE__)); 
        wp_register_script('cssp_plugin_edit', plugins_url('js/plugins/css-edit.js', __FILE__)); 
        wp_register_script('cssp_plugin_snippets', plugins_url('js/plugins/css-snippets.js', __FILE__)); 
        wp_register_script('cssp_plugin_incrementable', plugins_url('js/plugins/incrementable.js', __FILE__)); 
  
        // enqueue  
        wp_enqueue_script('cssp_script'); 
        wp_enqueue_script('cssp_script_preefix');  
        wp_enqueue_script('cssp_script_classList');   
        wp_enqueue_script('cssp_plugin_highlights');  
        wp_enqueue_script('cssp_plugin_controls'); 
        wp_enqueue_script('cssp_plugin_edit'); 
        wp_enqueue_script('cssp_plugin_snippets'); 
        wp_enqueue_script('cssp_plugin_incrementable'); 
    }  
}  
  
function np_register_styles() {  
    // register  
    wp_register_style('cssp_styles_reusable', plugins_url('css/reusable.css', __FILE__)); 
    wp_register_style('cssp_styles_slideshow', plugins_url('css/slideshow.css', __FILE__)); 
    wp_register_style('cssp_styles_talk', plugins_url('css/talk.css', __FILE__));  
    wp_register_style('cssp_styles_theme', plugins_url('css/theme.css', __FILE__));  
  
    // enqueue  
    wp_enqueue_style('cssp_styles_reusable');  
    wp_enqueue_style('cssp_styles_slideshow'); 
    wp_enqueue_style('cssp_styles_talk');  
    wp_enqueue_style('cssp_styles_theme');   
}

function csssp_function($type='cssp_function') {  
    $args = array(  
        'post_type' => 'csss_slides',  
        'posts_per_page' => 5  
    );  
    $result = '<!DOCTYPE html>';  
    $result .= '<html>';  
    $result .= '<head>'; 
    $result .= '<meta charset="utf-8" />'; 
    $result .= '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />'; 
    $result .= '<title>Sample CSSS presentation</title>'; 
    $result .= '<link href="css/slideshow.css" rel="stylesheet" />'; 
    $result .= '<link href="css/talk.css" rel="stylesheet" />'; 
    $result .= '<script src="js/prefixfree.min.js"></script>'; 
    $result .= '</head>'; 
  
    //the loop  
    $loop = new WP_Query($args);
    $numOfPosts = $loop->found_posts;  
    while ($loop->have_posts()) { 
      $loop->the_post(); 
      $the_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $type);

      if($loop['index'] == 0){
        $result .= '<header id="intro" class="slide">'; 
        $result .= '<h1>Sample CSSS presentation</h1>'; 
        $result .= '<img title="'.get_the_title().'" src="' . $the_url[0] . '" data-thumb="' . $the_url[0] . '" alt=""/>';
        $result .= '<p class="attribution">By Lea Verou</p>'; 
        $result .= '</header>'; 
      }
      else if($loop['index'] == $numOfPosts - 1){
        $result .= '<footer class="slide">'; 
        $result .= '<h2>Thank you!</h2>'; 
        $result .= '<p>Closing remarks</p>';
        $result .= '</footer>';  
      }
      else{
          $result .= '<section>';
          $result .= '<header class="slide">';
          $result .= '<h1>Section title</h1>';
          $result .= '</header>';  
          $result .= '<section class="slide">';
          $result .= '<img title="'.get_the_title().'" src="' . $the_url[0] . '" data-thumb="' . $the_url[0] . '" alt=""/>';
          $result .= '</section>';
          $result .= '</header>'; 
          $result .= '</section>'; 

      }  
    }  
    $result .= '<script src="js/slideshow.js"></script>';  
    $result .= '<script src="js/plugins/css-edit.js"></script>';  
    $result .= '<script src="js/plugins/css-snippets.js"></script>'; 
    $result .= '<script src="js/plugins/css-controls.js"></script>';   
    $result .= '<script src="js/plugins/css-highlights.js"></script>';   
    return $result;  
} 

/*
* Initialize Widget 
*  
*
*/
function csssp_widgets_init() {  
    register_widget('csssp_Widget');  
}  

class csssp_Widget extends WP_Widget {  
  
    public function __construct() {  
        parent::__construct('csssp_Widget', 'CSSS Slideshow', array('description' => __('A CSSS Slideshow Widget', 'text_domain')));  
    }  
} 

public function form($instance) {  
    if (isset($instance['title'])) {  
        $title = $instance['title'];  
    }  
    else {  
        $title = __('CSSS Slideshow', 'text_domain');  
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
    echo cssp_function('csssp_widget');  
    echo $after_widget;  
} 

add_theme_support( 'post-thumbnails' ); 

add_action('wp_print_scripts', 'np_register_scripts');  
add_action('wp_print_styles', 'np_register_styles'); 
add_action('widgets_init', 'cssp_widgets_init'); 
add_action('init', 'csss_init'); 
?> 