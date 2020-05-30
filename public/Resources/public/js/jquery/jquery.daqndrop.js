(function($) {
    $.fn.daqndrop = function(options){
        var container = $(this),
            unique_id = (new Date).getTime(),
            settings = $.extend({
            downloadLink: false,
            initRemoveButton: false,
            allowRemove: false,
            height: false,
            showIcon: false,
            disableLabels: false,

            onDrop: function(){
                alert('item dropped');
            },
            onChoose: function() {
                alert('choose button clicked');
            },
            onRemove: function() {
                alert('remove button clicked');
            }
        }, options);

        $(document).on(
            'drag dragstart dragend dragover dragenter dragleave drop',
            function(e) {
                e.preventDefault();
                e.stopPropagation();
            }
        ).on({
            'dragenter': function(e) {
                $('.dropzone').addClass('dragging');
            },
            'dragleave': function(e) {
                var mouseX = e.originalEvent.clientX, mouseY = e.originalEvent.clientY,
                    boxW = $(this).width(), boxH = $(this).height();

                if(mouseX < 0 || mouseX > boxW
                    || mouseY < 0 || mouseY > boxH) {
                    $('.dropzone').removeClass('dragging');
                }
            },
            'drop': function(e) {
                $('.dropzone').removeClass('dragging');
            }
        });

        var multiple_block_lib = settings.downloadLink !== false;

        $(this).prepend('\
            <div class="dropzone'+(settings.height < 100 ? ' min' : '')+(settings.showIcon === true ? ' show': '')+'">\
                <div class="overlay'+(multiple_block_lib === true ? ' multiple' : '')+'">\
                    <div id="daqndrop_choose_'+unique_id+'" class="block-lib" title="Choisir le fichier">\
                        <span class="icon icon-upload'+(settings.showIcon ? ' lien': '')+'"></span>\
                '+(!settings.disableLabels ? '\
                        <div class="lib">Choisir le fichier</div>'
                : '')+'\
                    </div>\
                '+(settings.downloadLink !== false ? '\
                    <a id="daqndrop_download_'+unique_id+'" class="block-lib" href="'+settings.downloadLink+'" target="_blank" title="Télécharger le fichier">\
                        <span class="icon icon-document-download"></span>\
                '+(!settings.disableLabels ? '\
                        <div class="lib">Télécharger le fichier</div>'
                : '')+'\
                    </a>'
                : '')+'\
                </div>\
                <div class="dragging">\
                    <div class="block-lib">\
                        <span class="icon icon-drop-in"></span>\
                '+(!settings.disableLabels ? '\
                        <div class="lib">Déposer le fichier ici</div>'
                : '')+'\
                    </div>\
                </div>\
            </div>\
        ')
        .find('.dropzone').on(
            'drag dragstart dragend dragover dragenter dragleave drop',
            function(e) {
                e.preventDefault();
                e.stopPropagation();
            }
        )
        .on({
            'dragenter': function() {
                $(this).addClass('over');
            },
            'dragleave': function(e) {
                var mouseX = e.originalEvent.clientX, mouseY = e.originalEvent.clientY,
                    boxX = $(this).offset().left, boxY = $(this).offset().top,
                    boxW = $(this).width(), boxH = $(this).height();

                if(mouseX < boxX || mouseX > boxX+boxW
                || mouseY < boxY || mouseY > boxY+boxH) {
                    $(this).removeClass('over');
                }
            },
            'drop': function(e) {
                var file = e.originalEvent.dataTransfer.files[0];
                $(this).addClass('ondrop');
                settings.onDrop(file, e.originalEvent.dataTransfer.files);
                $('.dropzone').removeClass('dragging');
            }
        })
        .find('.overlay.multiple').children('.block-lib').addClass('lien');
        
        /*$(this).find('.overlay').children('.icon-cancel').click(function(e) {
            e.preventDefault();
            e.stopPropagation();
            settings.onRemove();
        });*/

        if(multiple_block_lib === true) {
            $('#daqndrop_choose_'+unique_id).click(settings.onChoose);
        } else {
            $(this).find('.dropzone').children('.overlay').click(settings.onChoose);
        }

        if(settings.height !== false) {
            $(this).height(settings.height);
        }

        $(this).addClass('daqndrop');
        $(this).data('daqndrop:settings', settings);
    
        if(settings.initRemoveButton) {
            $(this).enableDaqndropRemove(settings.onRemove);
        }
    };

    $.fn.disableDaqndropView = function() {
        var dropzone = $(this).find('.dropzone');
        dropzone.addClass('ondrop');
    };

    $.fn.resetDaqndropView = function() {
        var dropzone = $(this).find('.dropzone');
        dropzone.removeClass('over');
        dropzone.removeClass('ondrop');
        dropzone.removeClass('dragging');
    };

    $.fn.removeDaqndrop = function() {
        $(this).find('.dropzone').off();
        $(this).find('.dropzone').remove();
        $(document).off('drag dragstart dragend dragover dragenter dragleave drop');
    };

    $.fn.enableDaqndropRemove = function(onRemove) {
        if($(this).find('.icon-cancel').length == 0) {
            $(this).find('.overlay').prepend('<span class="lien icon icon-cancel" title="Supprimer le fichier"></span>');
        }
        $(this).find('.overlay').children('.icon-cancel').click(onRemove);
    };

    $.fn.uploadedDaqnDrop = function() {
        var min = false;
        if($(this).find('.dropzone').length > 0 && $(this).find('.dropzone').hasClass('min')) {
            min = true;
        }
        $(this).append('\
            <div class="uploaded_ok'+(min ? ' min' : '')+'">\
                <span class="icon icon-checkmark"></span>\
            </div>\
        ');
        
        var settings = $(this).data('daqndrop:settings');
        if(settings && settings.allowRemove) {
            $(this).enableDaqndropRemove(settings.onRemove);
        }
    };
})(jQuery);