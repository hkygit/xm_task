<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta http-equiv='X-UA-Compatible' content='IE=edge'>
	<meta name="renderer" content="webkit">
	<link rel="Shortcut Icon" href="/Public/image/img/favicon.ico">
    <title>登录-Task任务管理系统</title>
	<link rel="stylesheet" type="text/css" href="/Public/css/login.css?v=2.0" />
	<link rel="stylesheet" type="text/css" href="/Public/css/ui-dialog.css" />
</head>
<body>
<div id="login_page">
    <h1 class="task_title"></h1>
    <div class="login_box">
        <div class="fl login_left">
            <div class="login_left_box">
                <img src="/Public/image/img/task_logo.png" alt="">
            </div>
        </div>
		<form id="loginForm" action="<?php echo u('passport/login');?>" autocomplete="off">
			<div class="fr login_right">
				<div class="login_right_box">
					<h2 class="user_login"></h2>
					<table>
						<tbody>
						<tr>
							<th>账号：</th>
							<td class="input_box"><input type="text" name="loginName" id="loginName" placeholder="请输入登录账号"></td>
						</tr>
						<tr>
							<th></th>
							<td>
								<div class="wrong_detail" id="loginNameTips"></div>
							</td>
						</tr>
						<tr>
							<th>密码：</th>
							<td class="input_box"><input type="password" name="loginPass" id="loginPass" placeholder="请输入密码"></td>
						</tr>
						<tr>
							<th></th>
							<td>
								<div class="wrong_detail" id="loginPassTips"></div>
							</td>
						</tr>

						<tr>
							<th></th>
							<td>
								<input type="checkbox" id="remember" name="remember" value="1"><label for="remember">下次自动登录</label>
							</td>
						</tr>
						<tr>
							<th></th>
							<td>
								<div class="wrong_detail"></div>
							</td>
						</tr>

						<tr>
							<th></th>
							<td>
								<input type="button" class="btn btn_img login_btn" value="登录">
							</td>
						</tr>
						</tbody>
					</table>
					<input type="hidden" id="location_href" value="<?php echo ($back_act); ?>" />
				</div>
			</div>
		</form>
    </div>
</div>
<script type="text/javascript" src="/Public/js/jquery.js?v=1.11"></script>
<script type="text/javascript" src="/Public/js/dialog-min.js"></script>
<script type="text/javascript" src="/Public/js/common.js?v=2.05"></script>
<script type="text/javascript" src="/Public/js/login.js?v=2.0"></script>
</body>
</html>