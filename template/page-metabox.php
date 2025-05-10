<?php

$post_id = isset($post->ID) ? $post->ID : '';
$mcm_meta_title = get_post_meta($post_id, 'mcm-meta-title', true);
$mcm_meta_description = get_post_meta($post_id, 'mcm-meta-description', true);
//wpnonce field
wp_nonce_field('save_my_custom_metabox_callback', 'mcm_nonce_field');

?>
<p class="meta-title-area">
    <label for="meta-title">Meta Title</label>
    <input type="text" name="mcm-meta-title" id="meta-title" value="<?php echo $mcm_meta_title; ?>" placeholder="Meta Title .." class="widefat" />
</p>

<p class="meta-description-area">
    <label for="meta-description">Meta Description</label>
    <input type="text" name="mcm-meta-description" id="meta-description" value="<?php echo $mcm_meta_description; ?>" class="widefat" placeholder="Meta Description ..." />
</p>