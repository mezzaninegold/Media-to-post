<?php
/*
Plugin Name: Media to Posts
Plugin URI: http://www.mezzaninegold.com
Description: Allows bulk uploading of images to be turned into posts with featured images. ( Select post type & category before use. )
Version: 2.1
Author: Mezzanine gold
Author URI: http://mezzaninegold.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

add_action('add_attachment', 'create_post');

function create_post( $attach_ID ) {

    $attachment = get_post( $attach_ID );

    $theoriginaltitle = $attachment->post_title;
    $thetitle = str_replace("-"," ",$theoriginaltitle);
    $uploadPostType = get_option('new_media_post_type');
    $uploadCat = get_option('new_media_cat');

    $my_post_data = array(
                'post_title' => $thetitle,
                'post_type' => $uploadPostType,
                'post_category' => array( $uploadCat ),
                'post_status' => 'publish'
    );
    $post_id = wp_insert_post( $my_post_data );

    // attach media to post
    wp_update_post( array(
        'ID' => $attach_ID,
        'post_parent' => $post_id,
    ) );

    set_post_thumbnail( $post_id, $attach_ID );

    return $attach_ID;
}


function bulk_create_post(){
  add_action('add_attachment', 'create_post');
}


// create plugin settings menu
add_action('admin_menu', 'mtp_create_menu');

function mtp_create_menu() {

    //create new top-level menu
    global $my_admin_page;
    $my_admin_page = add_menu_page('MTP Plugin Settings', 'MTP', 'administrator', __FILE__, 'mtp_settings_page','dashicons-images-alt');
    add_action('load-'.$my_admin_page, 'bulk_create_post');
    //call register settings function
    add_action( 'admin_init', 'register_mysettings' );
}


function register_mysettings() {
    //register our settings
    register_setting( 'mtp-settings-group', 'new_media_cat' );
    register_setting( 'mtp-settings-group', 'new_media_post_type' );
}

function mtp_settings_page() {


?>



<div class="wrap">


<h2>Media To Posts</h2>
<p> Go to the <a href="<?php echo site_url(); ?>/wp-admin/plugins.php">plugins</a> page to deactive when not in use.</p>
<p> <a href="https://github.com/mezzaninegold/Media-to-post" target="_blank">Visit the GitHub repository for updates</a></p>

<form method="post" action="options.php">
    <?php settings_fields( 'mtp-settings-group' ); ?>
    <?php do_settings_sections( 'mtp-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">Select the post type</th>
            <td>
                <select name="new_media_post_type">
                    <option style="font-weight:bold;" value="<?php echo get_option('new_media_post_type'); ?>">
                        <?php echo get_option('new_media_post_type'); ?>
                    </option>
                    <option value="post">
                        post
                    </option>
                    <option value="page">
                        page
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
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Select the category</th>   
            <td>
                <?php 
                $args = array( 'hide_empty' => 0,'hierarchical' => 0 ); 
                $categories = get_categories($args); ?>
                <select name="new_media_cat">
                    <option style="font-weight:bold;" value="<?php echo get_option('new_media_cat'); ?>">
                        <?php $cat=get_option('new_media_cat'); $yourcat = get_category($cat);
                        if ($yourcat) { echo $yourcat->name; } ?>
                    </option>
                    <?php foreach ($categories as $category) { ?>
                        <option value="<?php echo $category->cat_ID ?>"><?php echo $category->name ?></option>
                    <? } ?>
                </select>
            </td>
        </tr>
    </table>
    
    <?php submit_button(); ?>

</form>
<?php

wp_enqueue_script('plupload-handlers');
?>

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
<?php } ?>