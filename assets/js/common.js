var curMenuItem = $("#menu").find("ul.menu_level_1 > li.current_ancestor");
var menuItems = $("#menu").find("ul.menu_level_1 > li > span").closest('li');
var curIndex = menuItems.index(curMenuItem);

$('#menu li:not(.current_ancestor):not(:last-child) .menu_parametrage').show();
$("#menu .scrollable > ul").accordion({
    header: ".menu_level_1 > li > span",
    collapsible: true,
    icons: false,
    animate: 200,
    heightStyle: "content",
    active: (curIndex >= 0 ? curIndex : false)
});
$('#menu li:not(.current_ancestor):not(:last-child) .menu_parametrage').hide();

$(function() {
    $(":radio,:checkbox:not([id^=colSelect])").jsCheckbox();
    $("select:not(.tablesorter-filter)").select2({
        minimumResultsForSearch: 6,
        dropdownAutoWidth: true
    });
    $('.input-date:not([readonly]):not(.no-init)').datetimepicker({
        timepicker: false,
        format: 'd/m/Y',
        formatDate: 'd/m/Y',
        lang: 'fr',
        dayOfWeekStart: 1,
        scrollInput: false,
        closeOnDateSelect: true
    }).attr('autocomplete', 'nope');
    $('.input-time:not([readonly])').datetimepicker({
        datepicker: false,
        format: 'H:i',
        formatDate: 'H:i',
        lang: 'fr',
        step: 15,
        minTime: "08:00",
        maxTime: "18:01",
        dayOfWeekStart: 1,
        scrollInput: false,
        closeOnDateSelect: true
    }).attr('autocomplete', 'nope');

    $("#menu .scrollable").scrollbar();

    $('#menu .menu_parametrage').prev('span').css('cursor', 'pointer').click(function () {
        var next = $(this).next('ul');
        $(next).slideToggle("slide");
    });

    if($.tablesorter) {
        tablesorter_init();
    }

    if($.fancybox) {
        fancybox = $.fancybox;
    }

    var tel_inputs = $('input.telephone');
    tel_inputs.focus(function() { $(this).val(InputHandler.reinitFormat($(this).val())); });
    tel_inputs.blur(function() { $(this).val(InputHandler.formatTelephone($(this).val())); });
    //tel_inputs.focus();
    //tel_inputs.blur();

    var cur_inputs = $('input.currency');
    cur_inputs.focus(function() { $(this).val(InputHandler.reinitFormat($(this).val())); });
    cur_inputs.blur(function() { $(this).val(InputHandler.formatNumberWithSeparators($(this).val())); });
    //cur_inputs.focus();
    //cur_inputs.blur();

    $('#displayable_content').addClass('loaded');
});

// Inputs

function checkboxAsRadio(event, parentSelector) {
    var el = $(this);
    var name = el.attr('name');

    if(!parentSelector) {
        parentSelector = 'form';
    }

    if(el.is(':checked')) {
        var parent = el.closest(parentSelector);
        parent.find('input[type=checkbox][name="'+name+'"]').not(el).prop('checked', false);
    }
}

function highlighting_element(el) {
    $(el).addClass('init_highlighting');
    $(el).addClass('highlighting');
    setTimeout(function() {
        $(el).removeClass('highlighting');
        $(el).addClass('highlighting_stop');
        setTimeout(function() {
            $(el).removeClass('highlighting_stop');
            setTimeout(function() {
                $(el).removeClass('init_highlighting');
            }, 500);
        }, 800);
    }, 400);
}

var InputHandler = {
    forceNumeric: function(event, input, min, max) {
        if(!input) {
            return;
        }

        var delayedFunction = function(){
            if(input.type == "number") {
                if(!min && input.min) {
                    min = parseInt(input.min);
                }
                if(!max && input.max) {
                    max = parseInt(input.max);
                }
                if(input.value == "") {
                    input.value = (min ? min : (input.min ? input.min : 0));
                } else {
                    if(parseInt(input.value) < (min ? min : 0)) {
                        input.value = 0;
                    }
                    if(max && parseInt(input.value) > max) {
                        input.value = max;
                    }
                }
            } else {
                var val = input.value;
                var newVal = val;
                var changed = false;
                for(var i=val.length; i > 0; i--) {
                    var curChar = val.charAt(i-1);
                    if(!new RegExp("^[0-9]$").test(curChar)) {
                        newVal = newVal.replace(curChar, '');
                        changed = true;
                    }
                }

                if(typeof(min) !== "undefined" && newVal < min) {
                    newVal = min;
                    changed = true;
                }
                if(typeof(max) !== "undefined" && newVal > max) {
                    newVal = max;
                    changed = true;
                }

                if(changed) {
                    input.value = newVal;
                }
            }

            if($(input).data('nullable') == "true" && input.value == 0) {
                input.value = "";
            }
        };
        setTimeout(delayedFunction, 1);
    },

    forceDecimal: function(event, input, min, max) {
        if(!input) {
            return;
        }

        var delayedFunction = function(){
            if(input.type == "number") {
                if(!min && input.min) {
                    min = parseFloat(input.min);
                }
                if(!max && input.max) {
                    max = parseFloat(input.max);
                }

                if(parseFloat(input.value) < (min ? min : 0)) {
                    input.value = 0;
                }
                if(max && parseFloat(input.value) > max) {
                    input.value = max;
                }
            } else {
                var val = input.value;
                var newVal = val;
                var changed = false;
                for(var i=val.length; i > 0; i--) {
                    var curChar = val.charAt(i-1);
                    if(curChar == '.' ||curChar == '-')
                        continue;
                    else if(curChar == ',') {
                        newVal = newVal.replace(',', '.');
                        changed = true;
                    } else if(!new RegExp("^[0-9]$").test(curChar)) {
                        newVal = newVal.replace(curChar, '');
                        changed = true;
                    }
                }

                if(typeof(min) !== "undefined" && newVal < min) {
                    newVal = min;
                    changed = true;
                }
                if(typeof(max) !== "undefined" && newVal > max) {
                    newVal = max;
                    changed = true;
                }

                if(changed) {
                    input.value = newVal;
                }
            }

            /*if($(input).data('nullable') && input.value == 0) {
                input.value = "";
            }*/
        };
        setTimeout(delayedFunction, 1);
    },

    reinitFormat: function(value) {
        return value != "" ? value.replace(/\s/g, '') : "";
    },

    formatTelephone: function(value) {
        if(value != "") {
            value = value.replace(/[^0-9]/g, '');
            value = value.split(' ').join('');
            value = value.match(/.{1,2}/g).join(" ");
            return value;
        } else {
            return '';
        }
    },

    formatNumberWithSeparators: function(value, toFixed) {
        if(value != '') {
            var newValue = '';
            if(value.indexOf('.') >= 0) {
                var parts    = value.split('.');
                newValue = parts[0].split(' ').join('');
                newValue = newValue.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1 ");
                
                if(parts.length > 0) {
                    newValue += "." + parts[1];
                }
            } else {
                newValue = value.split(' ').join('');
                newValue = newValue.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1 ");
            }
            
            if(toFixed) {
                newValue = parseFloat(newValue).toFixed(toFixed);
            }
            
            return newValue;
        } else {
            return '';
        }
    },

    capitalize: function(event) {
        var val = $(this).val();
        $(this).val(
            val.charAt(0).toUpperCase() + val.substr(1)
        );
    },

    upCase: function(event) {
        $(this).val( $(this).val().toUpperCase() );
    },
    
    autoNext: function(event) {
        var maxLength = parseInt($(this).attr('maxlength'));
        if(maxLength && maxLength > 0 && $(this).val().length > 0 && event.key != "ArrowLeft" && event.key != "ArrowRight") {
            if($(this).val().length >= maxLength) {
                var parent = $(this).parents('.field');
                
                if(parent.parents('div').first().is('[class^="col-"]')) {
                    parent.parents('div[class^="col-"]').first().next('div[class^="col-"]').first().find('input').focus();
                    return true;
                }
                
                if(parent.parents('div').first().hasClass('fields')) {
                    parent.next('div').first().find('input').focus();
                    return true;
                }
            }
        } else if(event.key == "Backspace" && $(this).val().length == 0) {
            var parent = $(this).parents('.field');
    
            if(parent.parents('div').is('[class^="col-"]')) {
                parent.parents('div').prev('div').first().find('input').focus();
                return true;
            }
            
            if(parent.parents('div').hasClass('fields')) {
                parent.prev('div').first().find('input').focus();
            }
        }
    },
    
    dynamicType: {
        focus: function(event) {
            if($(this).is('[readonly]')) {
                event.preventDefault();
                return;
            }
            
            var convVal = $(this).val();
            if($(this).data('initial-type') == "number") {
                convVal = $(this).val().replace(/([^0-9\.,-])/g, '');
            }
            $(this).prop('type', $(this).data('initial-type'));
            $(this).val(convVal);
        },
        
        blurNumber: function(event) {
            var oldVal = $(this).val();
            if($(this).is('[readonly]')) {
                oldVal = oldVal.replace(/([^0-9\.,-])/g, '');
            }
    
            $(this).attr('autocomplete', 'nope').prop('type', 'text');
            
            var newVal = InputHandler.formatNumberWithSeparators(oldVal, $(this).data('force-digits'));
            
            if(newVal != "" && $(this).data('append-val') && newVal.indexOf($(this).data('append-val')) < 0) {
                var el = $(this);
                setTimeout(function() {
                    el.val(newVal + el.data('append-val'));
                }, 1);
            } else {
                $(this).val(newVal);
            }
        }
    }
};

$('.toCapital').blur(InputHandler.capitalize);
$('.toUppercase').blur(InputHandler.upCase);
$('.autoNextInput').keyup(InputHandler.autoNext);
$('.dynamicNumber').each(function(ind, el) {
    $(el).data('initial-type', $(this).prop('type'));
    $(el).on('focus', InputHandler.dynamicType.focus);
    $(el).on('blur', InputHandler.dynamicType.blurNumber);
    $(el).trigger('blur');
});