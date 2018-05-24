$(function() {
	var func = {
		getThisMonth: function() {
			var obj = new Object();

			var t = new Date();
			var day = t.getDate();
			var mon = t.getMonth();
			var year = t.getFullYear();
			obj.end_time = this.formatDate(year, mon+1, day);

			var newDate = new Date(year, mon-1, day);
			obj.start_time = this.formatDate(newDate.getFullYear(), newDate.getMonth()+1, newDate.getDate());

			return obj;
		},
		getHalfYear: function() {
			var obj = new Object();

			var t = new Date();
			var day = t.getDate();
			var mon = t.getMonth();
			var year = t.getFullYear();
			obj.end_time = this.formatDate(year, mon+1, day);

			var newDate = new Date(year, mon-6, day);
			obj.start_time = this.formatDate(newDate.getFullYear(), newDate.getMonth()+1, newDate.getDate());

			return obj;
		},
		getThisYear: function() {
			var obj = new Object();

			var t = new Date();
			var day = t.getDate();
			var mon = t.getMonth();
			var year = t.getFullYear();
			obj.end_time = this.formatDate(year, mon+1, day);

			var newDate = new Date(year-1, mon, day);
			obj.start_time = this.formatDate(newDate.getFullYear(), newDate.getMonth()+1, newDate.getDate());

			return obj;
		},
		formatDate: function(year, month, day) {
			var month = month >= 10 ? month : '0'+month;
			var day = day >= 10 ? day : '0'+day;

			return year+'-'+month+'-'+day;
		},
		graphShow: function() {
			$.ajax({
				type: 'post',
				url: $('#graphBox').data('url'),
				data: $('#formCondition').serialize(),
				success: function(data) {
					if(!data) {
						$.dialogMsg.alert('系统繁忙，请稍后再试一次');
						return false;
					} else if(data.error) {
						if(!data.msg) {
							$.dialogMsg.alert('系统繁忙，请稍后再试一次');
						} else {
							$.dialogMsg.alert(data.msg);
						}
						return false;
					}

					if(data.graphType == 2) {
						makeGraph.column('graphBox', data.graphData);
					} else {
						makeGraph.pie($('#graphBox'), data.graphData);
					}

					var thisMonth = func.getThisMonth();
					if(thisMonth.start_time == data.start_time && thisMonth.end_time == data.end_time) {
						$('.quickSelectBtn').removeClass('on');
						$('#monthQuickBtn').addClass('on');
						return false;
					}

					var halfYear = func.getHalfYear();
					if(halfYear.start_time == data.start_time && halfYear.end_time == data.end_time) {
						$('.quickSelectBtn').removeClass('on');
						$('#halfYearQuickBtn').addClass('on');
						return false;
					}

					var thisYear = func.getThisYear();
					if(thisYear.start_time == data.start_time && thisYear.end_time == data.end_time) {
						$('.quickSelectBtn').removeClass('on');
						$('#yearQuikBtn').addClass('on');
						return false;
					}
				},
				error: function() {
					alert('系统繁忙，请稍后再试一次');
				},
				dataType: 'json'
			});
		}
	}

	$('#searchBtn').on('click', function() {
		var type = $('.report_result_type').find('.on').data('type');
		if(type == '2') {
			$.turn_page($('#formContent').data('url'), 'formContent', 'formCondition');
		} else {
			func.graphShow();
		}

		return false;
	});
	var formCondition = $('#formCondition');

	$('#searchBtn').click();

	$('.date_select').on('click', function() {
		WdatePicker({maxDate:'%y-%M-%d'});
	})

	$('.quickSelectBtn').on('click', function() {
		var self = $(this), type = self.data('type');
		if(type == 'year') {
			var dateObj = func.getThisYear()
		} else if(type == 'halfYear') {
			var dateObj = func.getHalfYear()
		} else {
			var dateObj = func.getThisMonth();
		}

		formCondition.find('input[name="start_time"]').val(dateObj.start_time);
		formCondition.find('input[name="end_time"]').val(dateObj.end_time);
		$('.quickSelectBtn').removeClass('on');
		self.addClass('on');
	})

	$('.dataTypeSelectBtn').on('click', function() {
		var self = $(this), type = self.data('type');
		formCondition.find('input[name="dataType"]').val(type);
		$('.dataTypeSelectBtn').removeClass('on');
		self.addClass('on');
	})

	$('.showSelectTypeBtn').on('click', function() {
		var self = $(this), type = self.data('type');
		formCondition.find('input[name="showType"]').val(type);
		// if(type == '2' || type == '3') {
			// $('.workResultBtn').parent('li').show();
			// $('.select_department_box').show('slow');
		// } else {
			// $('.workResultDetailBtn').parent('li').hide();
			// $('.select_department_box').parent('li').hide('slow');
			// $('.workResultBtn').removeClass('on');
			// $('.workResultBtn').eq(0).addClass('on');
			// formCondition.find('input[name="workResult"]').val('1');
		// }
		if(type == '2') {
			$('.select_department_level').show('slow');

			$('.workResultBtn').parent('li').show();
			$('.select_department_box').hide('slow');

			$('.dataTypeSelect_task').parent('li').hide();
			$('.dataTypeSelect_other').parent('li').show();
			$('.dataTypeSelect_other').parent('li').parent('ul').prepend($('.dataTypeSelect_other').parent('li'));
			if($('.dataTypeSelect_task').hasClass('on')) {
				formCondition.find('input[name="dataType"]').val('2');
				$('.dataTypeSelectBtn').removeClass('on');
				$('.dataTypeSelect_other').eq(0).addClass('on');
			}
		} else if(type == '3') {
			$('.select_department_level').hide('slow');

			$('.workResultBtn').parent('li').show();
			$('.select_department_box').show('slow');

			$('.dataTypeSelect_task').parent('li').show();
			$('.dataTypeSelect_other').parent('li').hide();
			$('.dataTypeSelect_other').parent('li').parent('ul').prepend($('.dataTypeSelect_task').parent('li'));
			formCondition.find('input[name="dataType"]').val('1');
			$('.dataTypeSelectBtn').removeClass('on');
			$('.dataTypeSelect_task').addClass('on');
		} else {
			$('.select_department_level').hide('slow');

			$('.workResultDetailBtn').parent('li').hide();
			$('.select_department_box').hide('slow');
			$('.workResultBtn').removeClass('on');
			$('.workResultBtn').eq(0).addClass('on');
			formCondition.find('input[name="workResult"]').val('1');

			$('.dataTypeSelect_task').parent('li').show();
			$('.dataTypeSelect_other').parent('li').show();
			$('.dataTypeSelect_other').parent('li').parent('ul').prepend($('.dataTypeSelect_task').parent('li'));
		}

		$('.showSelectTypeBtn').removeClass('on');
		self.addClass('on');
	})

	$('.showSelectDLevelBtn').on('click', function() {
		formCondition.find('input[name="dlevel"]').val($(this).data('type'));
		$('.showSelectDLevelBtn').removeClass('on');
		$(this).addClass('on');
	})

	$('.workResultBtn').on('click', function() {
		var self = $(this), type = self.data('type');
		formCondition.find('input[name="workResult"]').val(type);
		$('.workResultBtn').removeClass('on');
		self.addClass('on');
	})

	$('.report_result_type li').on('click', function() {
		$('.report_result_type li').removeClass('on');
		$(this).addClass('on');

		if($(this).data('type') == '2') {
			$('#formContent').show();
			$('#graphBox').hide();
			$.turn_page($('#formContent').data('url'), 'formContent', 'formCondition');
		} else {
			$('#graphBox').show();
			$('#formContent').hide();
			$('#graphBox').html('');
			func.graphShow();
		}
	})

	$(document).on('click', '.sort_icon', function() {
		var sort_order = $(this).hasClass('drop_sort') ? 'ASC' : 'DESC';
		$('#formCondition').find('input[name="sort_field"]').val($(this).data('field'));
		$('#formCondition').find('input[name="sort_order"]').val(sort_order);
		$.turn_page($('#formContent').data('url'), 'formContent', 'formCondition');
	})
})