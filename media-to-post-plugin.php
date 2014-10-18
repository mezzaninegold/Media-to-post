<?php
/*
Plugin Name: Media to Posts
Plugin URI: http://www.mezzaninegold.com
Description: Allows bulk uploading of images to be turned into posts with featured images. ( Select post type & category before use. )
Version: 2.1
Author: Mezzanine gold
Author URI: http://mezzaninegold.com
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


// create custom plugin settings menu
add_action('admin_menu', 'mtp_create_menu');

function mtp_create_menu() {

    //create new top-level menu
    add_menu_page('MTP Plugin Settings', 'MTP', 'administrator', __FILE__, 'mtp_settings_page','dashicons-images-alt');

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
                $args = array( 'hide_empty' => 0 ); 
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
</div>
<?php } ?>