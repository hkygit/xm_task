<?php
/**
 * Created by PhpStorm.
 * User: XMuser
 * Date: 2018-05-24
 * Time: 11:01
 */

namespace Home\Controller;


class CommontasksController extends BaseController
{
    public function index(){

        $assign['addBtn'] = $this->printIcon(CONTROLLER_NAME,'addTask','add','list',array(),'提出任务','',false,false,'target="_blank"');
        $assign['statusList'] = fetch_lang('ACTION_FIELD.work.status.value');
        $assign['stageList'] = fetch_lang('ACTION_FIELD.work.stage.value');
        $assign['priorityList'] = fetch_lang('ACTION_FIELD.work.priority.value');
        $assign['satisfactionStarList'] = fetch_lang('ACTION_FIELD.work.satisfaction_star_num.value');
        $assign['showTempDate'] = $this->uid == 1 ? 1 : 0;
        $this->assign($assign);
        $this->display();
    }

    public function taskList() {
        $condition = $list = $bindParam = array();
        $page = I('get.page', 1, 'intval');
        $model = D('work');

        $status = I('post.taskStatus', '', 'trim');
        if(strlen($status) > 0) {
            if($status == '12') {
                $condition['_string'] = 'pa.id IS NOT NULL';
            } elseif(in_array($status, array(0, 1, 2, 3, -2, -3, -4, -5))) {
                $condition['w.status'] = ':w_status';
                $bindParam[':w_status'] = array($status, \PDO::PARAM_INT);
            }
        }

        $name = I('post.title');
        if(strlen($name) > 0) {
            $condition['w.name'] = array('LIKE', ':w_name');
            $bindParam[':w_name'] = '%'.$name.'%';
        }

        $id = I('post.wid', 0, 'intval');
        if(!empty($id)) {
            $condition['w.id'] = ':w_id';
            $bindParam[':w_id'] = array($id, \PDO::PARAM_INT);
        }

        $priority = I('post.precedence', 0, 'intval');
        if(in_array($priority, array(1, 2, 3, 4))) {
            $condition['w.priority'] = ':w_priority';
            $bindParam[':w_priority'] = array($priority, \PDO::PARAM_INT);
        }

        $assign = I('post.person', 0, 'intval');
        if(!empty($assign)) {
            $condition['w.assign'] = ':w_assign';
            $bindParam[':w_assign'] = array($assign, \PDO::PARAM_INT);
        }

        $deadline = I('post.endDate');
        if(!empty($deadline)) {
            $condition['w.deadline'] = ':w_deadline';
            $bindParam[':w_deadline'] = $deadline;
        }

        $stage = I('post.taskStage');
        if(strlen($stage) > 0 && in_array($stage, array(0, 1, 2, 3, 4, 5, 6, 7))) {
            $condition['w.stage'] = ':w_stage';
            $bindParam[':w_stage'] = array($stage, \PDO::PARAM_INT);
        }

        $create_by = I('post.creator', 0, 'intval');
        if(!empty($create_by)) {
            $condition['w.create_by'] = ':w_create_by';
            $bindParam[':w_create_by'] = array($create_by, \PDO::PARAM_INT);
        }

        $create_time = I('post.createDate');
        if(!empty($create_time)) {
            $condition['w.create_time'] = array(array('egt', ':w_create_time_s'), array('elt', ':w_create_time_e'));
            $bindParam[':w_create_time_s'] = $create_time.' 00:00:00';
            $bindParam[':w_create_time_e'] = $create_time.' 23:59:59';
        }

        $temp_time = I('post.tempDate');
        if(empty($create_time) && !empty($temp_time)) {
            $condition['w.create_time'] = array(array('egt', ':w_temp_time_s'), array('elt', ':w_temp_time_e'));
            $bindParam[':w_temp_time_s'] = $temp_time.' 00:00:00';
            $bindParam[':w_temp_time_e'] = $temp_time.' 23:59:59';
        }

        $create_by_dept = I('post.createDept', 0, 'intval');
        if(!empty($create_by_dept)) {
            $condition['CONCAT(",", d.path, ",")'] = array('LIKE', ':w_create_by_dept');
            $bindParam[':w_create_by_dept'] = '%,'.$create_by_dept.',%';
        }

        $finish_by = I('post.finishedUser', 0, 'intval');
        if(!empty($finish_by)) {
            $condition['w.finish_by'] = ':w_finish_by';
            $bindParam[':w_finish_by'] = array($finish_by, \PDO::PARAM_INT);
        }

        $satisfactionStar = I('post.satisfactionStar');
        if(strlen($satisfactionStar) > 0 && in_array($satisfactionStar, array(1, 2, 3, 4, 5))) {
            $condition['w.satisfaction_star_num'] = ':w_satisfaction_star_num';
            $bindParam[':w_satisfaction_star_num'] = array($satisfactionStar, \PDO::PARAM_INT);
        }

        if(!empty($condition)) {
            $quickSelectStatus = '';
        } else {
            $quickSelectStatus = I('post.quickSelectStatus', 1, 'intval');
            $quickSelectStatus && $quickSelectStatus = in_array($quickSelectStatus, array(1, 2, 3)) ? $quickSelectStatus : 1;
            switch($quickSelectStatus) {
                case 1:
                    $condition['w.status'] = array('IN', '0,1,2,3,-4');
                    break;
                case 3:
                    $related_person = M('entrust_list')->where(array('be_entrusted_uid'=>$this->uid))->getField('entrust_uid', true);
                    $related_uid = empty($related_person) ? array($this->uid) : array_merge(array($this->uid), $related_person);
                    $condition['w.assign'] = array('IN', $related_uid);
                    break;
            }
        }

        if(empty($condition['w.status'])) {
            $condition['w.status'] = array('NEQ', ':w_status');
            $bindParam[':w_status'] = array('-1', \PDO::PARAM_INT);
        }

        $condition['w.special_work_type'] = array('EQ', ':w_special_work_type');
        $bindParam[':w_special_work_type'] = array('0', \PDO::PARAM_INT);

        $sort_post_field = I('post.sort_field');
        if(!empty($sort_post_field)) {
            $sort_field_map = $model->parseFieldsMap(array($sort_post_field => ''), 0);
            $sort_field_map_keys = array_keys($sort_field_map);
            $sort_field = array_shift($sort_field_map_keys);
        } else {
            $sort_post_field = 'wid';
        }
        $sort_order = I('post.sort_order');
        $sort_field = !empty($sort_field) && in_array($sort_field, array('id', 'priority', 'create_by', 'status', 'stage', 'deadline', 'assign', 'pid', 'child_num', 'progress', 'danger')) ? $sort_field : 'id';
        $sort_order = !empty($sort_order) && in_array(strtoupper($sort_order), array('ASC', 'DESC')) ? $sort_order : 'DESC';

        $viewMangerSql = $this->getManagerView('w', true);

        $taskModel = $model->alias('w');
        if(!empty($viewMangerSql)) {
            if(!empty($viewMangerSql['join'])) {
                foreach($viewMangerSql['join'] as $v) {
                    $taskModel = $taskModel->join($v);
                }
            }
            if(!empty($viewMangerSql['condition'])) {
                $condition = empty($condition) ? $viewMangerSql['condition'] : array_merge($condition, $viewMangerSql['condition']);
                $bindParam = empty($bindParam) ? $viewMangerSql['bindParam'] : array_merge($bindParam, $viewMangerSql['bindParam']);
            }
        }
        if(!empty($create_by_dept)) {
            $taskModel = $taskModel->join('LEFT JOIN __DEPT__ d ON d.id=w.create_by_dept');
        }

        $taskModel_list = clone $taskModel;

        $num = $taskModel->join('LEFT JOIN __WORK_OVERDUE_PROCESS_APPLY__ pa ON pa.wid=w.id AND pa.status=0')->where($condition)->bind($bindParam)->count('distinct(w.id)');
        // $subSql = $taskModel->where($condition)->bind($bindParam)->group('w.id')->field('w.id')->order('null')->buildSql();
        // $num = $taskModel->table($subSql.' n')->count('n.id');

        service('Page')->setPageConfig($page, CONTROLLER_NAME, ACTION_NAME, $num, 'formContent', 'formCondition');
        $arrPage = service('Page')->setPages();

        if($sort_field == 'id') {
            $sort_field_str = 'w.id '.$sort_order;
        } elseif(in_array($sort_field, array('progress', 'danger'))) {
            $sort_field_str = $sort_field.' '.$sort_order.', w.id '.$sort_order;
        } elseif($sort_field == 'status') {
            $sort_field_str = 'overdue_status '.$sort_order.', w.id '.$sort_order;
        } else {
            $sort_field_str = 'w.'.$sort_field.' '.$sort_order.', w.id '.$sort_order;
        }

        $num && $list = $taskModel_list
            // ->join('LEFT JOIN __WORK__ p ON p.id=w.pid')
            ->join('LEFT JOIN __USER__ a ON a.user_id=w.assign')
            ->join('LEFT JOIN __WORK_OVERDUE_PROCESS_APPLY__ pa ON pa.wid=w.id AND pa.status=0')
            // ->join('LEFT JOIN __USER__ c ON c.user_id=w.create_by')
            ->where($condition)
            ->bind($bindParam)
            ->order($sort_field_str)
            ->limit($arrPage['startNum'], $arrPage['pageSize'])
            // ->distinct(true)
            ->field('w.id, w.name, w.priority, w.status, IF(pa.id IS NULL, w.status, 12) overdue_status, pa.now_review_by, w.stage, w.deadline, w.pid, w.child_num, w.work_type, w.feedback_object, a.user_name assign_user, assess_task_danger(w.status, w.deadline, w.plan_start, w.consumed, w.surplus) danger, w.consumed/(w.consumed+w.surplus) progress, w.create_by, w.assign')
            ->group('w.id')
            ->select();
        // dump($list);
        if(!empty($list)) {
            $taskIds = array();
            foreach($list as $k => $val) {
                $taskIds[$val['wid']] = $val['wid'];
            }

            foreach($list as $k => $val) {
                $list[$k]['assignBtn'] = $this->printIcon(CONTROLLER_NAME, 'assignTo', 'assign', 'icon', array('id' => $val['wid']), '指派', '', true, (($val['taskStatus'] == '1' && $val['taskStage'] == '2' && $val['workUserNum'] == '1') || $val['taskStatus'] == '-3' || !$this->checkIsAllowAccess(array($val['creator'], $val['person']), true, CONTROLLER_NAME, 'assignTo')) ? true : false, 'data-dialogid="assignToDialog" data-dialogtitle="'.sub_str($val['title'], 20).' > 指派"');
                $list[$k]['changeBtn'] = $this->printIcon(CONTROLLER_NAME, 'change', 'change', 'icon', array('id' => $val['wid']), '变更', '', false, ((in_array($val['taskStatus'], array('2', '1', '-4', '3', '-3')) || ($val['taskStatus'] == '0' && $val['taskStage'] == '0')) && $this->checkIsAllowAccess($val['creator'], true, CONTROLLER_NAME, 'change')) ? false : true, 'target="_blank"');
                $list[$k]['reviewBtn'] = $this->printIcon(CONTROLLER_NAME, 'review', 'review', 'icon', array('id' => $val['wid']), '评审', '', false, in_array($val['taskStatus'], array('0', '2')) || ($val['overdue_status'] == '12' && $this->checkIsAllowAccess($val['now_review_by'], true, CONTROLLER_NAME, 'review')) ? false : true, 'target="_blank"');

                if($val['taskStatus'] == '1' && (($val['taskStage'] == '2' && $val['workUserNum'] == '1') || $val['workUserNum'] == '2')) {	//激活状态可暂停
                    $list[$k]['pauseBtn'] = $this->printIcon(CONTROLLER_NAME, 'pause', 'pause', 'icon', array('id' => $val['wid'], 'type' => $val['workUserNum']), '暂停', '', true, $this->checkIsAllowAccess($val['creator'], true, CONTROLLER_NAME, 'pause') ? false : true, 'data-dialogid="pauseTaskDialog" data-dialogtitle="'.sub_str($val['title'], 20).' > 暂停"');
                } elseif($val['taskStatus'] == '-4') { //暂停状态可继续
                    $list[$k]['continueBtn'] = $this->printIcon(CONTROLLER_NAME, 'continueTask', 'start', 'icon', array('id' => $val['wid']), '继续', '', true, $this->checkIsAllowAccess($val['person'], true, CONTROLLER_NAME, 'continueTask') ? false : true, 'data-dialogid="continueTaskDialog" data-dialogtitle="'.sub_str($val['title'], 20).' > 继续"');
                } else {
                    $list[$k]['startBtn'] = $this->printIcon(CONTROLLER_NAME, 'start', 'start', 'icon', array('id' => $val['wid']), '开始', '', true, $val['taskStatus'] == '1' && $val['workUserNum'] == '1' && $val['taskStage'] == '1' && $this->checkIsAllowAccess($val['person'], true, CONTROLLER_NAME, 'start') ? false : true, 'data-dialogid="startTaskDialog" data-dialogtitle="'.sub_str($val['title'], 20).' > 开始"');
                }

                $list[$k]['recordestimateBtn'] = $this->printIcon(CONTROLLER_NAME, 'recordestimate', 'recordestimate', 'icon', array('id' => $val['wid']), '工时日志', '', true,  $val['taskStatus'] != '0' ? false : true, 'data-dialogid="recordestimateDialog" data-dialogtitle="'.sub_str($val['title'], 20).' > 工时日志"');
                $list[$k]['finishBtn'] = $this->printIcon(CONTROLLER_NAME, 'finish', 'finish', 'icon', array('id' => $val['wid']), '完成', '', true, ($val['taskStatus'] == '1' && $val['taskStage'] == '2' && $this->checkIsAllowAccess($val['person'], true, CONTROLLER_NAME, 'finish')) ? false : true, 'data-dialogid="finishDialog" data-dialogtitle="'.sub_str($val['title'], 20).' > 完成"');
                $list[$k]['closeBtn'] = $this->printIcon(CONTROLLER_NAME, 'close', 'close', 'icon', array('id' => $val['wid']), '关闭', '', true, ($val['taskStatus'] == '3' && $this->checkIsAllowAccess($val['creator'], true, CONTROLLER_NAME, 'close')) ? false : true, 'data-dialogid="closeTaskDialog" data-dialogtitle="'.sub_str($val['title'], 20).' > 关闭"');
                $list[$k]['editBtn'] = $this->printIcon(CONTROLLER_NAME, 'edit', 'edit', 'icon', array('id' => $val['wid']), '编辑', '', false, ($this->checkIsAllowAccess($val['creator'], true, CONTROLLER_NAME, 'edit') ? false : true), 'target="_blank"');
                $list[$k]['breakBtn'] = $this->printIcon(CONTROLLER_NAME, 'breakDown', 'breakDown', 'icon', array('id' => $val['wid']), '分解', '', false, ($val['taskStatus'] == '1' && $val['workUserNum'] == '2' && $this->checkIsAllowAccess($val['person'], true, CONTROLLER_NAME, 'breakDown')) ? false : true, 'target="_blank"');
                // $list[$k]['relationBtn'] = $this->printIcon(CONTROLLER_NAME, 'editRelation', 'relation', 'icon', array('id' => $val['wid']),'编辑任务关系', '', false, (in_array($val['taskStatus'], array('-1', '-2', '-5')) || $val['creator'] != $this->uid) ? true : false, 'target="_blank"');
                $list[$k]['overdueProcessBtn'] = $this->printIcon(CONTROLLER_NAME, 'overdueProcess', 'overdue_action', 'icon', array('id' => $val['wid']), '执行超期处理流程', '', true, $val['taskStatus'] == '1' && $this->checkIsAllowAccess($val['person'], true, CONTROLLER_NAME, 'overdueProcess') && ((!empty($val['endDate']) && $val['endDate'] != '0000-00-00' && $val['endDate'] < date('Y-m-d')) || $val['overdue_status'] == '12') ? false : true, 'data-dialogid="overdueProcessDialog" data-dialogtitle="'.sub_str($val['title'], 20).' > 执行超期处理流程"');
                $list[$k]['delBtn'] = $this->printIcon(CONTROLLER_NAME, 'delTask', 'delete', 'icon', array('id' => $val['wid']), '删除', '', true, (in_array($val['taskStatus'], array('-3', '-2', '-5')) && $this->checkIsAllowAccess($val['creator'], true, CONTROLLER_NAME, 'delTask')) ? false : true, 'data-dialogid="deleteTaskDialog" data-dialogtitle="'.sub_str($val['title'], 20).' > 删除"');

                switch($val['taskStage']){
                    case '0':
                    case '4':
                        $list[$k]['stage_class'] = 'step_gray';
                        break;
                    case '1':
                        $list[$k]['stage_class'] = 'step_org';
                        break;
                    case '2':
                    case '3':
                        $list[$k]['stage_class'] = 'step_green';
                        break;
                    default:
                        $list[$k]['stage_class'] = '';
                }

                switch($val['overdue_status']) {
                    case 0:
                        $list[$k]['status_class'] = 'step_org';
                        break;
                    case 1:
                    case 3:
                        $list[$k]['status_class'] = 'step_green';
                        break;
                    case 2:
                    case -2:
                    case -5:
                    case -4:
                    case 12:
                        $list[$k]['status_class'] = 'step_red';
                        break;
                    default:
                        $list[$k]['status_class'] = 'step_gray';

                }
                $list[$k]['status'] = fetch_lang('ACTION_FIELD.work.status.value.'.$val['overdue_status']);
                // if(in_array($val['wid'], $taskOverDueList)) {
                // $list[$k]['status'] = '执行超期评审';
                // $list[$k]['status_class'] = 'step_red';
                // }

                if($val['taskStage'] == '1') {
                    $list[$k]['stage'] = $val['workUserNum'] == '1' ? '待执行' : '待细分';
                } elseif($val['taskStage'] == '2') {
                    $list[$k]['stage'] = $val['workUserNum'] == '1' ? '执行中' : '已细分';
                } else {
                    $list[$k]['stage'] = fetch_lang('ACTION_FIELD.work.stage.value.'.$val['taskStage']);
                }

                $list[$k]['priority'] = fetch_lang('ACTION_FIELD.work.priority.value.'.$val['precedence']);
                $list[$k]['progress'] = ($val['progress'] !== null) ? (floor(($val['progress'])*100)).'%' : '0%';
            }
        }

        $this->assign('list', $list);
        $this->assign('sort_order', $sort_order);
        $this->assign('sort_field', $sort_post_field);
        $this->assign('pages', $arrPage);
        $batchDelAccess = $this->checkNodeAccess(CONTROLLER_NAME, 'batchDel');
        $this->assign('batchDelAccess', $batchDelAccess);
        $this->assign('quickSelectStatus', $quickSelectStatus);
        $this->assign('viewChildAccess',  $this->checkNodeAccess(CONTROLLER_NAME, 'viewChildTasks'));

        $this->display();
    }
}