var onHoverColumnSelector = false;

function tablesorter_init(container) {
    var t = -1;

    if(container === undefined) {
        container = 'body';
    }

    $(container).find('.tablesorter:not(.no-column-selector)').each(function() {
            t++;
        var colspan = $(this).children('thead').find('th').length;
        var columnSelector = '\
            <div class="columnSelectorWrapper">\
                <input id="colSelect'+t+'" type="checkbox" class="hidden" style="display:none;">\
                <label class="columnSelectorButton" for="colSelect'+t+'">\
                    <span id="colSelectIcon'+t+'" class="show-selector lien icon icon-triangle-down"></span>\
                </label>\
                <div id="columnSelector_'+t+'" class="columnSelector"></div>\
            </div>\
        ';
        $(this).parent().prepend(columnSelector);
        $(this).attr('data-id', t);
        $(container).find('#colSelect' + t).click(function () {
            $(container).find('#columnSelector_'+t).animate({height: 'toggle'});
        });
    });

    $(container).find('.tablesorter:not(.no-tfoot)').each(function() {
        if($(this).find('tfoot').length == 0) {
            var colspan = 0;
            $(this).find('thead').find('tr').each(function(trInd, trEl) {
              var trColspan = 0;
              $(trEl).children('th').each(function(thInd, thEl) {
                trColspan += parseInt($(this).attr('colspan') !== undefined ? $(this).attr('colspan') : 1);
              });
              if(trColspan > colspan) {
                colspan = trColspan;
              }
            });

            var tfoot = '\
                <tfoot><tr><td colspan="'+colspan+'"></td></tr></tfoot>\
            ';
            $(this).append(tfoot);
        }
    });

    $.each($(container).find('.tablesorter'), function(ind, el) {
        var data_id = $(this).attr('data-id');

        var widgets = ['saveSort', 'columnSelector', 'output'];
        var widgetOptions = {
            // ColumnSelector
            columnSelector_container : $(container).find('#columnSelector_'+data_id),
            columnSelector_saveColumns: true,
            columnSelector_layout : '\
                    <div class="columnSelectorCell">\
                        <input id="{name}" type="checkbox">\
                        <label for="{name}" class="lib">{name}</label>\
                    </div>\
                ',
            columnSelector_name  : 'data-selector-name',
            columnSelector_mediaquery: false,
            columnSelector_mediaqueryState: true,
            columnSelector_mediaqueryHidden: true,
            columnSelector_maxVisible: null,
            columnSelector_minVisible: null,
            columnSelector_breakpoints : [ '20em', '30em', '40em', '50em', '60em', '70em' ],
            columnSelector_priority : 'data-priority',
            columnSelector_cssChecked : 'checked',

            // Output
            output_separator    : 'array',     // ',' 'json', 'array' or separator (e.g. ',')
            output_delivery     : 'd',         // (p)opup, (d)ownload
            output_saveRows     : 'f',         // (a)ll, (f)iltered or (v)isible
            output_replaceQuote : '\u201c;',   // change quote to left double quote
            output_trimSpaces   : false,       // remove extra white-space characters from beginning & end
            output_wrapQuotes   : false,       // wrap every cell output in quotes
            output_dataAttrib   : 'data-text',
            output_includeFooter : ($(el).data('export-footer')),
            output_callback     : function(c, data) {
                var headers = [],
                    content_style = [],
                    excluded_cols = [];
                
                $.each(c.headerList, function(ind, header) {
                    if($(header).hasClass('output-false')) {
                        excluded_cols[headers.length] = true;
                    } else if($(header).is(':visible')) {
                        headers.push({
                            "text":     $(header).text(),
                            "config":   $(header).data('output'),
                            "colspan":  $(header).prop('colspan'),
                            "rowspan":  $(header).prop('rowspan'),
                            "row":      $(header).closest('tr').index()
                        });
                        content_style.push({
                            "alignment": {
                                'horizontal': $(header).data('align') !== undefined
                                    ? $(header).data('align')
                                    : "left"
                            }
                        });
                    }
                });

                var jsonData = JSON.parse(data).slice(1);
                var output = {
                    "route":      currentRouteName,
                    "mode":       currentPageMode,
                    "headers":    headers,
                    "content":    jsonData,
                    "content_style": content_style
                };

                $.each(c.$tbodies[0].rows, function(index, row) {
                    for(var i = 0; i < jsonData[index].length; i++) {
                        if(excluded_cols[i]) {
                            jsonData[index][i] = '###DELETED###';
                            console.log("delete");
                        } else if($(row).hasClass('txt_error')) {
                            jsonData[index][i] = '[color=red]'+jsonData[index][i];
                        }
                    }
    
                    if(excluded_cols.length > 0) {
                        jsonData[index] = jsonData[index].filter(function(val) { return val !== '###DELETED###' ? true : false; });
                    }
                });

                if($(c.table).data('export-title') != null) {
                    output.title = $(c.table).data('export-title');
                }

                var form = $('<form>', {
                    action: Routing.generate('export_xls'),
                    method: 'POST',
                    styles : {display:'none'}
                });
                var chHidden = $('<input>', {
                    'type':'hidden',
                    'value':JSON.stringify(output),
                    'name':'data'
                });

                form.append(chHidden);
                $(document.body).append(form);

                form.submit();

                return false;
            }
        };

        if(!$(el).hasClass('not-resizable')) {
            widgets.push('resizable');
            $.extend(widgetOptions, {
                // Resizable
                resizable_widths: (function() {
                    var res = [];
                    $(el).find('thead tr:not(.column-selector) th').map(function(){
                        res.push($(this).css('width'));
                    });
                    return res;
                })()
            });
        }

        if($(el).hasClass('with-filters')) {
            widgets.push('zebra');
            widgets.push('filter');

            $.extend(widgetOptions, {
                // Filters
                filter_liveSearch: true,
                filter_saveFilters: true,
                filter_searchDelay: 150,
                filter_searchFiltered: true,
                filter_startsWith: true
            });
        }

        //if(!$(el).hasClass("with-filters")) {
            $(el).on("tablesorter-initialized", function(e, tbl) {
                tbl = $(tbl);
                var footer = tbl.find('tfoot'),
                    footerTextSingle = $(this).data('footer-text-single'),
                    footerTextMulti = $(this).data('footer-text-multi'),
                    nbRows = tbl.find('tbody').find('tr').length;

                if(footerTextSingle || footerTextMulti) {
                    if(nbRows > 1) {
                        footer.find('td').text(footerTextMulti.replace(/##nb##/g, nbRows).replace(/##nb_total##/g, nbRows));
                    } else {
                        if(tbl.find('td.no-entity').length == 0) {
                            var footerText = footerTextSingle ? footerTextSingle : footerTextMulti;
                            footer.find('td').text(footerText.replace(/##nb##/g, nbRows).replace(/##nb_total##/g, nbRows));
                        } else {
                            footer.find('td').text("");
                        }
                    }
                }
                
                if($(this).hasClass('with-filters')) {
                    if($(this).find('thead').find('th.right-border').length > 0) {
                        $(this).find('thead').find('th.right-border').each(function(trInd, trEl) {
                            $(trEl).closest('thead').find('tr[role=search]').find('td[data-column="'+$(trEl).data('column')+'"]').addClass('right-border');
                        });
                    }
                }
            });
        //}

        if($(el).hasClass("with-sticky-headers")) {
            widgets.push("stickyHeaders");
            $.extend(widgetOptions, {
                // Filters
                stickyHeaders_attachTo: '#displayable_content',
                stickyHeaders_zIndex : 20000
            });
        }
        
        if($(el).hasClass("with-static-row")) {
            widgets.push("staticRow");
        }
    
        if($(el).hasClass("with-total-row")) {
            widgets.push("math");
            $.extend(widgetOptions, {
                math_data     : 'math',
                math_mask     : '# ##0.00',
                math_suffix   : $(el).data('math-suffix') ? $(el).data('math-suffix') : '',
                math_complete : function($cell, wo, result) {
                    if(result <= 0 && !$(el).hasClass("math-allow-zero")) {
                        return '';
                    }
                    
                    return result;
                }
            });
        }
  
        if(!$(el).hasClass("no-alignChar")) {
            widgets.push('alignChar');
            $.extend(widgetOptions, {
              alignChar_wrap         : '<i/>',
              alignChar_charAttrib   : 'data-align-char',
              alignChar_indexAttrib  : 'data-align-index',
              alignChar_adjustAttrib : 'data-align-adjust' // percentage width adjustments
            });
        }

        try {
            $(el).tablesorter({
                widgets: widgets,
                widgetOptions: widgetOptions
            });

            if($(el).hasClass("with-filters")) {
                $(el).on('filterEnd', function(event, ts) {
                    var tbl = $(ts.table);

                    tbl[0].config.widgetOptions.resizable_widths = (function() {
                        var res = [];
                        $(el).find('thead tr:not(.column-selector) th').map(function(){
                            if($(this).attr('colspan') > 1) {
                                var colSpan = $(this).attr('colspan'),
                                    colWidth = Math.round($(this).outerWidth(false) / colSpan);

                                for(var i=0; i < colSpan; i++) {
                                    res.push(colWidth);
                                }
                            } else {
                                res.push($(this).outerWidth(false));
                            }
                        });
                        return res;
                    })();
                  
                    var footer = tbl.find('tfoot'),
                        footerTextSingle = $(this).data('footer-text-single'),
                        footerTextMulti = $(this).data('footer-text-multi');

                    if(footerTextSingle || footerTextMulti) {
                        if(ts.filteredRows > 1) {
                            footer.find('td').text(footerTextMulti.replace(/##nb##/g, ts.filteredRows).replace(/##nb_total##/g, ts.totalRows));
                        } else {
                            if(tbl.find('td.no-entity').length == 0) {
                                var footerText = footerTextSingle ? footerTextSingle : footerTextMulti;
                                footer.find('td').text(footerText.replace(/##nb##/g, ts.filteredRows).replace(/##nb_total##/g, ts.totalRows));
                            } else {
                                footer.find('td').text("");
                            }
                        }
                    }

                    tbl.trigger('removeWidget', 'resizable', false);
                    tbl.prev('.tablesorter-resizable-container').remove();
                    tbl.trigger('applyWidgetId', 'resizable');
                    tbl.trigger('applyWidgets');
                }).find('.tablesorter-filter-row select').select2({
                    minimumResultsForSearch: 10,
                    dropdownAutoWidth: true,
                    placeholder: "Tous",
                    allowClear: true,
                    width: '100%'
                });
            }

            if($(el).hasClass("with-filters")) {
                $(el).next('.tablesorter-sticky-wrapper').find('.tablesorter-filter-row select').select2({
                    minimumResultsForSearch: 10,
                    dropdownAutoWidth: true,
                    placeholder: "Tous",
                    allowClear: true,
                    width: '100%'
                });
            }
        } catch(ex) {}
    });

    init_tablesorter_parser();
    $(container).find('#table_div').addClass('loaded');
    $(container).find('.columnSelector').find(':checkbox,:radio').jsCheckbox();
}

function init_tablesorter_parser() {
    $.tablesorter.addParser({
        id: 'checkbox',
        is: function (s) {
            return false;
        },
        format: function (s, table, cell, cellIndex) {
            var $c = $(cell);
            // return 1 for true, 2 for false, so true sorts before false
            if (!$c.hasClass('updateCheckbox')) {
                $c
                    .addClass('updateCheckbox')
                    .bind('change', function () {
                        $(table).trigger('updateCell', [cell]);
                    });
            }
            return (
                $c.find('input[type=checkbox]').length > 0 
            &&  $c.find('input[type=checkbox]')[0].checked ) ? 1 : 2;
        },
        type: 'numeric'
    });
}