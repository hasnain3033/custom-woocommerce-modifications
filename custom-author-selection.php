<?php

function custom_author_selection_field() {
    $selected_author = get_post_field('post_author', get_the_ID());
    
    // Define the role you want to filter by (e.g., 'customer')
    $user_role_to_filter = 'customer';
    
    // Get users with the specified role
    $users_with_role = get_users(array('role' => $user_role_to_filter));
    
    $dropdown_args = array(
        'name' => 'post_author',
        'selected' => $selected_author,
        'show_option_none' => __('Select an Author'),
        'include' => wp_list_pluck($users_with_role, 'ID'), // Include only user IDs with the specified role
    );

    wp_dropdown_users($dropdown_args);
}


function add_custom_author_selection_field() {
    add_meta_box('custom-author-selection', 'Select Author', 'custom_author_selection_field', 'credit-card-authoriz', 'side', 'default');
}

add_action('add_meta_boxes', 'add_custom_author_selection_field');

function save_custom_author_selection($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (isset($_POST['post_author'])) {
        update_post_meta($post_id, 'post_author', $_POST['post_author']);
    }
}

add_action('save_post', 'save_custom_author_selection');

function custom_post_type_columns($columns) {
    // Create a new array to store the modified column order
    $new_columns = array();

    // Add the 'Author' column to the beginning of the new array
    

    // Loop through the existing columns and add them to the new array
    foreach ($columns as $key => $value) {
        if ($key !== 'date') {
            $new_columns[$key] = $value;
        }
    }
    $new_columns['author'] = 'Author';
    // Add the 'Date' column to the end of the new array
    $new_columns['date'] = 'Date';

    // Return the modified column order
    return $new_columns;
}

add_filter('manage_credit-card-authoriz_posts_columns', 'custom_post_type_columns');



function custom_post_type_custom_columns($column, $post_id) {
    if ($column === 'author') {
        $author_id = get_post_field('post_author', $post_id);
        $author = get_userdata($author_id);
        echo esc_html($author->display_name);
    }
}
add_action('manage_credit-card-authoriz_posts_custom_column', 'custom_post_type_custom_columns', 10, 2);
