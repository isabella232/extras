// Track the documentation view
var _gaq = _gaq || [];
_gaq.push(['so._setAccount', 'UA-15939505-2']);
_gaq.push(['so._trackPageview', '/premium/' + soPremiumUpgrade.theme + '/']);
_gaq.push(['so._setCustomVar', 3, 'Page Variation', soPremiumUpgrade.variation, 2]);

(function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();

jQuery(function($){
    var paymentWindow;
    $('#theme-upgrade .buy-button').click(function(){
        var $$ = $(this);
        
        // track the view
        _gaq.push(['so._setCustomVar', 3, 'Page Variation', soPremiumUpgrade.variation, 2]);
        _gaq.push(['so._trackPageview',  '/premium/' + soPremiumUpgrade.theme + '/buy/']);
        
        paymentWindow = window.open($$.attr('href'),'payment','height=800,width=1024');
        $('#theme-upgrade-info').slideDown();
        $('html, body').animate({'scrollTop': 0});
        
        return false;
    });
    
    // Display the 
    $('#theme-upgrade-already-paid').click(function(){
        $('#theme-upgrade-info').slideDown();
        return false;
    });
});