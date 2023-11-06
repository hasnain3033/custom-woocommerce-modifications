<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" enctype="multipart/form-data" class="upload-new-authorizatio-form">
    <input type="file" name="new-authorization-form" accept=".pdf" required>
    <input type="hidden" name="action" value="handle_new_authorization_form">
    <input type="submit" value="Upload New Authorization Form">
</form>
