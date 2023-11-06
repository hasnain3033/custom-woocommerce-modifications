<?php
function custom_pdf_meta_box() {
    add_meta_box('pdf_url_meta_box', 'PDF URL', 'render_pdf_meta_box', 'credit-card-authoriz', 'normal', 'default');
}

function render_pdf_meta_box($post) {
    $pdf_url = get_post_meta($post->ID, 'pdf_url', true);
    ?>
    <label for="pdf_url_field">PDF URL:</label>
    <input type="text" id="pdf_url_field" name="pdf_url_field" value="<?php echo esc_url($pdf_url); ?>" size="50">
    <?php
}

add_action('add_meta_boxes', 'custom_pdf_meta_box');

function save_pdf_url_meta_data($post_id) {
    if (isset($_POST['pdf_url_field'])) {
        update_post_meta($post_id, 'pdf_url', esc_url($_POST['pdf_url_field']));
    }
}

add_action('save_post', 'save_pdf_url_meta_data');



// Handle form submission for uploading a new authorization form
function handle_new_authorization_form() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['new-authorization-form'])) {
        $file = $_FILES['new-authorization-form'];

        // Check if it's a PDF file
        $file_type = wp_check_filetype(basename($file['name']));
        if ($file_type['ext'] === 'pdf') {
            $user_id = get_current_user_id();

            // Get the user's first name, last name, or username
            $user_data = get_userdata($user_id);
            $user_first_name = !empty($user_data->first_name) ? $user_data->first_name : $user_data->user_login;

            // Create a timestamp
            $current_timestamp = current_time('timestamp');

            // Generate a unique post title
            $post_title = 'Authorization Form - ' . $user_first_name . ' - ' . date('Ymd-His', $current_timestamp);

            // Set post data
            $post_data = array(
                'post_title' => $post_title,
                'post_status' => 'publish',
                'post_author' => $user_id,
                'post_type' => 'credit-card-authoriz',
            );

            // Insert the post
            $post_id = wp_insert_post($post_data);

            // Upload the file to the post
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            $attachment_id = media_handle_upload('new-authorization-form', $post_id);

            if (is_wp_error($attachment_id)) {
                // Handle upload error
                wp_safe_redirect(wc_get_account_endpoint_url('upload-authorization-form') . '?error=1');
                exit;
            } else {
                // File uploaded successfully, you can save additional information if needed
                // Retrieve the attachment's URL and store it in post meta if necessary
                $file_url = wp_get_attachment_url($attachment_id);
                update_post_meta($post_id, 'pdf_url', $file_url);
                $pdf_url = get_post_meta($post_id, 'pdf_url', true);



                wp_safe_redirect(wc_get_account_endpoint_url('authorization-forms') . '?success=1&pdf-url=' . $pdf_url);
                exit;
            }
        } else {
            // File is not a PDF, handle the error
            wp_safe_redirect(wc_get_account_endpoint_url('upload-authorization-form') . '?error=2');
            exit;
        }
    }
}

add_action('admin_post_handle_new_authorization_form', 'handle_new_authorization_form');
add_action('admin_post_nopriv_handle_new_authorization_form', 'handle_new_authorization_form');
