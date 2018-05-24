$(function() {
	var func = {
		login: function() {
			var loginName = $('#loginName').val(), loginPass = $('#loginPass').val(), loginNameTips = $('#loginNameTips'), loginPassTips = $('#loginPassTips');

			$('.wrong_detail').html('');

			if(loginName.length <= 0) {
				loginNameTips.html('请输入用户名');
				return false;
			}

			if(loginPass.length <= 0) {
				loginPassTips.html('请输入登陆密码');
				return false;
			}

			$.ajax({
				type: 'post',
				url: $('#loginForm').attr('action'),
				data: $('#loginForm').serialize(),
				success: function(data) {
					if(!data) {
						$.dialogMsg.alert('操作失败，请刷新后再试');
						$('#loginPass').val('');
						return false;
					}
					if(data.code > '10001') {
						if(!data.errorTips) {
							$.dialogMsg.alert(data.msg);
						} else {
							$('#'+data.errorTips).html(data.msg);
						}
						$('#loginPass').val('');
						return false;
					}

					location.href=$('#location_href').val();
				},
				dataType: 'json'
			})
		}
	}

	$('.login_btn').on('click', function() {
		func.login();
	})

	//回车登陆
	$('#loginForm input').keydown(function(event){
		var e = event || window.event;
		var key = e ? (e.charCode || e.keyCode) : 0;
		key == 13 && func.login();
	});
})