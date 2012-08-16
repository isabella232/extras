jQuery(function($){
    // Create the main dialog
    $('#panels-dialog').dialog({
        autoOpen: false,
        modal : true,
        title : $('#panels-dialog').attr('data-title'),
        minWidth: 960,
        minHeight: 400,
        close: function(){
            $('#panels-container .panel.new-panel').hide().fadeIn('slow').removeClass('new-panel');
        }
    });
    
    // And the tabs in the main dialog
    $('#panels-dialog-tabs').tabs({});$('#panels-dialog-tabs').tabs({});
    
    $('#panels .panels-add')
        .button({
            icons : {primary: 'ui-icon-add'},
            text : false
        })
        .click(function(){
            $('#panels-dialog').dialog('open');
            return false;
        });

    $('#panels .columns-add')
        .button({
            icons : {primary: 'ui-icon-columns'},
            text : false
        })
        .click(function(){
            $('#columns-add-dialog').dialog('open');
            return false;
        });
    
    var newPanelId = 0;
    
    $('#panels-dialog .panel-type').click(function(){
        var $$ = $(this);
        var panel = $('<div class="panel new-panel"><div class="panel-wrapper"><h4></h4><small class="description"></small><div class="form"></div></div></div>');
        var dialog;

        var formHtml = $$.attr('data-form');
        formHtml = formHtml.replace(/\{\%id\}/g, newPanelId++);
        
        panel.find('h4').html('Panel Title').click(function(){
            dialog.dialog('open');
        });
        panel.find('.description').html('Panel Description');
        panel.find('.form').html(formHtml);
        dialog = $('<div />').addClass('dialog-form').html(formHtml).dialog({
                autoOpen: false,
                modal : true,
                title : 'asd',
                minWidth: 700,
                buttons: {
                    'Done' : function(){
                        dialog.find('*[name]').each(function(){
                            panel.find('.form *[name="'+$(this).attr('name')+'"]').val($(this).val());
                        });
                        dialog.dialog('close');
                    }
                }
            });
        panel.disableSelection();
        
        $('#panels-container .cell .panels-container').last().append(panel).sortable('refresh');
        $('#panels-container .cell .panels-container').sortable( "refresh" );
        $.grid.resizeCells($('#panels-container .cell .panels-container').last().closest('.grid-container'));
    });
    
    // Temporary
    $.grid.setupGrid($.grid.createGrid(1));
});