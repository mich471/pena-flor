(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module depending on jQuery.
        define(['jquery'], factory);
    } else {
        // No AMD. Register plugin with global jQuery object.
        factory(jQuery);
    }
}(function($){
    var defaults = {
        'host': "https://graph.instagram.com/me/media",
        'token': '',
        'container': '',
        'display_captions': false,
        'callback': null,
        'on_error': console.error,
        'after': null,
        'items': 6,
        'image_new_tab': '',
        'image_padding': '',
        'image_alt_tag': 0,
        'image_alt_label': '',
        'image_lazy_load': false,
        'show_videos' : false,
        'cache_time': 30,
        'lazy_load_placeholder_width': '100%'
    };
    var escape_map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;',
        '/': '&#x2F;',
        '`': '&#x60;',
        '=': '&#x3D;'
    };

    var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

    function escape_string(str) {
        return str.replace(/[&<>"'`=\/]/g, function (char) {
            return escape_map[char];
        });
    }

    /**
     * Cache management
     */
    function get_cache(options, last_resort){
        var read_cache = last_resort || false;

        if (!last_resort && options.cache_time > 0) {
            var cached_time = localStorage.getItem(options.cache_time_key);
            if(cached_time !== null && parseInt(cached_time) + 1000 * 60 * options.cache_time > new Date().getTime()){
                read_cache = true;
            }
        }

        if(read_cache){
            var data = localStorage.getItem(options.cache_data_key);
            if(data !== null){
                return JSON.parse(data);
            }
        }

        return false;
    };

    function set_cache(options, data){
        localStorage.setItem(options.cache_data_key, JSON.stringify(data));
        localStorage.setItem(options.cache_time_key, new Date().getTime());
    }

    /**
     * Request / Response
     */
    function parse_response(response){
        try {
            let data = response.data;
            if(typeof data !== "undefined"){
                return data;
            }
        } catch (e) {
            return false;
        }
    }

    function request_data(url, token, callback){
        $.get(
            url,
            {
                access_token: token,
                fields: 'id, caption, media_type, media_url, permalink'
            },
            function(response){
                data = parse_response(response);
                if(data !== false){
                    callback(data);
                }else{
                    // Unexpected response, not retrying
                    callback(false);
                }
            },
            'json')
            .fail(function (e) {
                callback(false, e);
        });
    }

    /**
     * Retrieve data
     */
    function get_data(options, callback){
        var data = get_cache(options, false);

        if(data !== false){
            // Retrieving data from cache
            callback(data);
        }else{
            // No cache, let's do the request
            var url = options.host;

            request_data(url, options.token, function(data, exception){
                if(data !== false){
                    set_cache(options, data);
                    callback(data);
                }else{
                    // Trying cache as last resort before throwing
                    data = get_cache(options, true);
                    if(data !== false){
                        callback(data);
                    }else{
                        options.on_error("Instagram Feed: Unable to fetch: " + exception.status, 5);
                    }
                }
            });
        }
    }

    /**
     * Rendering
     */
    function render(options, data){
        var html = "";
        var videoOptions = 'playsinline controls loop muted';

        if (isMobile) videoOptions = 'playsinline autoplay loop muted';

        window.wpLazyLoad = window.wpLazyLoad || {};

        var max = (data.length > options.items) ? options.items : data.length;
        var i = 0, totalDisplays = 0;
        do {
            var mediaType = data[i].media_type;
            var url = data[i].permalink;
            var image = data[i].media_url;
            var caption = (data[i].caption) ? escape_string(data[i].caption) : '';
            if (mediaType.toUpperCase() == 'IMAGE') {
                html +=     "    <a href='" + url + "'" + (options.display_captions && caption  ? " data-caption='" + caption + "'" : "") +  "  rel='noopener'" + options.image_new_tab + ">";
                if (options.image_lazy_load) {
                    html += "<span style='width: auto; height: 320px; float: none; display: block; position: relative;'>";
                    html +=     "       <img style='max-width: "+ options.lazy_load_placeholder_width +" ;margin-left: 45%' src='" + window.wpLazyLoad.imageloader + "' class='lazy "+ options.image_padding + "'" + " data-original='" + image + "' ";
                } else {
                    html +=     "       <img class='"+ options.image_padding + "'" + " src='" + image + "' ";
                }
                switch (options.image_alt_tag) {
                    case 1:
                        html +=     " alt='" + caption + "'";
                        break;
                    case 2:
                        html +=     " alt='" + options.image_alt_label + "'";
                        break;
                }
                html +=     " />";
                if (options.image_lazy_load) {
                    html += "</span>";
                }
                html +=     "    </a>";
                totalDisplays += 1;
            } else if (options.show_videos && ( mediaType.toUpperCase() == 'VIDEO') ) {
                html +=     "    <a href='" + url + "'" + (options.display_captions && caption  ? " data-caption='" + caption + "'" : "") +  "  rel='noopener'" + options.image_new_tab + ">";
                html +=     "       <video " + videoOptions + " class='" + options.image_padding  +"'><source src='" + image + "' ";
                html +=     " type='video/mp4'/>";
                html +=     "    </a>";
                totalDisplays += 1;
            }
            i += 1;
        } while ( (totalDisplays < max) && (i < data.length) );

        /** WeltPixel Rendering */

        $(options.container).html(html);

        /** WeltPixel Rendering */
        if (options.image_lazy_load) {
            $('img.lazy').lazyload({
                effect: window.wpLazyLoad.effect || "fadeIn",
                effectspeed: window.wpLazyLoad.effectspeed || "",
                imageloader: window.wpLazyLoad.imageloader || "",
                threshold: window.wpLazyLoad.threshold || "",
                load: function () {
                    if ($(this).parents('.instagram-photos').length) {
                        $(this).parent().removeAttr("style");
                    }
                    $(this).css({'max-width':'100%'});
                    $(this).css({'margin-left':'0'});
                    setTimeout(function () {
                        $(window).scroll();
                    }, 500);
                }
            });
        }

        if ((options.after != null) && typeof options.after === 'function') {
            var that = this;
            setTimeout(function(){ options.after.call(that); $('.shuffle-item img.use-padding').css('width', '98%');$('.shuffle-item video.use-padding').css('width', '98%');  }, 1000);
        }
    }

    $.instagramFeedBasic = function (opts) {
        var options = $.fn.extend({}, defaults, opts);

        if (options.token == "") {
            options.on_error("Instagram Feed: Error, no token defined.", 1);
            return false;
        }

        options.cache_data_key = 'instagramFeedBasic_' + options.container;
        options.cache_time_key = options.cache_data_key + '_time';

        get_data(options, function(data){
            if(options.container != ""){
                render(options, data);
            }
            if(options.callback != null){
                options.callback(data);
            }
        });
        return true;
    };

}));
