jQuery(function($){
    var container = $(share.selector);
    
    var niceFormat = function(count){
        if(count > 1e6) return Math.round(count/1e6) + ' M';
        else if(count > 1e3) return Math.round(count/1e3) + ' K';
        else return count;
    }
    
    window.originLoadTwitterCount = function (data) {
        container.find('.twitter .count').html(niceFormat(data.count)).css('visibility', 'visible');
    }
    
    window.originLoadFacebookCount = function(data){
        if(data == false) return;
        
        if(data.shares == undefined) data.shares = 0;
        
        container.find('.facebook .count')
            .css('visibility', 'visible')
            .html(niceFormat(data.shares));
    }
    
    url = encodeURIComponent(share.permalink);
    
    // Load tweet count for this URL
    $.getScript(
        'http://urls.api.twitter.com/1/urls/count.json?url=' + url + '&callback=originLoadTwitterCount'
    )

    $.getScript(
        'https://graph.facebook.com/' + url + '?callback=originLoadFacebookCount'
    )
    
    $(".twitter", container).click(function(){
        var $$ = $(this);
        
        var params = {
            url:share.permalink,
            text:share.title
        }
        
        if($$.attr('data-related') != undefined && $$.attr('data-related') != ''){
            params.related = $$.attr('data-related');
        }

        if($$.attr('data-via') != undefined && $$.attr('data-via') != ''){
            params.via = $$.attr('data-via');
        }
        
        console.log(params);
        
        var args = $.param(params);
        window.open('https://twitter.com/intent/tweet?'+args, 'Tweet', 'status=0,toolbar=0,width=550,height=420');
        return false;
    });
    
    $(".facebook", container).click(function () {
        var args = $.param({
            u:url
        });
        window.open('http://www.facebook.com/sharer.php?' + args, 'Like', 'status=0,toolbar=0,width=640,height=380,scrollbars=0,resizable=0,location=0');
        return false;
    });
});