jQuery(function($){
    // Create the main dialog
    $('#panels-dialog').show().dialog({
        autoOpen: false,
        modal : true,
        title : $('#panels-dialog').attr('data-title'),
        minWidth: 960,
        minHeight: 400,
        close: function(){
            $('#panels-container .panel.new-panel').hide().fadeIn('slow').removeClass('new-panel');
        }
    }).find('.panel-type').disableSelection();
    
    // And the tabs in the main dialog
    $('#panels-dialog-tabs').tabs({});$('#panels-dialog-tabs').tabs({});
    
    // The button for adding a panel
    $('#panels .panels-add')
        .button({
            icons : {primary: 'ui-icon-add'},
            text : false
        })
        .click(function(){
            $('#panels-dialog').dialog('open');
            return false;
        });
    
    // The button for adding a grid
    $('#panels .grid-add')
        .button({
            icons : {primary: 'ui-icon-columns'},
            text : false
        })
        .click(function(){
            $('#grid-add-dialog').dialog('open');
            return false;
        });
    
    var newPanelId = 0;

    /**
     * Create a new panel
     * 
     * @param type
     * @return {*}
     */
    var createPanel = function(type, data){
        var $$;
        if(typeof type == 'string') $$ = $('#panels-dialog .panel-type[data-class="'+type+'"]');
        else $$ = type;
        
        var panel = $('<div class="panel new-panel"><div class="panel-wrapper"><h4></h4><small class="description"></small><div class="form"></div></div></div>');
        var dialog;

        var formHtml = $$.attr('data-form');
        formHtml = formHtml.replace(/\{\%id\}/g, newPanelId++);

        panel.find('h4').html('Panel Title').click(function(){
            dialog.dialog('open');
        });
        panel.find('.description').html('Panel Description');
        panel.find('.form').html(formHtml);
        dialog = $('<div id="panel-dialog" />').addClass('dialog-form').html(formHtml).dialog({
            autoOpen: false,
            modal : true,
            title : 'Temporary Title',
            minWidth: 700,
            open: function(){
                panel.find('.form *[name]').each(function(){
                    dialog.find('*[name="'+$(this).attr('name')+'"]').val($(this).val());
                });
                
                // This gives panel types a chance to influence the form
                $(this).trigger('panelsopen');
            },
            buttons: {
                'Done' : function(){
                    $(this).trigger('panelsdone');
                    
                    // Transfer the dialog values across
                    dialog.find('*[name]').each(function(){
                        panel.find('.form *[name="'+$(this).attr('name')+'"]').val($(this).val());
                    });
                    dialog.dialog('close');
                }
            }
        });
        panel.disableSelection();
        
        if(data != undefined){
            // Populate the form values
            for(c in data){
                if(c != 'info') {
                    panel.find('.form *[name$="['+c+']"]').val(data[c]);
                    dialog.find('*[name$="['+c+']"]').val(data[c]);
                }
            }
        }

        // This is to refresh the dialog positions
        $(window).resize();
        return panel;
    }
    
    // Handle adding a new panel
    $('#panels-dialog .panel-type').click(function(){
        var panel = createPanel($(this));
        $('#panels-container .cell .panels-container').last().append(panel);
        $('#panels-container .cell .panels-container').sortable( "refresh").trigger('refreshcells');
        $.grid.resizeCells($('#panels-container .cell .panels-container').last().closest('.grid-container'));
    });
    
    if(panelsData != undefined){
        // Create all the content
        for(var gi in panelsData.grids){
            var cellWeights = [];

            // Get the cell weights
            for(var ci in panelsData.grid_cells){
                if(Number(panelsData.grid_cells[ci]['grid']) == gi){
                    cellWeights[cellWeights.length] =  Number(panelsData.grid_cells[ci].weight);
                }
            }

            // Create the grids
            var grid = $.grid.createGrid(Number(panelsData.grids[gi]['cells']), cellWeights);
            $.grid.setupGrid(grid);

            // Add panels to the grid cells
            for(var pi in panelsData.panels){

                if(Number(panelsData.panels[pi]['info']['grid']) == gi){
                    var pd = panelsData.panels[pi];
                    var panel = createPanel(pd['info']['class'], pd);
                    grid
                        .find('.panels-container').eq(Number(panelsData.panels[pi]['info']['cell']))
                        .append(panel)
                }
            }
        }

        $('.panels-container')
            .sortable('refresh')
            .trigger('refreshcells');

        // Remove the new-panel class from any of these created panels
        $('#panels-container .panel').removeClass('new-panel');
        // Make sure everything is sized properly
        $('#panels-container .grid-container').each(function(){
            $.grid.resizeCells($(this));
        });
    }
    else{
        // Temporary
        $.grid.setupGrid($.grid.createGrid(1));
    }
    
    $(window).resize(function(){
        $('.panels-admin-dialog').dialog("option", "position", "center");
    });
});