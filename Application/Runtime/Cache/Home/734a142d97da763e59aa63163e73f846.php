<?php if (!defined('THINK_PATH')) exit();?><table class="main_table">
	<thead>
	<tr>
		<?php if($batchDelAccess): ?><th class="row_select">
				<input type="checkbox" name="chkall" />
			</th><?php endif; ?>
		<th class="col_50_px">
			<a class="sort_icon <?php if(($sort_field) == "wid"): if(($sort_order) == "DESC"): ?>drop_sort<?php else: ?>rise_sort<?php endif; ?> sort_already<?php else: ?>no_sort<?php endif; ?>" data-field="wid">ID</a>
		</th>
		<th class="col_20">
			任务名称
		</th>
		<th>
			<a class="sort_icon <?php if(($sort_field) == "precedence"): if(($sort_order) == "DESC"): ?>drop_sort<?php else: ?>rise_sort<?php endif; ?> sort_already<?php else: ?>no_sort<?php endif; ?>" data-field="precedence">优先级</a>
		</th>
		<!--<th class="col_80">
			<a class="sort_icon <?php if(($sort_field) == "creator"): if(($sort_order) == "DESC"): ?>drop_sort<?php else: ?>rise_sort<?php endif; ?> sort_already<?php else: ?>no_sort<?php endif; ?>" data-field="creator">提出人</a>
		</th>-->
		<th class="col_100">
			<a class="sort_icon <?php if(($sort_field) == "taskStatus"): if(($sort_order) == "DESC"): ?>drop_sort<?php else: ?>rise_sort<?php endif; ?> sort_already<?php else: ?>no_sort<?php endif; ?>" data-field="taskStatus">状态</a>
		</th>
		<th>
			<a class="sort_icon <?php if(($sort_field) == "taskStage"): if(($sort_order) == "DESC"): ?>drop_sort<?php else: ?>rise_sort<?php endif; ?> sort_already<?php else: ?>no_sort<?php endif; ?>" data-field="taskStage">阶段</a>
		</th>
		<th class="col_date">
			<a class="sort_icon <?php if(($sort_field) == "endDate"): if(($sort_order) == "DESC"): ?>drop_sort<?php else: ?>rise_sort<?php endif; ?> sort_already<?php else: ?>no_sort<?php endif; ?>" data-field="endDate">截止</a>
		</th>
		<th class="col_80">
			<a class="sort_icon <?php if(($sort_field) == "person"): if(($sort_order) == "DESC"): ?>drop_sort<?php else: ?>rise_sort<?php endif; ?> sort_already<?php else: ?>no_sort<?php endif; ?>" data-field="person">指派给</a>
		</th>
		<th class="col_75">
			<a class="sort_icon <?php if(($sort_field) == "parentTask"): if(($sort_order) == "DESC"): ?>drop_sort<?php else: ?>rise_sort<?php endif; ?> sort_already<?php else: ?>no_sort<?php endif; ?>" data-field="parentTask">上级</a>
		</th>
		<th class="col_80">
			<a class="sort_icon <?php if(($sort_field) == "danger"): if(($sort_order) == "DESC"): ?>drop_sort<?php else: ?>rise_sort<?php endif; ?> sort_already<?php else: ?>no_sort<?php endif; ?>" data-field="danger">危险评估</a>
		</th>
		<th>
			<a class="sort_icon <?php if(($sort_field) == "cTaskNum"): if(($sort_order) == "DESC"): ?>drop_sort<?php else: ?>rise_sort<?php endif; ?> sort_already<?php else: ?>no_sort<?php endif; ?>" data-field="cTaskNum">子任务</a>
		</th>
		<th class="col_120">
			<a class="sort_icon <?php if(($sort_field) == "progress"): if(($sort_order) == "DESC"): ?>drop_sort<?php else: ?>rise_sort<?php endif; ?> sort_already<?php else: ?>no_sort<?php endif; ?>" data-field="progress">进度</a>
		</th>
		<th class="col_21">
			操作
		</th>
	</tr>
	</thead>
	<?php if(!empty($list)): ?><tbody class="data_load_box">
		<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><tr class="main_tr <?php if(($mod) == "0"): ?>odd_tr<?php else: ?>even_tr<?php endif; ?>">
				<?php if($batchDelAccess): ?><td><input type="checkbox" name="delId[]" value="<?php echo ($val["wid"]); ?>" /></td><?php endif; ?>
				<td><?php echo ($val["wid"]); ?></td>
				<td class="text_left"><a href="<?php echo U('view', array('id' => $val['wid']));?>" title="<?php echo (htmlspecialchars($val["title"])); ?>" target="_blank"><?php echo (htmlspecialchars($val["title"])); ?></a></td>
				<td><?php echo ($val["priority"]); ?></td>
				<!--<td><?php echo ($val["create_user"]); ?></td>-->
				<td class="<?php echo ($val["status_class"]); ?>"><?php echo ($val["status"]); ?></td>
				<td class="<?php echo ($val["stage_class"]); ?>"><?php echo ($val["stage"]); ?></td>
				<td><?php if(empty($val['endDate']) OR $val['endDate'] == '0000-00-00'): ?>--<?php else: echo ($val["endDate"]); endif; ?></td>
				<td><?php echo ((isset($val["assign_user"]) && ($val["assign_user"] !== ""))?($val["assign_user"]):'--'); ?></td>
				<td><?php if(empty($val["parentTask"])): ?>--<?php else: ?><a href="<?php echo U('view', array('id' => $val['parentTask']));?>" title="#<?php echo ($val["parentTask"]); ?>" target="_blank">#<?php echo ($val["parentTask"]); ?></a><?php endif; ?></td>
				<td class="<?php switch($val["danger"]): case "已超期限": ?>beyond_deadline<?php break; case "危险": ?>step_org<?php break; case "高危": ?>step_red<?php break; case "正常": ?>step_green<?php break; default: endswitch;?>"><?php echo ($val["danger"]); ?></td>
				<td><?php if($val['workUserNum'] != '2'): ?>--<?php elseif(!$viewChildAccess OR $val['cTaskNum'] <= '0'): echo ($val["cTaskNum"]); else: ?><a class="onlyBody_click" data-dialogtitle="<?php echo sub_str($val['title'], 20);?> > 子任务" data-dialogid="viewChildTaskDialog" href="<?php echo U('viewChildTasks', array('id' => $val['wid']));?>" target="_blank"><?php echo ($val["cTaskNum"]); ?></a><?php endif; ?></td>
				<td>
					<div class="progress_bar_box">
						<div class="fl progress_bar_contrainer">
							<span class="progress_bar_state" style="width:<?php echo ($val["progress"]); ?>;"></span>
						</div>
						<i class="fr progress_bar_tips"><?php echo ($val["progress"]); ?></i>
					</div>
				</td>
				<td>
					<?php if(!empty($val["assignBtn"])): echo ($val["assignBtn"]); endif; ?>
					<?php if(!empty($val["changeBtn"])): echo ($val["changeBtn"]); endif; ?>
					<?php if(!empty($val["reviewBtn"])): echo ($val["reviewBtn"]); endif; ?>
					<?php if(!empty($val["pauseBtn"])): echo ($val["pauseBtn"]); endif; ?>
					<?php if(!empty($val["continueBtn"])): echo ($val["continueBtn"]); endif; ?>
					<?php if(!empty($val["startBtn"])): echo ($val["startBtn"]); endif; ?>
					<?php if(!empty($val["recordestimateBtn"])): echo ($val["recordestimateBtn"]); endif; ?>
					<?php if(!empty($val["finishBtn"])): echo ($val["finishBtn"]); endif; ?>
					<?php if(!empty($val["closeBtn"])): echo ($val["closeBtn"]); endif; ?>
					<?php if(!empty($val["editBtn"])): echo ($val["editBtn"]); endif; ?>
					<?php if(!empty($val["breakBtn"])): echo ($val["breakBtn"]); endif; ?>
					<?php if(!empty($val["overdueProcessBtn"])): echo ($val["overdueProcessBtn"]); endif; ?>
					<?php if(!empty($val["delBtn"])): echo ($val["delBtn"]); endif; ?>
				</td>
			</tr><?php endforeach; endif; else: echo "" ;endif; ?>
		</tbody>
	<?php else: ?>
		<tbody class="no_data_box">
		<tr>
			<td colspan="<?php if($batchDelAccess): ?>13<?php else: ?>12<?php endif; ?>">没有数据</td>
		</tr>
		</tbody><?php endif; ?>
	<tfoot>
	<tr>
		<td colspan="<?php if($batchDelAccess): ?>13<?php else: ?>12<?php endif; ?>">
			<?php if($batchDelAccess): ?><div class="fl select_action">
					<input type="checkbox" id="select_row" name="chkall" />
					<label class="btn no_select btn_select_row" for="select_row">全选/反选</label>
					<a class="btn btn_img btn_delete_row" id="batchDelBtn" data-url="<?php echo U('batchDel');?>">批量删除</a>
				</div><?php endif; ?>
			<div class="fr page_skip">
	<b class="fl">共计<?php echo ($pages["nums"]); ?>条记录，每页
	<select name="pageSetPerNum" class="pageSetPerNumSelect" data-url="<?php echo ($pages["setPerPageNumUrl"]); ?>" data-listid="<?php echo ($pages["formContent"]); ?>" data-formid="<?php echo ($pages["formCondition"]); ?>">
		<option value="3" <?php if(($pages["pageSize"]) == "3"): ?>selected<?php endif; ?>>3</option>
		<option value="5" <?php if(($pages["pageSize"]) == "5"): ?>selected<?php endif; ?>>5</option>
		<option value="10" <?php if(($pages["pageSize"]) == "10"): ?>selected<?php endif; ?>>10</option>
		<option value="15" <?php if(($pages["pageSize"]) == "15"): ?>selected<?php endif; ?>>15</option>
		<option value="20" <?php if(($pages["pageSize"]) == "20"): ?>selected<?php endif; ?>>20</option>
		<option value="30" <?php if(($pages["pageSize"]) == "30"): ?>selected<?php endif; ?>>30</option>
		<option value="40" <?php if(($pages["pageSize"]) == "40"): ?>selected<?php endif; ?>>40</option>
		<option value="50" <?php if(($pages["pageSize"]) == "50"): ?>selected<?php endif; ?>>50</option>
	</select>条记录</b>
	<b class="fl"><?php if(($pages["pageCount"]) == "0"): ?>0<?php else: echo ($pages["currentPage"]); endif; ?>/<?php echo ($pages["pageCount"]); ?></b>

	<div class="fl page_skip_select">
		<a class="<?php if(($pages["currentPage"]) <= "1"): ?>disabled<?php endif; ?> basePageOperation" href="javascript:void(0)" data-listid="<?php echo ($pages["formContent"]); ?>" data-formid="<?php echo ($pages["formCondition"]); ?>" data-url="<?php echo ($pages["firsturl"]); ?>">[首页]</a>
		<a class="<?php if(($pages["currentPage"]) <= "1"): ?>disabled<?php endif; ?> basePageOperation" href="javascript:void(0)" data-listid="<?php echo ($pages["formContent"]); ?>" data-formid="<?php echo ($pages["formCondition"]); ?>" data-url="<?php echo ($pages["preurl"]); ?>">[上页]</a>
		<a class="<?php if(($pages["currentPage"]) >= $pages["pageCount"]): ?>disabled<?php endif; ?> basePageOperation" href="javascript:void(0)" data-listid="<?php echo ($pages["formContent"]); ?>" data-formid="<?php echo ($pages["formCondition"]); ?>" data-url="<?php echo ($pages["nexturl"]); ?>">[下页]</a>
		<a class="<?php if(($pages["currentPage"]) >= $pages["pageCount"]): ?>disabled<?php endif; ?> basePageOperation" href="javascript:void(0)" data-listid="<?php echo ($pages["formContent"]); ?>" data-formid="<?php echo ($pages["formCondition"]); ?>" data-url="<?php echo ($pages["lasturl"]); ?>">[尾页]</a>
	</div>
	<div class="fl page_skip_location">
		跳转到：<input type="text" value="" class="basePageLocationNum" onSubmit="return false;">页
		<a class="btn btn_img basePageOperation" data-listid="<?php echo ($pages["formContent"]); ?>" data-formid="<?php echo ($pages["formCondition"]); ?>" data-url="<?php echo ($pages["locationNumUrl"]); ?>">GO</a>
	</div>
	<input type="hidden" value="<?php echo ($pages["currentPageUrl"]); ?>" id="currenPageId" />
	<input type="hidden" value="<?php echo ($trunpageDataType); ?>" id="trunpageDataType" />
</div>
		</td>
	</tr>
	</tfoot>
</table>