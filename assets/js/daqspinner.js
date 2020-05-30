(function($) {
    $.fn.appendSpinner = function() {
        if($(this).find('.spinner_container').length == 0) {
            $(this).append('\
                <div class="spinner_container"><div class="spinner"></div></div>\
            ');
        }
    };
    $.fn.appendMiniSpinner = function() {
        if($(this).find('.spinner_container').length == 0) {
            $(this).append('\
                <div class="spinner_container min"><div class="spinner"></div></div>\
            ');
        }
    };

    $.fn.removeSpinner = function() {
        $(this).find('.spinner_container').remove();
    };
})(jQuery);