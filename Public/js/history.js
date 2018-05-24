$(function() {
	var history_show_box = $('#history_show_box'), func = {
		toggle_list: function(obj) {
			if(obj.hasClass('btn_up_icon')) {
				obj.addClass('btn_down_icon').removeClass('btn_up_icon');
			} else {
				obj.addClass('btn_up_icon').removeClass('btn_down_icon');
			}

			obj.parent('legend').next().find('li').each(function() {
				$(this).prependTo($(this).parent());
			})
		}
	};

	history_show_box.find('.history_change').hide();

	history_show_box.find('.btn_up_icon').on('click', function() {
		func.toggle_list($(this));
	});

	history_show_box.find('legend .btn_expandable_icon').on('click', function() {
		if($(this).hasClass('btn_expandable_icon')) {
			history_show_box.find('.btn_expandable_icon').removeClass('btn_expandable_icon').addClass('btn_collapsable_icon');
			$(this).parent('legend').next().find('li .history_change').show();
		} else {
			history_show_box.find('.btn_collapsable_icon').removeClass('btn_collapsable_icon').addClass('btn_expandable_icon');
			$(this).parent('legend').next().find('li .history_change').hide();
		}
	});

	history_show_box.find('li .btn_img').on('click', function() {
		if($(this).hasClass('btn_expandable_icon')) {
			$(this).removeClass('btn_expandable_icon').addClass('btn_collapsable_icon');
			$(this).parent('li').find('.history_change').show();
		} else {
			$(this).removeClass('btn_collapsable_icon').addClass('btn_expandable_icon');
			$(this).parent('li').find('.history_change').hide();
		}
	})

})