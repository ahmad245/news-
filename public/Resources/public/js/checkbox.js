(function( $ ){
  $.fn.jsCheckbox = function( )
  {
    return this.each(function()
    {
      var element = $(this),
          parent = $(element).parent();
      if (!parent.hasClass('checkbox') && !parent.hasClass('switch-button')) {
        if (!$(element).hasClass('switch')) {
          $(element).wrap('<label class="checkbox"></label>');
          $(element).parent().append('<div class="container-checkbox"></div>');
        } else {
          $(element).wrap('<label class="switch-button' + ($(element).hasClass('yes-no') ? ' yes-no' : '') + '"></label>');
          $(element).parent().append('<div class="container-switch"></div>');
        }
      } else {
        element.click(function(event) {
          $(event.currentTarget).removeClass('indeterminate');
        });
      }
    });
  };
}( jQuery ));


$(document).ready(function() {
  $(':checkbox, :radio').jsCheckbox();
});