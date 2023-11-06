<a href="<?php echo esc_url(wc_get_account_endpoint_url('upload-authorization-form')); ?>" class="button">Upload New Authorization Form</a>

<?php
$current_user = wp_get_current_user();
$args = array(
    'post_type' => 'credit-card-authoriz',
    'author' => $current_user->ID,
    'posts_per_page' => 5,
    'orderby' => 'date',
    'order' => 'DESC',
);
$forms = new WP_Query($args);

if ($forms->have_posts()) {
    echo '<table>';
    echo '<tr><th>Form Title</th><th>PDF Link</th></tr>';

    while ($forms->have_posts()) {
        $forms->the_post();

        // Get the PDF URL from post meta
        $pdf_url = get_post_meta(get_the_ID(), 'pdf_url', true);

        echo '<tr>';
        echo '<td>' . get_the_title() . '</td>';
        echo '<td><a href="' . esc_url($pdf_url) . '" target="_blank">View PDF</a></td>';
        echo '</tr>';
    }

    echo '</table>';
} else {
    echo 'No forms found for the user';
}

wp_reset_postdata();
