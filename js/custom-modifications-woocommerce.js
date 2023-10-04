jQuery(document).ready(function($) {
    console.log('yeah here too')
    $(document).on('click', '.cart-shipping-expenses', function(){
        $(this).toggleClass('active')
        $('.fee').slideToggle()
    })
    $('input.qty').on('change', function() {
        // Enable the "Update Cart" button when the quantity changes
        $('button[name="update_cart"]').prop('disabled', false);
    });

    // Listen for clicks on the plus and minus buttons
    $(document).on('click', '.plus, .minus', function() {
        // Enable the "Update Cart" button when the quantity changes
        $('button[name="update_cart"]').prop('disabled', false);
    });
});


