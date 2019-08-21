<?php
/**
 * Nexpress theme's functions and definitions
 *
 * @package NexpressTheme
 * @since NexpressTheme 1.0
 */
if ( ! function_exists( 'nexpresstheme_setup' ) ) {
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which runs
     * before the init hook. The init hook is too late for some features, such as indicating
     * support post thumbnails.
     */
    function nexpresstheme_setup() {
        /**
         * Enable support for post thumbnails and featured images.
         */
        add_theme_support( 'post-thumbnails' );

        /**
         * Add support for two custom navigation menus.
         */
        register_nav_menus(array(
            'NAVBAR_MENU'   => __( 'Navbar Menu', 'NexpressTheme' ),
            'FOOTER_MENU' => __('Footer Menu', 'NexpressTheme' )
        ));

        /**
         * Add responsive class for images in posts.
         */
        add_filter('the_content', 'add_responsive_class');
    }
} // myfirsttheme_setup
add_action( 'after_setup_theme', 'nexpresstheme_setup' );

add_action( 'after_switch_theme', 'install_pages' );


/**
 * 
 */
function add_responsive_class($content){
    $content = mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");
    $document = new DOMDocument();
    libxml_use_internal_errors(true);
    $document->loadHTML(utf8_decode($content));

    $imgs = $document->getElementsByTagName('img');
    foreach ($imgs as $img) {
        $img->setAttribute('class','img-fluid');
    }

    $html = $document->saveHTML();
    return $html;
}

function install_pages() {
    //create a variable to specify the details of page
    $np_home = array(
            'post_name'     =>  'np_home', //slug
            'post_type'     =>  'page',  // type of post
            'post_title'    =>  'Welcome', //title of page
            'post_content'  =>  'This is the homepage content', //content of page
            'post_status'   =>  'publish' , //status of page - publish or draft
    );
    $np_categories = array(
            'post_name'     =>  'np_categories', //slug
            'post_type'     =>  'page',  // type of post
            'post_title'    =>  'Topics', //title of page
            'post_content'  =>  'This is the topics page content', //content of page
            'post_status'   =>  'publish' , //status of page - publish or draft
    );
    $np_posts = array(
            'post_name'     =>  'np_posts', //slug
            'post_type'     =>  'page',  // type of post
            'post_title'    =>  'Posts', //title of page
            'post_content'  =>  'This is the posts page content', //content of page
            'post_status'   =>  'publish' , //status of page - publish or draft
    );
    $np_home_id = wp_insert_post( $np_home );
    $np_categories_id = wp_insert_post( $np_categories );
    $np_posts_id = wp_insert_post( $np_posts );

    Generate_Featured_Image( get_template_directory_uri().'/img/home.jpg', $np_home_id );
    Generate_Featured_Image( get_template_directory_uri().'/img/categories.jpg', $np_categories_id );
    Generate_Featured_Image( get_template_directory_uri().'/img/posts.jpg', $np_posts_id );
}


function Generate_Featured_Image( $image_url, $post_id  ){
    $upload_dir = wp_upload_dir();
    $image_data = file_get_contents($image_url);
    $filename = basename($image_url);
    if(wp_mkdir_p($upload_dir['path']))
      $file = $upload_dir['path'] . '/' . $filename;
    else
      $file = $upload_dir['basedir'] . '/' . $filename;
    file_put_contents($file, $image_data);

    $wp_filetype = wp_check_filetype($filename, null );
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    $res1= wp_update_attachment_metadata( $attach_id, $attach_data );
    $res2= set_post_thumbnail( $post_id, $attach_id );
}

?>