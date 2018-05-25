<?php
namespace Home\Model;

class WorkModel extends BaseModel {
	protected $_map = array(
		'wid'					=>	'id',
		'title'					=>	'name',
		'parentTask'  			=>	'pid',
		'taskVnum'				=>	'version',
		'precedence'			=>	'priority',
		'initHour'				=>	'estimate',
		'usedHour'				=>	'consumed',
		'remainHour'			=>	'surplus',
		'expectEndDate'			=>	'expect_deadline',
		'endDate'				=>	'deadline',
		'workUserNum'			=>	'work_type',
		'creator'				=>	'create_by',
		'createDept'			=>	'create_by_dept',
		'createDate'			=>	'create_time',
		'person'				=>	'assign',
		'planStartDate'			=>	'plan_start',
		'actualStartDate'		=>	'real_start',
		'finishedUser'			=>	'finish_by',
		'cancelUser'			=>	'cancel_by',
		'closedUser'			=>	'closed_by',
		'lastEditUser'			=>	'last_edit_by',
		'taskStatus'			=>	'status',
		'taskStage'				=>	'stage',
		'cTaskNum'				=>	'child_num',
		'copyUser'				=>	'cc',
		'relatedTask'			=>	'related_task',
		'finishedTime'			=>	'finish_time',
		'closedTime'			=>	'closed_time',
		'cancelTime'			=>	'cancel_time',
		'allowChildView'		=>	'allow_child_view',
		'taskStar'				=>	'satisfaction_star_num'
	);

	protected $_formField = array(
		array('type' => 'mustInput', 'field' => 'name', 'msg' => '请输入任务名称', 'tips_id' => 'task_name_tips_id', 'model'=>1),
		array('type' => 'lengthMustLessThan', 'field' => 'name', 'msg' => '您输入的任务名称过长', 'extend' => 255, 'tips_id' => 'task_name_tips_id', 'model'=>1),
		array('type' => 'mustInput', 'field' => 'priority', 'msg' => '请选择优先级', 'tips_id' => 'precedence_tips_id', 'model'=>3),
		// array('type' => 'mustInput', 'field' => 'assign', 'msg' => '请选择指派人', 'tips_id' => 'person_tips_id', 'model'=>1),
		array('type' => 'mustInput', 'field' => 'create_by', 'msg' => '请选择提出人', 'tips_id' => 'creator_tips_id', 'model'=>3),
		array('type' => 'mustInput', 'field' => 'create_by_dept', 'msg' => '请选择提出部门', 'tips_id' => 'create_dept_tips_id', 'model'=>3),
		array('type' => 'mustInput', 'field' => 'create_time', 'msg' => '请选择提出时间', 'tips_id' => 'create_date_tips_id', 'model'=>2),
		array('type' => 'mustInput', 'field' => 'expect_deadline', 'msg' => '请选择期望截止日期', 'tips_id' => 'expect_deadline_tips_id', 'model'=>3),
	);
	
	protected $_auto = array(
		array('pid', 0, 3, 'default'),
		array('version', 1, 1, 'default'),
		array('path', '', 1, 'default'),
		array('estimate', 0, 1, 'default'),
		array('consumed', 0, 1, 'default'),
		array('surplus', 0, 1, 'default'),
		array('deadline', '0000-00-00 00:00:00', 1, 'default'),
		array('work_type', 0, 1, 'default'),
		array('create_time', '0000-00-00 00:00:00', 1, 'default'),
		array('assign', 0, 1, 'default'),
		array('assign_time', '0000-00-00 00:00:00', 1, 'default'),
		array('plan_start', '0000-00-00', 1, 'default'),
		array('real_start', '0000-00-00 00:00:00', 1, 'default'),
		array('allow_start_time', '0000-00-00', 1, 'default'),
		array('finish_by', 0, 1, 'default'),
		array('finish_time', '0000-00-00 00:00:00', 1, 'default'),
		array('cancel_by', 0, 1, 'default'),
		array('cancel_time', '0000-00-00 00:00:00', 1, 'default'),
		array('closed_by', 0, 1, 'default'),
		array('closed_time', '0000-00-00 00:00:00', 1, 'default'),
		array('last_edit_by', 0 , 1, 'default'),
		array('last_edit_time', '0000-00-00 00:00:00', 1, 'default'),
		array('status', 0, 1, 'default'),
		array('stage', 0, 1, 'default'),
		array('activated_num', 0, 1, 'default'),
		array('operator', 0, 1, 'default'),
		array('child_num', 0, 1, 'default'),
		array('level', 1, 1, 'default'),
		array('cc', '', 3, 'default'),
		array('related_task', '', 3, 'default'),
		array('pVersion', 0, 1, 'default'),
		array('part_in_review', '', 1, 'default'),
		array('allow_child_view', 0, 3, 'default'),
		array('special_work_type', 0),
		array('feedback_object', '', 1, 'default')
	);

	protected $insertFields = array('name', 'pid', 'version', 'path', 'priority', 'estimate', 'consumed', 'surplus', 'expect_deadline', 'deadline', 'work_type', 'create_by', 'create_time', 'create_by_dept', 'assign', 'assign_time', 'plan_start', 'real_start', 'allow_start_time', 'finish_by', 'finish_time', 'cancel_by', 'cancel_time', 'closed_by', 'closed_time', 'last_edit_by', 'last_edit_time', 'status', 'stage', 'activated_num', 'operator', 'child_num', 'level', 'cc', 'related_task', 'pVersion', 'allow_child_view', 'special_work_type');
	protected $updateFields = array('name', 'pid', 'version', 'path', 'priority', 'estimate', 'consumed', 'surplus', 'expect_deadline', 'deadline', 'work_type', 'create_by', 'create_time', 'create_by_dept', 'assign', 'assign_time', 'plan_start', 'allow_start_time', 'finish_by', 'finish_time', 'cancel_by', 'cancel_time', 'closed_by', 'closed_time', 'last_edit_by', 'last_edit_time', 'status', 'stage', 'activated_num', 'operator', 'child_num', 'level', 'cc', 'related_task', 'pVersion', 'allow_child_view', 'satisfaction_star_num');
}