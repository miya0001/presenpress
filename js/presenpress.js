(function($){
    var defaults = {
        history: true,
        transition: 'default'
    };
    var options = $.extend(defaults, presentation_settings);

    Reveal.initialize({
        controls: true,
        progress: true,
        history: options.history,
        center: true,
        transition: options.transition/*,
        dependencies: [
            { src: presenpress_url + '/reveal/lib/js/classList.js', condition: function() { return !document.body.classList; } },
            { src: presenpress_url + '/reveal/plugin/markdown/marked.js', condition: function() { return !!document.querySelector( '[data-markdown]' ); } },
            { src: presenpress_url + '/reveal/plugin/markdown/markdown.js', condition: function() { return !!document.querySelector( '[data-markdown]' ); } },
            { src: presenpress_url + '/reveal/plugin/highlight/highlight.js', async: true, callback: function() { hljs.initHighlightingOnLoad(); } },
            { src: presenpress_url + '/reveal/plugin/zoom-js/zoom.js', async: true, condition: function() { return !!document.body.classList; } },
            { src: presenpress_url + '/reveal/plugin/notes/notes.js', async: true, condition: function() { return !!document.body.classList; } }
        ]*/
    });

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
        $('#presenpress-highlight').hide();
    }).bind('pointables', function(e, frame){
        if (frame.pointables[0].touchZone === 'touching') {
            var pos = frame.pointerOffset();
            var h = $('#presenpress-highlight').height();
            var w = $('#presenpress-highlight').width();
            $('#presenpress-cursor').fadeOut();
            $('#presenpress-highlight').fadeIn();
            $('#presenpress-highlight').css({
                top: pos[0].top - (h / 2),
                left: pos[0].left - (w / 2)
            });
        } else {
            $('#presenpress-cursor').show();
            var pos = frame.pointerOffset();
            var h = $('#presenpress-cursor').height();
            var w = $('#presenpress-cursor').width();
            $('#presenpress-cursor').css({
                top: pos[0].top - (h / 2),
                left: pos[0].left - (w / 2)
            });
            $('#presenpress-highlight').fadeOut();
        }
    }).bind('pointablesout', function(e, frame){
        $('#presenpress-cursor').fadeOut();
        $('#presenpress-highlight').fadeOut();
    });
})(jQuery)
