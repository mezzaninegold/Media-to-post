<?php
/*
Plugin Name: Media to Posts
Plugin URI: http://www.mezzaninegold.com
Description: Allows bulk uploading of images to be turned into posts with featured images. ( Select post type & category before use. )
Version: 2.5
Author: Mezzanine gold
Author URI: http://mezzaninegold.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

require_once( 'functions/category-walker.php' );
require_once( 'functions/create-post.php' );


// function bulk_create_post(){
//  add_action('add_attachment', 'create_post');
// }




// create plugin settings menu
add_action('admin_menu', 'mtp_create_menu');

function mtp_create_menu() {

    //create new top-level menu
    global $my_admin_page;
    $my_admin_page = add_menu_page('MTP Plugin Settings', 'MTP', 'administrator', __FILE__, 'mtp_settings_page','dashicons-images-alt');
    //add_action('load-'.$my_admin_page, 'bulk_create_post');
    //call register settings function
    add_action( 'admin_init', 'register_mysettings' );
}


function register_mysettings() {
    //register our settings
    register_setting( 'mtp-settings-group', 'mtp_switch' );
    register_setting( 'mtp-settings-group', 'mtp_cat' );
    register_setting( 'mtp-settings-group', 'mtp_post_type' );
}

function mtp_settings_page() {


?>



<div id="poststuff" class="wrap" style="width:50%;">


<h2>Media To Posts</h2>

<p> <a href="https://github.com/mezzaninegold/Media-to-post" target="_blank">Visit the GitHub repository for updates</a></p>



<form method="post" action="options.php">
    <?php settings_fields( 'mtp-settings-group' ); ?>
    <?php do_settings_sections( 'mtp-settings-group' ); ?>

<div class="postbox">
    <div title="Click to toggle" class="handlediv"><br></div><h3 class="hndle"><span>Turn On</span></h3>
        <div class="inside">
            <select name="mtp_switch">
                <option style="font-weight:bold;" value="<?php echo get_option('mtp_switch'); ?>">
                    <?php echo get_option('mtp_switch'); ?>
                </option>
                <option value="On">
                        On
                </option>
                <option value="Off">
                        Off
                </option>
            </select>
        </div>
    </div>   
<div class="postbox">
    <div title="Click to toggle" class="handlediv"><br></div><h3 class="hndle"><span>Post Type</span></h3>
        <div class="inside">
            <p>Select your post type</p>
                <select name="mtp_post_type">
                    <option style="font-weight:bold;" value="<?php echo get_option('mtp_post_type'); ?>">
                        <?php echo get_option('mtp_post_type'); ?>
                    </option>
                    <option value="post">
                        Post
                    </option>
                    <option value="page">
                        Page
                    </option>
                    <?php $args = array( 'public'   => true, '_builtin' => false );

                    $output = 'names'; // names or objects, note names is the default
                    $operator = 'and'; // 'and' or 'or'

                    $post_types = get_post_types( $args, $output, $operator ); 

                    foreach ( $post_types  as $post_type ) { ?>
                        <option value="<?php echo $post_type ?>">
                           <?php echo $post_type ?>
                        </option>
                    <?php } ?>
                </select>
    </div>
</div>

    <?php $selected_cats = get_option('mtp_cat');
    $walker = new Walker_Category_Checklist_Widget(
      'mtp_cat', 'in-category'
    );
    echo '<div class="postbox">';
    echo '<div title="Click to toggle" class="handlediv"><br></div><h3 class="hndle"><span>Categories</span></h3>';
    echo '<div class="inside">';
    echo '<div class="categorydiv">';
    echo '<div class="tabs-panel">';
    echo '<ul class="categorychecklist">';
    wp_category_checklist( 0, 0, $selected_cats, FALSE, $walker, FALSE);
    echo '</ul>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

                 ?>
    
    <?php submit_button(); ?>

</form>


    <?php 
    $switchOnOff = get_option('mtp_switch');
    if ($switchOnOff == 'On') { ?>
    

        <?php wp_enqueue_script('plupload-handlers'); ?>

        <?php
        $form_class = 'media-upload-form type-form validate';
        if ( get_user_setting('uploader') || isset( $_GET['browser-uploader'] ) )
        $form_class .= ' html-uploader';
        ?>
        <div class="wrap">
        <h2><?php echo esc_html( $title ); ?></h2>
        <form enctype="multipart/form-data" method="post" action="<?php echo admin_url('media-new.php'); ?>" class="<?php echo esc_attr( $form_class ); ?>" id="file-form">
        <?php media_upload_form(); ?>
        <script type="text/javascript">
        var post_id = <?php echo $post_id; ?>, shortform = 3;
        </script>
        <input type="hidden" name="post_id" id="post_id" value="<?php echo $post_id; ?>" />
        <?php wp_nonce_field('media-form'); ?>
        <div id="media-items" class="hide-if-no-js"></div>
        </form>
        </div>
        </div>
    <?php } elseif ($switchOnOff == 'Off') { } ?>
<?php } ?>