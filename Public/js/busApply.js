$(function() {
	var func = (function() {
		var d = function() {
			$.turn_page($('#formCondition').attr('action'), 'formContent', 'formCondition');
		};
		var s = function() {
			$.ajax({
				type: 'post',
				url: $('#stationSelectBox').data('url'),
				data: {getStation: '1', type: $('#addBusApplyForm input[type="radio"][name="applyType"]:checked').val(), overtimeType: $('#addBusApplyForm input[name="overtimeType"]:checked').val()},
				beforeSend: function() {
					$('#stationSelectBox').html('<select class="select_contrainer" name="apply_station"><option value="">请选择</option></select>');
					$('#addBusApplyForm .wrong_detail').html('');
				},
				success: function(data) {
					if(typeof(data) === 'undefined') {
						$.dialogMsg.alert('系统错误，请刷新后再试一次~');
						return false;
					} else if(data.error == '1') {
						$.dialogMsg.alert(data.msg);
						return false
					}

					$.each(data.content, function(i, v) {
						$('#stationSelectBox .select_contrainer').append('<option value="'+v.id+'">'+v.name+'</option>');
					})
					$('#stationSelectBox .select_contrainer').chosen('destroy');
					$('#stationSelectBox .select_contrainer').chosen({
						allow_single_deselect: true,
						disable_search_threshold: 10,
						no_results_text: "没有匹配结果",
						search_contains: true
					});
					$('#stationSelectBox .select_contrainer').trigger('change');
				},
				error: function() {
					$.dialogMsg.alert('系统错误，请刷新后再试一次~');
				},
				dataType: 'json'
			})
		};

		return {
			"getBusApplyData": d,
			"getStation": s
		};
	})();

	func.getBusApplyData();
	$('#formCondition .main_search_btn a').on('click', function() {
		func.getBusApplyData();
	})

	$('#formCondition #searchStartTime').on('click', function() {
		WdatePicker({isShowClear: false, readOnly: true});
	})
	$('#formCondition #searchEndTime').on('click', function() {
		WdatePicker({readOnly: true});
	})

	$('#formCondition .select_contrainer').chosen({
		allow_single_deselect: true,
		disable_search_threshold: 10,
		no_results_text: "没有匹配结果",
		search_contains: true
	});

	$(document).on('click', '#formContent .sort_icon', function() {
		var sort_order = $(this).hasClass('drop_sort') ? 'ASC' : 'DESC';
		$('#formCondition').find('input[name="sort_field"]').val($(this).data('field'));
		$('#formCondition').find('input[name="sort_order"]').val(sort_order);
		func.getBusApplyData();
	})

	$(document).on('change', '#addBusApplyForm input[type="radio"][name="applyType"]', function() {
		var type = $('#addBusApplyForm input[type="radio"][name="applyType"]:checked').val();

		$.each($('#addBusApplyForm .applyTypeSelectBox'), function() {
			if($(this).data('id') == type) {
				$(this).show();
			} else {
				$(this).hide();
			}
		})

		if(type == '1') {
			$('select[name="apply_season"]').next('.chosen-container').width(150);
		}

		func.getStation();
	})

	$(document).on('change', '#addBusApplyForm input[name="overtimeType"]', function() {
		func.getStation();
	})

	$(document).on('change', '#stationSelectBox .select_contrainer', function() {
		var self = $(this);

		if(self.val() == '') {
			$('#busLineSelectBoxId').html('<select class="select_contrainer"><option value="">请选择</option></select>');
			$('.selectBusLineBox').hide();
			return false;
		}

		$.ajax({
			type: 'post',
			url: $('#busLineSelectBoxId').data('url'),
			data: {getLine: '1', type: $('#addBusApplyForm input[type="radio"][name="applyType"]:checked').val(), overtimeType: $('#addBusApplyForm input[name="overtimeType"]:checked').val(), station: self.val()},
			beforeSend: function() {
				$('#busLineSelectBoxId').html('<select class="select_contrainer" name="apply_line"><option value="">请选择</option></select>');
				$('#addBusApplyForm .wrong_detail').html('');
			},
			success: function(data) {
				if(typeof(data) === 'undefined') {
					$.dialogMsg.alert('系统错误，请刷新后再试一次~');
					return false;
				} else if(data.error == '1') {
					$.dialogMsg.alert(data.msg);
					return false
				}

				$.each(data.content, function(i, v) {
					$('#busLineSelectBoxId .select_contrainer').append('<option value="'+v.id+'">'+v.name+'</option>');
				})
				$('.selectBusLineBox').show();
				$('#busLineSelectBoxId .select_contrainer').chosen({
					allow_single_deselect: true,
					disable_search_threshold: 10,
					no_results_text: "没有匹配结果",
					search_contains: true
				});
			},
			error: function() {
				$.dialogMsg.alert('系统错误，请刷新后再试一次~');
			},
			dataType: 'json'
		})
	})

	$(document).on('click', '#addBusApplyForm .btn_cannel', function() {
		dialog.get('addBusApplyDialog').remove();
	})

	$(document).on('click', '#addBusApplyForm .btn_submit', function() {
		var self = $(this);

		$.ajax({
			type: 'post',
			url: $('#addBusApplyForm').attr('action'),
			data: $('#addBusApplyForm').serialize(),
			beforeSend: function() {
				self.hide();
				self.before('<a class="btn btn btn_loading"></a>');
				$('#addBusApplyForm .wrong_detail').html('');
			},
			success: function(data) {
				if(typeof(data) == 'undefined') {
					$.dialogMsg.alert('系统错误，请刷新后再试一次~');
					dialog.get('addBusApplyDialog').remove();
					return false;
				} else if(data.error == '1') {
					if(data.tips_id && $('#'+data.tips_id)) {
						$('#'+data.tips_id).html(data.msg);
					} else {
						$.dialogMsg.alert(data.msg);
					}
					return false;
				}

				func.getBusApplyData();
				dialog.get('addBusApplyDialog').remove();
			},
			error: function() {
				$.dialogMsg.alert('系统错误，请刷新后再试一次~');
				dialog.get('addBusApplyDialog').remove();
			},
			complete: function() {
				self.show();
				self.prev('.btn_loading').remove();
			},
			dataType: 'json'
		})
	})

	$(document).on('click', '#addBusApplyForm .date_select', function() {
		WdatePicker({minDate:'%y-%M-%d'});
	})
})