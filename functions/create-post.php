<?php 


add_action('add_attachment', 'mtp_create_post');


function mtp_create_post( $attach_ID ) {


    $switchOnOff = get_option('mtp_switch');
    echo $switchOnOff;
    if ($switchOnOff == 'On') {
    $attachment = get_post( $attach_ID );

    $theoriginaltitle = $attachment->post_title;
    $thetitle = str_replace("-"," ",$theoriginaltitle);
    $uploadPostType = get_option('new_media_post_type');
    $uploadCat = get_option('new_media_cat');

    $my_post_data = array(
                'post_title' => $thetitle,
                'post_type' => $uploadPostType,
                'post_category' => $uploadCat,
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
}