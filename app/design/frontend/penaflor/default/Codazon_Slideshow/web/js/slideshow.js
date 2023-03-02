require(['jquery','domReady!'],function($){
    var resizeMobile = false;
    var resizeDesktop = false;

    $(window).resize(function(){
        loadSlideshowImages();
    });

    $(document).ready(function(){
        loadSlideshowImages();
    });

    function loadSlideshowImages(){
        if($(window).width() < 768 && !resizeMobile){
            $('.slideshow-img-mobile').each(function(){
                if(!$(this).attr('src')){
                    $(this).attr('src', $(this).attr('data-src'));
                }
            });

            resizeMobile = true;
        } else if(!resizeDesktop) {
            $('.slideshow-img').each(function(){
                if(!$(this).attr('src')){
                    $(this).attr('src', $(this).attr('data-src'));
                }
            });

            resizeDesktop = true;
        }
    }
});