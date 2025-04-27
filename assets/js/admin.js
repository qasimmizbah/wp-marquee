jQuery(document).ready(function($) {
    
    // Initialize color pickers
    $('.color-field').wpColorPicker();
    
    // Tab switching
    $('.nav-tab-wrapper a').on('click', function(e) {
        e.preventDefault();
        
        // Remove active class from all tabs
        $('.nav-tab-wrapper a').removeClass('nav-tab-active');
        $('.tab-content').removeClass('active');
        
        // Add active class to clicked tab
        $(this).addClass('nav-tab-active');
        $($(this).attr('href')).addClass('active');
    });
    
    // Show the first tab by default
    $('.nav-tab-wrapper a:first').trigger('click');
});
