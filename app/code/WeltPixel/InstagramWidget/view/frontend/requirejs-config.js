var config = {
    map: {
        '*': {
            Instafeed: 'WeltPixel_InstagramWidget/js/Instafeed',
            shufflejs: 'WeltPixel_InstagramWidget/js/Shuffle',
            polyfill: 'WeltPixel_InstagramWidget/js/Polyfill',
            instagramFeed: 'WeltPixel_InstagramWidget/js/instagramFeed',
            instagramFeedBasic: 'WeltPixel_InstagramWidget/js/instagramFeedBasic'
        }
    },
    shim: {
        Instafeed: {
            deps: ['jquery']
        },
        shufflejs : {
            deps: ['polyfill']
        },
        instagramFeed: {
            deps: ['jquery']
        },
        instagramFeedBasic: {
            deps: ['jquery']
        }
    }
};
