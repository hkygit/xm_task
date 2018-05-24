(function ($) {
    $.fn.snow = function (options) {
		// alert($('.snow_box').length);
		if($('.snow_box').length <= 0) {
			$('body').append("<div class='snow_box'></div>");
		}
        var $snow = $('<div class="flakebox" />').css({'position': 'fixed', 'top': '-50px'}),
            documentHeight = $(document).height(),
            documentWidth = $(document).width(),
            defaults = {
                minSize: 10,		//雪花的最小尺寸
                maxSize: 20,		//雪花的最大尺寸
                newOn: 1000,		//雪花出现的频率
                flakeColor: "#FFFFFF"
            },
            options = $.extend({}, defaults, options);
        var interval = setInterval(function () {
            var startPositionLeft = Math.random() * documentWidth - 100,
                startOpacity = 0.5 + Math.random(),
                sizeFlake = options.minSize + Math.random() * options.maxSize,
                endPositionTop = documentHeight - 120,
                endPositionLeft = startPositionLeft - 100 + Math.random() * 500,
                maxEndPositionLeft = documentWidth - 80,
                durationFall = documentHeight * 10 + Math.random() * 5000;
            if (endPositionLeft > maxEndPositionLeft) {
                endPositionLeft = maxEndPositionLeft;
            }
            var snowShape = ["&#10052;", "<b>&#183;</b>"],
                snowHtml = snowShape[Math.floor(Math.random() * snowShape.length + 1) - 1];
            $snow.html(snowHtml).clone().appendTo('.snow_box').css({
                left: startPositionLeft,
                opacity: startOpacity,
                'font-size': sizeFlake,
                color: options.flakeColor
            }).animate({
                    top: endPositionTop,
                    left: endPositionLeft,
                    opacity: 0.5
                }, durationFall, 'linear', function () {
                    $(this).remove();
                }
            );
        }, options.newOn);
    };

})(jQuery);