$(document).ready(function() {
    var current = $('nav').find('.current');
    current.parents('li.list-item').addClass('current_ancestor deplier');
    
    //var headerHeight = $('header').height();
    var headerHeight = $('.nav-wrapper').height();
    var maxHeightSousNav = $(document).height() - headerHeight;
    var widthNav = $('nav.principale').width();
    var sousNavDiv = $('#display-sous-menu');
    var liHeight = $('nav.principale > ul > li:not(.current_ancestor)').first().height();
    var firstClick = true;
    
    $('nav > ul > li:not(.current_ancestor)').on('click', function(event) {
        sousNavDiv.removeAttr('style');
        
        if (firstClick === false) {
            sousNavDiv.css({
                left: '-300px',
                transition: 'left 0.5s ease',
            });
        } else {
            sousNavDiv.css({
                left: '-300px',
                transition: 'left 0.3s ease',
            });
        }
        
        sousNavDiv.html('');
        sousNavDiv.removeAttr('data-simplebar');
        
        $('nav li').removeClass('hovered');
        $(event.currentTarget).addClass('hovered');
        
        var contenu = $(event.currentTarget).find('ul').html();
        var pos = $(event.currentTarget).position().top + headerHeight;
        console.log(pos);
        
        sousNavDiv.css({
            top: pos + 'px'
        });
        
        sousNavDiv.html(contenu);
        
        // on prend le bottom pos de la nav
        var bottomPos = sousNavDiv.height() + pos;
        var heightSousNav = sousNavDiv.height();
        
        if (heightSousNav > maxHeightSousNav) {
            heightSousNav = maxHeightSousNav;
            
            sousNavDiv.css({
                top: '60px',
                bottom: '0px',
            });
        } else if (bottomPos > maxHeightSousNav) {
            // si le bottom pos est > à la taille max de la nav on met top et bottom en auto
            
            sousNavDiv.css('top', 'auto');
            sousNavDiv.css('bottom', ($(document).height() - pos - liHeight) + 'px');
            
            if (sousNavDiv.position().top < headerHeight) {
                sousNavDiv.css('top', headerHeight +'px');
            }
        }
        
        if (firstClick === false) {
            setTimeout(function() {
                sousNavDiv.css({
                    left: widthNav + 'px',
                    visibility: 'visible',
                    height: heightSousNav + 'px',
                    maxHeight: heightSousNav + 'px',
                    transition: 'left 0.5s ease',
                });
            }, 200);
        } else {
            sousNavDiv.css({
                left: widthNav + 'px',
                visibility: 'visible',
                height: heightSousNav + 'px',
                maxHeight: heightSousNav + 'px',
                transition: 'left 0.5s ease',
            });
        }
        
        new SimpleBar($('#display-sous-menu')[0]);
        firstClick = false;
    });
    
    /**
     * pour pouvoir plier et déplier le sous menu à gauche
     */
    $('nav > ul > li > ul > li > .no-link').click(function(event) {
        if ($(event.currentTarget).parent().hasClass('deplier')) {
            $(event.currentTarget).parent().removeClass('deplier');
            $(event.currentTarget).parent().find('ul').slideToggle();
        } else {
            var deplier = $(event.currentTarget).parent().parent().find('.deplier');
            deplier.find('ul').slideToggle();
            deplier.removeClass('deplier');
            
            $(event.currentTarget).parent().addClass('deplier');
            $(event.currentTarget).parent().find('ul').slideToggle();
        }
    });
    
    
    $('body').click(function(event) {
        if ($(event.target).parents('nav').length === 0 && $(event.target).parents('#display-sous-menu').length === 0) {
            $('#display-sous-menu').css('left', '-300px');
            $('nav .hovered').removeClass('hovered');
            $('.dropdown-header').removeClass('active');
        }
    });
    
    $('#plateforme, #profil').on('click', function(event) {
        var idDropdown = $(event.currentTarget).attr('id');
        
        $('.dropdown-header').removeClass('active');
        $('#dropdown-' + idDropdown).addClass('active');
    });
    
    $('.dropdown-header').on('mouseleave', function(event) {
        var menu = $(event.currentTarget);
        setTimeout(function(elem) {
            elem.removeClass('active');
        }, 500, menu);
    });
});