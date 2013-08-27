(function($){

    var defaults = {
        width: 960,
        height: 720,
        history: true,
        transition: 'default',
        icon_min: 50
    };
    var options = $.extend(defaults, presentation_settings);
    var links = [];
    var target_link;

    Reveal.initialize({
        width: options.width,
        height: options.height,
        controls: true,
        progress: true,
        history: options.history,
        center: true,
        transition: options.transition,

        dependencies: [
            { src: presenpress_url + '/reveal/lib/js/classList.js', condition: function() { return !document.body.classList; } },
            { src: presenpress_url + '/reveal/plugin/markdown/marked.js', condition: function() { return !!document.querySelector( '[data-markdown]' ); } },
            { src: presenpress_url + '/reveal/plugin/markdown/markdown.js', condition: function() { return !!document.querySelector( '[data-markdown]' ); } },
            { src: presenpress_url + '/reveal/plugin/highlight/highlight.js', async: true, callback: function() { hljs.initHighlightingOnLoad(); } },
            { src: presenpress_url + '/reveal/plugin/zoom-js/zoom.js', async: true, condition: function() { return !!document.body.classList; } },
            { src: presenpress_url + '/reveal/plugin/notes/notes.js', async: true, condition: function() { return !!document.body.classList; } }
        ]
    });

    Reveal.addEventListener('ready', function(){
        get_links();
    });

    Reveal.addEventListener('slidechanged', function(){
        get_links();
    });

    $('section').addClass('content-bootstrap-area');

    function get_links(){
        links = [];
        $('section.present a').each(function(){
            links[links.length] = this;
        });
    }

    function is_air_hover(pos){
        if ($('#presenpress-cursor').hasClass('highlight')) {
            return;
        }
        $(links).each(function(){
            var offset = $(this).offset();
            if (pos.top > offset.top && pos.left > offset.left) {
                if (pos.top < (offset.top + $(this).height())) {
                    if (pos.left < (offset.left + $(this).width())) {
                        $('#presenpress-cursor').addClass('wait');
                        target_link = this;
                        return false;
                    }
                }
            }
            $('#presenpress-cursor').removeClass('wait');
            target_link = false;
        });
    }

    var lock_gesture = false;
    $.leapmotion();
    $(window).bind('swipeleft', function(e, gesture){
        if (lock_gesture === false && 1000 < gesture.speed && 0 > gesture.startPosition[2]) {
            Reveal.next();
            lock_gesture = true;
            setTimeout(function(){
                lock_gesture = false;
            }, 1000);
        }
    }).bind('swiperight', function(e, gesture){
        if (lock_gesture === false && 1000 < gesture.speed && 0 > gesture.startPosition[2]) {
            Reveal.prev();
            lock_gesture = true;
            setTimeout(function(){
                lock_gesture = false;
            }, 1000);
        }
    }).bind('swipe', function(e, gesture){
        $('#presenpress-cursor').hide();
    }).bind('pointables', function(e, frame){
        var pos = frame.pointerOffset();
        is_air_hover(pos[0]);
        if (frame.pointables[0].touchZone === 'touching' && frame.pointables[0].stabilizedTipPosition[2] < 0) {
            if (target_link && !$('#presenpress-cursor').hasClass('highlight')) {
                location.href = $(target_link).attr('href');
                target_link = false;
            } else {
                $('#presenpress-cursor').addClass('highlight');
                var h = $('#presenpress-cursor').height();
                var w = $('#presenpress-cursor').width();
                $('#presenpress-cursor').css({
                    top: pos[0].top - (h / 2),
                    left: pos[0].left - (w / 2)
                });
            }
        } else {
            $('#presenpress-cursor').show();
            var h = $('#presenpress-cursor').height();
            var w = $('#presenpress-cursor').width();
            $('#presenpress-cursor').css({
                top: pos[0].top - (h / 2),
                left: pos[0].left - (w / 2)
            });
        }
        if ($('#presenpress-cursor').hasClass('highlight')) {
            if (frame.pointables[0].stabilizedTipPosition[2] < 0) {
                var size = Math.round(options.icon_min + Math.abs(frame.pointables[0].stabilizedTipPosition[2]) * 2);
                add_gradient(size);
            } else {
                add_gradient(options.icon_min);
            }
        }
    }).bind('pointablesout', function(e, frame){
        $('#presenpress-cursor').fadeOut();
        $('#presenpress-cursor').removeClass('highlight');
    });

    function add_gradient(size){
        var from_size = size * 0.9;
        $('#presenpress-cursor').css(
            "background",
            "-webkit-gradient(radial, center center, "+from_size+", center center, "+size+", from(transparent), to(#000000))"
        );
        $('#presenpress-cursor').css(
            "background",
            "-moz-radial-gradient(center center, circle, transparent "+from_size+"px, #000000 "+size+"px)"
        );
    }
})(jQuery)
