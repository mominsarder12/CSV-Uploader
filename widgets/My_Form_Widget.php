<?php
class My_Form_Widget extends Wp_Widget {
    //contractor
    public function __construct() {
        parent::__construct(
            'my_form_widget',
            'My Form Widget',
            array(
                'description' => 'This is a custom form widget',
            ),

        );
    }

    //display the widget in admin
    public function form($instance) {
        $mcw_title = !empty($instance['mcw_title']) ? $instance['mcw_title'] : "";
        $mcw_display_options = !empty($instance['mcw_display_options']) ? $instance['mcw_display_options'] : "";
        $mcw_number_of_post = !empty($instance['mcw_number_of_post']) ? $instance['mcw_number_of_post'] : "";
        $mcw_message_field = !empty($instance['mcw_message_field']) ? $instance['mcw_message_field'] : "";
?>
        <p>
            <label for="<?php echo $this->get_field_name('mcw_title'); ?>">Title</label>
            <input type="text" name="<?php echo $this->get_field_name('mcw_title'); ?>" id="<?php echo $this->get_field_id('mcw_title'); ?>" class="widefat" value="<?php echo $mcw_title; ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_name('mcw_display_options'); ?>">Display Type</label>
            <select name="<?php echo $this->get_field_name('mcw_display_options'); ?>" id="<?php echo $this->get_field_id('mcw_display_options'); ?>" class="widefat mcw_display_type_options">
                <option value="recent-posts" <?php if ($mcw_display_options == 'recent-posts') echo 'selected'; ?>>Recent posts</option>
                <option value="static-message" <?php if ($mcw_display_options == 'static-message') echo 'selected'; ?>>Static Message</option>
            </select>
        </p>

        <p id="mcw-recent-posts" <?php if ($mcw_display_options == 'recent-posts') {
                                    } else {
                                        echo 'class="mcw-hide-item"';
                                    } ?>>
            <label for="<?php echo $this->get_field_name('mcw_number_of_post'); ?>">Number of posts</label>
            <input type="number" name="<?php echo $this->get_field_name('mcw_number_of_post'); ?>" id="<?php echo $this->get_field_id('mcw_number_of_post'); ?>" class="widefat" value="<?php echo $mcw_number_of_post; ?>">
        </p>
        <p id="mcw-static-message" <?php if ($mcw_display_options == 'static-message') {
                                    } else {
                                        echo 'class="mcw-hide-item"';
                                    } ?>>
            <label for="<?php echo $this->get_field_name('mcw_message_field'); ?>">Your Message</label>
            <input type="text" name="<?php echo $this->get_field_name('mcw_message_field'); ?>" id="<?php echo $this->get_field_id('mcw_message_field'); ?>" class="widefat" value="<?php echo $mcw_message_field; ?>">
        </p>
<?php
    }


    //to save the widget in wordpress
    public function update($new_instance, $old_instance) {
        $instance = []; //mcw_title,mcw_display_options, mcw_number_of_post, mcw_message_field
        $instance['mcw_title'] = !empty($new_instance['mcw_title']) ? strip_tags($new_instance['mcw_title']) : "";

        $instance['mcw_display_options'] = !empty($new_instance['mcw_display_options']) ? sanitize_text_field($new_instance['mcw_display_options']) : "";

        $instance['mcw_number_of_post'] = !empty($new_instance['mcw_number_of_post']) ? sanitize_text_field($new_instance['mcw_number_of_post']) : "";

        $instance['mcw_message_field'] = !empty($new_instance['mcw_message_field']) ? sanitize_text_field($new_instance['mcw_message_field']) : "";
        return $instance;
    }
    //display the widget in frontend
    // Display the widget in frontend
public function widget($args, $instance) {
    $mcw_title = !empty($instance['mcw_title']) ? $instance['mcw_title'] : '';
    $mcw_display_options = !empty($instance['mcw_display_options']) ? $instance['mcw_display_options'] : 'recent-posts';
    $mcw_number_of_post = !empty($instance['mcw_number_of_post']) ? (int) $instance['mcw_number_of_post'] : 5;
    $mcw_message_field = !empty($instance['mcw_message_field']) ? $instance['mcw_message_field'] : '';

    // Only filter the widget *title*, not other fields
    $mcw_title = apply_filters('widget_title', $mcw_title);

    echo $args['before_widget'];

    // Display the title if set
    if (!empty($mcw_title)) {
        echo $args['before_title'] . $mcw_title . $args['after_title'];
    }

    if ($mcw_display_options === 'static-message') {
        echo '<p>' . esc_html($mcw_message_field) . '</p>';

    } elseif ($mcw_display_options === 'recent-posts') {
        $mcw_query = new WP_Query(array(
            'post_type'      => 'post',
            'posts_per_page' => $mcw_number_of_post,
        ));

        if ($mcw_query->have_posts()) {
            echo "<ul>";
            while ($mcw_query->have_posts()) {
                $mcw_query->the_post();
                echo '<li><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></li>';
            }
            echo "</ul>";
        } else {
            echo '<p>No posts found.</p>';
        }
        wp_reset_postdata();
    }

    echo $args['after_widget'];
}

    // the widget function close here
}
