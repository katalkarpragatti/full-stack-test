$(document).ready(function(){
    
    // Config function to dynamically spin up Slick Sliders for desktop
    function initDesktopSliders(tabId) {
        var textSlider = $('#text-slider-' + tabId);
        var imageSlider = $('#image-slider-' + tabId);

        // Destroy instance if previously initialized to avoid state bugs
        if (textSlider.hasClass('slick-initialized')) {
            textSlider.slick('unslick');
        }
        if (imageSlider.hasClass('slick-initialized')) {
            imageSlider.slick('unslick');
        }

        // Initialize Text Slide (Column 2)
        textSlider.slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            dots: true,
            asNavFor: '#image-slider-' + tabId,
            fade: true,
            infinite: true
        });

        // Initialize Image Slide (Column 3)
        imageSlider.slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            dots: false,
            asNavFor: '#text-slider-' + tabId,
            infinite: true
        });
    }

    // Config function to spin up sliders for mobile
    function initMobileSliders(tabId) {
        var mobSlider = $('#mobile-slider-' + tabId);
        if (mobSlider.hasClass('slick-initialized')) {
            mobSlider.slick('unslick');
        }
        mobSlider.slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            dots: true,
            fade: true,
            infinite: true
        });
    }

    // Initialize the default layout views (Active Tab = 1st)
    var initialTabId = $('.tab-nav-item.active').data('tab-id');
    if (initialTabId) {
        initDesktopSliders(initialTabId);
    }

    // Initialize default mobile view (1st opened accordion)
    var initialMobileCollapse = $('.accordion-collapse.show');
    if(initialMobileCollapse.length) {
        var initialMobTabId = initialMobileCollapse.attr('id').split('-')[1];
        initMobileSliders(initialMobTabId);
    }

    // Web Tab Click Behavior
    $('.tab-nav-item').on('click', function(e) {
        e.preventDefault();
        if ($(this).hasClass('active')) return;

        $('.tab-nav-item').removeClass('active');
        $(this).addClass('active');

        var targetTabId = $(this).data('tab-id');

        // Toggle Visibility
        $('.content-slider').hide();
        $('.image-slider').hide();
        $('#text-slider-' + targetTabId).show();
        $('#image-slider-' + targetTabId).show();

        // Re-initialize synchronization state
        initDesktopSliders(targetTabId);
    });

    // Mobile Accordion Toggle Event Hooks
    $('.accordion-collapse').on('shown.bs.collapse', function () {
        var activeTabId = $(this).attr('id').split('-')[1];
        initMobileSliders(activeTabId);
        
        // Update Indicator icon text to "-"
        $(this).parent().find('.accordion-indicator').html('<img src="files/images/minus-01.svg" alt="Minus">');
    });

    $('.accordion-collapse').on('hidden.bs.collapse', function () {
        // Update Indicator icon text to "+"
        $(this).parent().find('.accordion-indicator').html('<img src="files/images/plus-01.svg" alt="Plus">');
    });
});