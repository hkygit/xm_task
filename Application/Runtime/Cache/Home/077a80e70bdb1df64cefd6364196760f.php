<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta http-equiv='X-UA-Compatible' content='IE=edge'>
	<meta name="renderer" content="webkit">
	<link rel="Shortcut Icon" href="/Public/image/img/favicon.ico">
    <title>通用任务-Task任务管理系统</title>
	<link rel="stylesheet" type="text/css" href="/Public/css/common.css?v=20180417" />
	<link rel="stylesheet" type="text/css" href="/Public/css/ui-dialog.css" />
	<link rel="stylesheet" type="text/css" href="/Public/css/chosen.css" />
	<link rel="stylesheet" type="text/css" href="/Public/js/kindeditor/plugins/code/prettify.css" />
	<?php if(($isIE9) == "1"): ?><link rel="stylesheet" type="text/css" href="/Public/css/uploadify.css" />
	<?php else: ?>
	<link rel="stylesheet" type="text/css" href="/Public/css/uploadifive.css" /><?php endif; ?>
	
    <link rel="stylesheet" type="text/css" href="/Public/css/zTreeStyle.css" />


	<script type="text/javascript">
		var uploadImageUrl = '<?php echo ($uploadImageUrl); ?>';
		var ajaxPasteImageUrl = '<?php echo U("file/ajaxPasteImage");?>';
		var isIE9Browser = '<?php echo ($isIE9); ?>';
	</script>
</head>
<body>
<div id="task_header">
    <div class="header_box">
        <a href="<?php echo U('index/index');?>" class="logo_box"></a>
        <div class="label_box">
			<ul>
				<?php if(is_array($topNav)): $i = 0; $__LIST__ = $topNav;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><li><a href="<?php echo ($val["url"]); ?>"<?php if(($val["name"]) == $nav_name): ?>class="on"<?php endif; ?>><?php echo ($val["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
        </div>
        <div class="user_action_box">
            <i class="user_action_icon"></i>
            欢迎您：<a class="user_name" href="<?php echo U('Usercenter/index');?>"><?php if(empty($userName)): ?>游客<?php else: echo ($userName); endif; ?></a>
			<a class="btn btn_img user_message" title="私人信息：<?php echo ($person_msg_num); ?>条 公共信息：<?php echo ($public_msg_num); ?>条" href="<?php echo U('Message/index');?>">
				<?php if(($total_msg_num) != "0"): ?><i id="total_msg_num"><?php echo ($total_msg_num); ?></i><?php endif; ?>
			</a>
            <a class="btn btn_img user_exit" href="<?php echo u('passport/logout');?>">退出</a>
        </div>
    </div>
</div>
<div id="task_content">
    <div class="content_label">
		<ul>
			<?php if(is_array($menu)): $i = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i; if(empty($val["childMenu"])): ?><li>
						<div class="label_content_container">
							<a href="<?php echo ($val["url"]); ?>" class="main_label <?php if(($val["name"]) == $menu_name): ?>on<?php endif; ?>"><?php echo ($val["title"]); ?></a>
						</div>
					</li>
				<?php else: ?>
					<li>
						<div class="label_content_container">
							<a href="javascript:void(0);" class="main_label <?php if(($val["name"]) == $menu_name): ?>on<?php endif; ?>"><?php echo ($val["title"]); ?></a>
							<div class="hide label_content_box">
								<ul class="hide third_label_box">
								<?php if(is_array($val["childMenu"])): $i = 0; $__LIST__ = $val["childMenu"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><li><a href="<?php echo ($v["url"]); ?>"><i><?php echo ($v["title"]); ?></i></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
								</ul>
							</div>
						</div>
					</li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
        </ul>
    </div>
	<div class="content_location">
        <a class="btn btn_img home_href" href="<?php echo u('index/index');?>"></a>
        <i>&gt;</i>
        <a href="<?php echo ($top_url); ?>"><?php echo ($top_menu); ?></a>
		<?php if(!empty($second_menu)): ?><i class="<?php if(empty($third_menu) AND !empty($second_menu)): ?>last_arrow<?php endif; ?>">&gt;</i>
        <a class="<?php if(empty($third_menu) AND !empty($second_menu)): ?>location_href<?php endif; ?>" href="<?php echo ($second_url); ?>"><?php echo ($second_menu); ?></a><?php endif; ?>
		<?php if(!empty($third_menu)): ?><i class="<?php if(!empty($third_menu) AND !empty($second_menu)): ?>last_arrow<?php endif; ?>">&gt;</i>
        <a class="<?php if(!empty($third_menu) AND !empty($second_menu)): ?>location_href<?php endif; ?>" href="<?php echo ($third_url); ?>"><?php echo ($third_menu); ?></a><?php endif; ?>
    </div>
	<div class="content_box">
		
    <div class="common_task_index">
        <div class="main_header">
            <div class="main_header_contrainer">
                <form action="<?php echo U('taskList');?>" autocomplete="off" id="formCondition">
                    <div class="fl task_type">
                        <ul id="quickSelectBtnContent">
                            <li><a class="on" data-id="1">未结束的</a></li>
                            <li><a data-id="2">所有的</a></li>
                            <li><a data-id="3">指派给我的</a></li>
                        </ul>
                    </div>
                    <div class="fl search_box">
                        <div class="search_contrainer content_search_box">
                            <div class="fl search_type_box">
                                <a class="btn btn_img no_select search_type_on">
                                    <span>任务名称</span>
                                    <i class="arrow down_arrow"></i>
                                    <input type="hidden" value="1" name="search_option" id="search_option" />
                                </a>
                                <ul class="search_type">
                                    <li><a data-id="1">任务名称</a></li>
                                    <li><a data-id="2">任务ID</a></li>
                                    <li><a data-id="3">优先级</a></li>
                                    <li><a data-id="4">指派给</a></li>
                                    <li><a data-id="5">计划截止</a></li>
                                    <li><a data-id="6">状态</a></li>
                                    <li><a data-id="7">阶段</a></li>
                                    <li><a data-id="8">提出人</a></li>
                                    <li><a data-id="9">提出日期</a></li>
                                    <li><a data-id="10">提出部门</a></li>
                                    <li><a data-id="11">完成人</a></li>
                                    <li><a data-id="12">完成满意度</a></li>
                                </ul>
                            </div>
                            <div class="fl input_box search_content_box" id="search_val_1">
                                <input type="text" class="search_text search_val" name="title" placeholder="请输入查询内容">
                            </div>
                            <div class="fl input_box hide search_content_box" id="search_val_2">
                                <input type="text" class="search_text search_val" name="wid" placeholder="请输入查询内容">
                            </div>
                            <div class="fl select_box hide search_content_box" id="search_val_3">
                                <select class="select_contrainer search_val" name="precedence">
                                    <option value="">请选择优先级</option>
                                    <?php if(is_array($priorityList)): $i = 0; $__LIST__ = $priorityList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>"><?php echo ($val); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                            <div class="fl input_box hide search_content_box" id="search_val_4">
                                <input type="text" class="search_text search_val" name="person" chosen_search='1' data-advanced="<?php echo U('user/advancedSearch');?>" data-url="<?php echo U('user/searchUser');?>" />
                            </div>
                            <div class="fl input_box hide search_content_box" id="search_val_5">
                                <input type="text" class="date_select search_val" name="endDate" placeholder="请选择计划截止" readonly="readonly" unselectable="on" />
                            </div>
                            <div class="fl select_box hide search_content_box" id="search_val_6">
                                <select class="select_contrainer search_val" name="taskStatus">
                                    <option value="">请选择状态</option>
                                    <?php if(is_array($statusList)): $i = 0; $__LIST__ = $statusList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i; if(($key) != "-1"): ?><option value="<?php echo ($key); ?>"><?php echo ($val); ?></option><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                            <div class="fl select_box hide search_content_box" id="search_val_7">
                                <select class="select_contrainer search_val" name="taskStage">
                                    <option value="">请选择阶段</option>
                                    <?php if(is_array($stageList)): $i = 0; $__LIST__ = $stageList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>"><?php echo ($val); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                            <div class="fl input_box hide search_content_box" id="search_val_8">
                                <input type="text" class="search_text search_val" name="creator" chosen_search='1' data-advanced="<?php echo U('user/advancedSearch');?>" data-url="<?php echo U('user/searchUser');?>" />
                            </div>
                            <div class="fl input_box hide search_content_box" id="search_val_9">
                                <input type="text" class="date_select search_val" name="createDate" placeholder="请选择提出日期" readonly="readonly" unselectable="on" />
                            </div>
                            <div class="fl input_box hide search_content_box" id="search_val_10">
                                <input type="text" class="search_text search_val" name="createDept" chosen_search='1' data-url="<?php echo U('department/searchDepartment');?>" data-advanced="<?php echo U('department/advancedSearch');?>" value="" data-formdata='{"noTop" : "1"}'/>
                            </div>
                            <div class="fl input_box hide search_content_box" id="search_val_11">
                                <input type="text" class="search_text search_val" name="finishedUser" chosen_search='1' data-advanced="<?php echo U('user/advancedSearch');?>" data-url="<?php echo U('user/searchUser');?>" />
                            </div>

                            <div class="fl select_box hide search_content_box" id="search_val_12">
                                <select class="select_contrainer search_val" name="satisfactionStar">
                                    <option value="">请选择满意度</option>
                                    <?php if(is_array($satisfactionStarList)): $i = 0; $__LIST__ = $satisfactionStarList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i; if(($key) != "0"): ?><option value="<?php echo ($key); ?>"><?php echo ($val); ?></option><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                            <div class="fl search_btn_box">
                                <a class="btn btn_img search_btn">搜索</a>
                            </div>
                        </div>
                    </div>
                    <?php if(($showTempDate) == "1"): ?><div class="fl input_box">
                            &nbsp;&nbsp;提出日期：<input type="text" class="date_select" name="tempDate" placeholder="请选择提出日期" readonly="readonly" unselectable="on" />
                        </div><?php endif; ?>

                    <input type="hidden" name="sort_field" value="" />
                    <input type="hidden" name="sort_order" value="" />
                    <input type="hidden" name="quickSelectStatus" value="1" />
                </form>
                <div class="fr action_btn task_action"><?php echo ($addBtn); ?></div>
            </div>
        </div>
        <div class="main_table_box common_task_contrainer" id="formContent"></div>
    </div>

	</div>
</div>
<div class="loading_data hide" id="loading_data_imgIcon"></div>

<?php if(!empty($task_helpcenter_url)): ?><a class="btn help_icon" href="<?php echo ($task_helpcenter_url); ?>" target="_blank"></a><?php endif; ?>

<script type="text/javascript" src="/Public/js/jquery.js?v=20170713"></script>
<?php if(($isIE9) == "1"): ?><script type="text/javascript" src="/Public/js/jquery.uploadify.js"></script>
<?php else: ?>
<script type="text/javascript" src="/Public/js/jquery.uploadifive.js"></script><?php endif; ?>
<script type="text/javascript" src="/Public/js/dialog-min.js"></script>
<script type="text/javascript" src="/Public/js/chosen.jquery.min.js"></script>
<?php if(($isIE8) == "1"): ?><script type="text/javascript" src="/Public/js/kindeditor/kindeditor-min-IE8.js"></script>
<?php else: ?>
<script type="text/javascript" src="/Public/js/kindeditor/kindeditor-min.js"></script><?php endif; ?>
<script type="text/javascript" src="/Public/js/kindeditor/lang/zh_CN.js"></script>
<script type="text/javascript" src="/Public/js/kindeditor/plugins/code/prettify.js"></script>
<script type="text/javascript" src="/Public/js/common.js?v=20180420"></script>

    <script type="text/javascript" src="/Public/js/WdatePicker.js"></script>
    <script type="text/javascript" src="/Public/js/jquery.ztree.core-3.5.min.js"></script>
    <script type="text/javascript" src="/Public/js/commontasks/task.js"></script>

</body>
</html>