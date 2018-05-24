<?php
namespace Home\Controller;
use Think\Controller;
use Home\Service\Rbac1Service as RBAC;
// use Home\Service\Rbac1Service as RBAC;

class BaseController extends Controller {
	protected $uid = NULL;	//登陆用户uid
	protected $userName = NULL;	//登陆用户名
	protected $realName = NULL;	//真实姓名
	protected $roleId = 0;	//角色id
	protected $dept = 0;	//部门id
	protected $work_attendance = 1;	//考勤类型（单双休类型）
	protected $nodeAccessList = array(); //可访问节点权限列表
	protected $viewAccessList = array(); //可访问视图权限列表
	protected $needCheckAccess = true;	//当前操作是否需要权限检测

	function _initialize() {
		$session = session(C('USER_AUTH_KEY'));
		$this->uid = isset($session['uid']) ? $session['uid'] : 0;
		$this->realName = isset($session['realName']) ? $session['realName'] : '';
		$this->userName = isset($session['userName']) ? $session['userName'] : '';
		$this->roleId = isset($session['roleId']) ? $session['roleId'] : 0;
		$this->dept = isset($session['dept']) ? $session['dept'] : 0;
		$this->work_attendance = isset($session['work_attendance']) ? $session['work_attendance'] : 0;

		if($this->uid && in_array($this->uid, explode(',', C('ADMIN_AUTH_KEY')))) {
			$this->needCheckAccess = false;
		}

		// 用户权限检查
		if(C('USER_AUTH_ON') && $this->needCheckAccess) {
			//检查是否登录
			if(RBAC::checkLoginAccess() && !service('Passport')->isLogged()) {
				echo '<script style="text/javascript">location.href="'.U('Passport/index').'"</script>';
				exit;
			}
			if(!$this->uid) {
				$session = session(C('USER_AUTH_KEY'));
				$this->uid = isset($session['uid']) ? $session['uid'] : 0;
				$this->realName = isset($session['realName']) ? $session['realName'] : '';
				$this->userName = isset($session['userName']) ? $session['userName'] : '';
				$this->roleId = isset($session['roleId']) ? $session['roleId'] : 0;
				$this->dept = isset($session['dept']) ? $session['dept'] : 0;
				$this->work_attendance = isset($session['work_attendance']) ? $session['work_attendance'] : 0;
			}

			if(!in_array($this->uid, explode(',', C('ADMIN_AUTH_KEY')))) {
				$accessList = RBAC::getAccsessData($this->uid);
				if($this->uid && (empty($accessList) || empty($accessList['node']))) {
					service('Passport')->logoutUser();
					// 定义权限错误页面
					$this->error('该网站您没有访问权限', U('Passport/index'));
					exit;
				}
				$this->nodeAccessList = $accessList['node'];
				$this->viewAccessList = $accessList['view'];
				if(RBAC::checkAccess() && !RBAC::accessDecision($this->nodeAccessList)) {
					$this->error('该页面您没有访问权限', U('Index/index'));
					exit;
				}
			} else {
				$this->needCheckAccess = false;
			}
		}

		$this->menuList();	//导航列表
		$this->webMsgList();//站内信

		$this->assign('realName', $this->realName);
		$this->assign('userName', $this->userName);
		$this->assign('uploadImageUrl', U('file/uploadImage', '', ''));

		$isIE8 = $isIE9 = 0;
		$userAgent = $_SERVER['HTTP_USER_AGENT'];
		if (preg_match('/MSIE\s(\d+)\..*/i', $userAgent, $regs)) {
			$isIE8 = $regs[1] <= 8 ? 1 : 0;
		}
		$this->assign('isIE8', $isIE8);

		if (preg_match('/MSIE\s(\d+)\..*/i', $userAgent, $regs)) {
			$isIE9 = $regs[1] <= 9 ? 1 : 0;
		}
		$this->assign('isIE9', $isIE9);
	}

	/**
     +----------------------------------------------------------
     * 根据用户权限获取相应的顶级导航和二级导航
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     */
	protected function menuList() {
		$list = $topNav = $menu = $nowMenuList = array();

		$nav_menu_list = C('CONTROLLER');
		$now_menu = isset($nav_menu_list[strtoupper(CONTROLLER_NAME)]) ? $nav_menu_list[strtoupper(CONTROLLER_NAME)] : array();
		$this->assign('nav_name', isset($now_menu['nav']) ? strtoupper($now_menu['nav']) : '');
		$this->assign('menu_name', isset($now_menu['menu']) ? strtoupper($now_menu['menu']) : '');

		$menu_list = C('MENU');
		$nav_list = C('TOP_NAV');
		$third_list = C('THIRD_MENU');

		$this->assign('top_menu', isset($now_menu['nav']) && isset($nav_list[strtoupper($now_menu['nav'])]) ? $nav_list[strtoupper($now_menu['nav'])] : '');
		isset($now_menu['menu']) ? $this->assign('second_menu', $menu_list[strtoupper($now_menu['menu'])]) : '';
		isset($now_menu['third_menu']) ? $this->assign('third_menu', $third_list[strtoupper($now_menu['third_menu'])]) : '';
		
		$top_nav_index = $menu_index = array();

		if($this->needCheckAccess) {
			$NavMenuInfo = $nowNavList = array();

			if($this->uid) {
				$haveChildUser = M('user')->where(array('pid' => $this->uid, 'status' => array('gt', '0')))->cache(true, 3600)->count('user_id');
				$haveAccessViewJournal = M('journal_role_detail')->where(array('type' => '1', 'uid' => $this->uid))->cache(true, 3600)->count('id');

				$checkExamQuestionManager = M('exam_question_type')->where(array('maintain_by' => ':maintain_by', 'child_num' => ':child_num'))->bind(array(':maintain_by' => array($this->uid, \PDO::PARAM_INT), ':child_num' => array(0, \PDO::PARAM_INT)))->cache(true, 3600)->count('id');
				$checkQuestionnaireManager = M('questionnaire_question_type')->where(array('maintain_by' => ':maintain_by', 'child_num' => ':child_num'))->bind(array(':maintain_by' => array($this->uid, \PDO::PARAM_INT), ':child_num' => array(0, \PDO::PARAM_INT)))->cache(true, 3600)->count('id');

				
				$checkBusinessPartInfo = M('user_business')->alias('u')
				->join('LEFT JOIN __BUSINESS__ b ON b.id=u.business_id')
				->where(array('u.user_id' => ':user_id'))
				->bind(array(':user_id' => array($this->uid, \PDO::PARAM_INT)))
				->cache(true, 3600)
				->getField('b.property, u.business_id');
			} else {
				$haveChildUser = $haveAccessViewJournal = $checkExamQuestionManager = $checkQuestionnaireManager = 0;
				$checkBusinessPartInfo = array();
			}

			//获取有权限访问的顶级导航和二级导航
			if(!empty($this->nodeAccessList[strtoupper(MODULE_NAME)])) {
				foreach($this->nodeAccessList[strtoupper(MODULE_NAME)] as $controller => $actions) {
					foreach($actions as $key => $value) {
						if($value['type'] == 1) {
							$NavMenuInfo = isset($nav_menu_list[strtoupper($controller)]) ? $nav_menu_list[strtoupper($controller)] : array();
							if(empty($NavMenuInfo)) {
								continue;
							}

							if($NavMenuInfo['nav'] == 'MYHOME_NAV' && $haveChildUser <= 0 && (strtoupper($NavMenuInfo['menu']) == 'MYHOME_DEPT_MENU' || strtoupper($NavMenuInfo['menu']) == 'MYHOME_PENDING_MENU')) {	//没有考核成员不显示考核成员和协同审批导航
								continue;
							} elseif($NavMenuInfo['nav'] == 'REPORT_NAV' && $haveAccessViewJournal <= 0 && strtoupper($NavMenuInfo['menu']) == 'REPORT_JOURNALCHECK_MENU' && strtoupper($NavMenuInfo['third_menu']) == 'REPORT_JOURNAL_CHECK') {
								continue;
							} elseif($NavMenuInfo['nav'] == 'ADMIN_NAV' && (isset($NavMenuInfo['third_menu']) && strtoupper($NavMenuInfo['third_menu']) == 'EXAM_TASK_MANAGE_MENU') && C('EXAM_TASK_CHECK_USER') != $this->uid && $checkExamQuestionManager <= 0) {
								continue;
							} elseif($NavMenuInfo['nav'] == 'TASK_NAV' && strtoupper($NavMenuInfo['menu']) == 'PRODUCT_MANAGER_MENU') {
								if(empty($checkBusinessPartInfo) && empty($checkBusinessPartInfo['1'])) {	//不属于产品委员会
									continue;
								}
							}/* elseif($NavMenuInfo['nav'] == 'TASK_NAV' && strtoupper($NavMenuInfo['menu']) == 'PRODUCT_MANAGING_MENU' && $NavMenuInfo['third_menu'] == 'PRODUCT_MANAGER_THIRD_MENU') {
								if(empty($checkBusinessPartInfo) && empty($checkBusinessPartInfo['4'])) {	//不属于产品经理
									continue;
								}
							}*/

							if(strtoupper($value['moduleName']) == 'EXAMTYPE' && C('EXAM_TASK_CHECK_USER') != $this->uid && strtoupper($key) == 'INDEX') {
								continue;
							}
							if(strtoupper($value['moduleName']) == 'EXAMTYPE' && $checkExamQuestionManager <= 0 && strtoupper($key) == 'QUESTIONLIBRARY') {
								continue;
							}

							if(strtoupper($value['moduleName']) == 'QUESTIONNAIREMANAGE' && C('QUESTIONNAIRE_TASK_CHECK_USER') != $this->uid && strtoupper($key) == 'INDEX') {
								continue;
							}
							if(strtoupper($value['moduleName']) == 'QUESTIONNAIREMANAGE' && $checkQuestionnaireManager <= 0 && strtoupper($key) == 'QUESTIONLIBRARY') {
								continue;
							}

							if(strtoupper($NavMenuInfo['nav']) == strtoupper($now_menu['nav'])) {
								$nowMenuList[strtoupper($NavMenuInfo['menu'])] = strtoupper($menu_list[strtoupper($NavMenuInfo['menu'])]);
								empty($menu_index[strtoupper($NavMenuInfo['menu'])]) && $menu_index[strtoupper($NavMenuInfo['menu'])] = u(ucfirst(strtolower($controller)).'/'.strtolower($key));
								
								//if(strtoupper($NavMenuInfo['menu']) == strtoupper($now_menu['menu'])) {//三级导航
									isset($NavMenuInfo['third_menu']) && $third_menu_list[strtoupper($NavMenuInfo['menu'])][strtoupper($NavMenuInfo['third_menu'])] = strtoupper($third_list[strtoupper($NavMenuInfo['third_menu'])]);
									isset($NavMenuInfo['third_menu']) && $third_menu_index[strtoupper($NavMenuInfo['menu'])][strtoupper($NavMenuInfo['third_menu'])] =  u(ucfirst(strtolower($controller)).'/'.strtolower($key));
								//}
							}
							$nowNavList[strtoupper($NavMenuInfo['nav'])] = strtoupper($nav_list[strtoupper($NavMenuInfo['nav'])]);
							empty($top_nav_index[strtoupper($NavMenuInfo['nav'])]) && $top_nav_index[strtoupper($NavMenuInfo['nav'])] = u(ucfirst(strtolower($controller)).'/'.strtolower($key));
							break 1;
						}
					}
				}
			}

			//按照正确顶级导航顺序进行排序显示
			if(!empty($nowNavList)) {
				foreach($nav_list as $key => $value) {
					if(isset($nowNavList[strtoupper($key)])) {
						$topNav[] = array(
							'name'	=> strtoupper($key),
							'title'	=> strtoupper($value),
							'url'	=> $top_nav_index[strtoupper($key)]
						);
						if(strtoupper($key) == strtoupper($now_menu['nav'])){
							$this->assign('top_url', $top_nav_index[strtoupper($key)]);
						}
					}
				}
			}
		} else {
			//获取本次要显示的二级导航
			foreach(C('CONTROLLER') as $key => $value) {
				if(strtoupper($value['nav']) == strtoupper($now_menu['nav'])) {
					$nowMenuList[strtoupper($value['menu'])] = strtoupper($menu_list[strtoupper($value['menu'])]);
					
					isset($value['third_menu']) && $third_menu_list[strtoupper($value['menu'])][strtoupper($value['third_menu'])] = strtoupper($third_list[strtoupper($value['third_menu'])]);
					isset($value['third_menu']) && $third_menu_index[strtoupper($value['menu'])][strtoupper($value['third_menu'])] =  u(ucfirst(strtolower($key)).'/index');
				}

				if(!isset($top_nav_index[strtoupper($value['nav'])])) {
					$top_nav_index[strtoupper($value['nav'])] = u(ucfirst(strtolower($key)).'/index');
				}
				if(isset($value['menu']) && !isset($menu_index[strtoupper($value['menu'])])) {
					$menu_index[strtoupper($value['menu'])] = u(ucfirst(strtolower($key)).'/index');
				}
				
			}
			
			//获取顶级导航
			foreach(C('TOP_NAV') as $key => $value) {
				$topNav[] = array(
					'name'	=> strtoupper($key),
					'title'	=> strtoupper($value),
					'url'	=> $top_nav_index[strtoupper($key)]
				);
				if(strtoupper($key) == strtoupper($now_menu['nav'])){
					$this->assign('top_url', $top_nav_index[strtoupper($key)]);
				}
			}
		}

		//按照正确二级导航顺序进行排序显示
		if(!empty($nowMenuList)) {
			foreach($menu_list as $key => $value) {
				if(isset($nowMenuList[strtoupper($key)])) {
					if(isset($third_menu_list[strtoupper($key)])){
						$childMenu = array();
						$menu[] = array(
							'name'	=> strtoupper($key),
							'title'	=> strtoupper($value),
							'url'	=> $menu_index[strtoupper($key)]
						);
						if(strtoupper($key) == strtoupper($now_menu['menu'])){
							$this->assign('second_url', $menu_index[strtoupper($key)]);
						}
						foreach ($third_menu_list[strtoupper($key)] as $k => $v){
							$childMenu[] = array(
								'name'	=> strtoupper($k),
								'title'	=> strtoupper($v),
								'url'	=> $third_menu_index[strtoupper($key)][$k]
							);
							if(isset($now_menu['third_menu'])){
								if(strtoupper($k) == strtoupper($now_menu['third_menu'])){
									$this->assign('third_url', $third_menu_index[strtoupper($key)][$k]);
								}
							}
						}
						if(!empty($childMenu)){
							foreach ($menu as $k =>$v){
								if($v['name'] == strtoupper($key)){
									$menu[$k]['childMenu'] = $childMenu;
								}
							}
						}
					}else{
						$menu[] = array(
							'name'	=> strtoupper($key),
							'title'	=> strtoupper($value),
							'url'	=> $menu_index[strtoupper($key)]
						);
						if(strtoupper($key) == strtoupper($now_menu['menu'])){
							$this->assign('second_url', $menu_index[strtoupper($key)]);
						}
					}
				}
			}
		}
		$this->assign('topNav', $topNav);
		$this->assign('menu', $menu);
	}
	
	
	/**
	 +----------------------------------------------------------
	 * 根据用户获得站内信
	 +----------------------------------------------------------
	 * @access protected
	 +----------------------------------------------------------
	 */
	protected function webMsgList() {
		$msgList = M('web_message')->where(array('status'=> 0, 'receiver' => $this->uid))->getField('type', true);
		$person_msg_num = $public_msg_num = 0;
		if(!empty($msgList)){
			foreach ($msgList as $v){
				if($v == 1){//私人信息
					$person_msg_num ++;
				}elseif($v == 2){//公共信息
					$public_msg_num ++;
				}
			}
		}
		$this->assign('person_msg_num', $person_msg_num);
		$this->assign('public_msg_num', $public_msg_num);
		$this->assign('total_msg_num', $public_msg_num+$person_msg_num);
	}

	/**
     +----------------------------------------------------------
     * 根据传入的控制类和操作返回按钮html
     +----------------------------------------------------------
     * @param string $controller 控制类名称
     +----------------------------------------------------------
	 * @param integer $method 操作方法名称
	 +----------------------------------------------------------
	 * @param string $iconType 按钮图标样式（查看详情:detail ,添加：add,编辑:edit,取消:cancel,完成：finish,关闭:close，指派:assign，开始：start，记录工时：recordestimate，变更:change,分析评审：review，激活：activate，暂停：pause，删除：delete，备注：comment, 视图权限维护:manageView, 权限维护：manageAccess, 分解:breakDown, 修改密码:update_pwd, 停靠站点:station, 申请乘车人：apply_list）
	 +----------------------------------------------------------
	 * @param string $buttonType 按钮类型（图标：icon, 图文按钮：list）
	 +----------------------------------------------------------
	 * @param string||array $query URL传递入的查询字符串（例如：array('id' => 1)）
     +----------------------------------------------------------
	 * @param string $title 按钮提示或名称
	 +----------------------------------------------------------
	 * @param string $extraClass 附加class样式, 多个以空格分隔
	 +----------------------------------------------------------
	 * @param boolean $onlyBody 是否仅需要主体部分（例如ajax弹窗）
     +----------------------------------------------------------
	 * @param boolean $isforbid 是否禁止操作（默认false，可操作）
     +----------------------------------------------------------
	 * @param string $extra 附加信息（例如打开新页面：target="_blank"）
	 +----------------------------------------------------------
	 * @param boolean $noUrl url是否为空
     +----------------------------------------------------------
     * @return string 返回按钮html样式
     +----------------------------------------------------------
     */
	protected function printIcon($controller, $method, $iconType, $buttonType = 'icon', $query = array(), $title = '', $extraClass = '', $onlyBody = false, $isforbid = false, $extra = '', $noUrl = false) {
		if($this->needCheckAccess && !RBAC::accessDecision($this->nodeAccessList, MODULE_NAME, $controller, $method)) {
			return;
		}

		if($noUrl) {
			$url = 'javascript:void(0)';
		} else {
			$url = U($controller.'/'.$method, $query);
		}

		$btn = '';

		$btn = '<a class="btn btn_common_icon'.($isforbid ? ' btn_icon_disabled' : ($onlyBody ? ' onlyBody_click' : '')).(!empty($extraClass) ? ' '.$extraClass : '').'" href="'.($isforbid ? 'javascript:void(0)' : $url).'" title="'.$title.'"'. (!$isforbid ? (!empty($extra) ? ' '.trim($extra) : '') : str_replace('target="_blank"', '', $extra)).'>
					<i class="'.$iconType.'_icon"></i>
					<span>'.($buttonType == 'list' ? $title : '').'</span>
				</a>';

		return $btn;
	}

	/**
     +----------------------------------------------------------
     * 获取新旧数据间的不同之处
     +----------------------------------------------------------
     * @param array $old 旧数据
     +----------------------------------------------------------
	 * @param array $new 新数据
     +----------------------------------------------------------
	 * @return array
     +----------------------------------------------------------
     */
	protected function createChanges($old, $new) {
        $changes    = array();
        $magicQuote = get_magic_quotes_gpc();
        foreach($new as $key => $value) {
            if(strtolower($key) == 'last_edit_by') continue;
            if(strtolower($key) == 'last_edit_time')   continue;
            if(strtolower($key) == 'assign_time')   continue;
            if(strtolower($key) == 'operator')		 continue;
            if(strtolower($key) == 'mailto')		 continue;
            if(strtolower($key) == 'allow_start_time')	continue;
            if(strtolower($key) == 'update_time')	continue;
            if(strtolower($key) == 'path')	continue;

            if($magicQuote) $value = stripslashes($value);
            if($value != stripslashes($old[$key])) {
                $diff = '';
                if(substr_count($value, "\n") > 1 || substr_count($old[$key], "\n") > 1 || in_array(strtolower($key), array('name', 'version_info', 'contact_info', 'spec', 'steps', 'verify', 'remark', 'conclusion'))) {
                    $diff = $this->diff($old[$key], $value);
                }
                $changes[] = array('field' => $key, 'old' => $old[$key], 'new' => $value, 'diff' => $diff);
            }
        }
        return $changes;
    }

	/**
	 +----------------------------------------------------------
	 * 写入操作记录
	 +----------------------------------------------------------
	 * @param integer $object_type 操作对象类型
	 +----------------------------------------------------------
	 * @param integer $object_id 操作对象id
	 +----------------------------------------------------------
	 * @param string $actionType 操作类型
	 +----------------------------------------------------------
	 * @param string $comment 备注
	 +----------------------------------------------------------
	 * @param string $extra 附属信息
	 +----------------------------------------------------------
	 * @param integer $actor 操作人uid
	 +----------------------------------------------------------
	 * @param integer $actor_id 操作人uid
	 +----------------------------------------------------------
	 * @return 操作记录id
	 +----------------------------------------------------------
	 */
	protected function createActionLog($object_type, $object_id, $actionType, $comment = '', $extra = '', $actor_id = null) {
		if(isset($actor_id) && $actor_id == 0) {
			$real_actor = $actor = '系统';
			$real_actor_id = 0;
		} elseif(!empty($actor_id)) {
			if(is_array($actor_id)) {
				if(in_array($this->uid, $actor_id)) {
					$actor_id = $this->uid;
				} else {
					$actor_id = array_pop($actor_id);
				}
			}
			$actor = getUserById($actor_id);
			$real_actor = $this->userName;
			$real_actor_id = $this->uid;
		} else {
			$real_actor_id = $actor_id = $this->uid;
			$real_actor = $actor = $this->userName;
		}

		$actionType = strtolower($actionType);

		$action = array();

		$object_type = str_replace('`', '', $object_type);
		$action['object_type']	= strtolower($object_type);
		$action['object_id']	= $object_id;
		$action['actor_id']		= $actor_id;
		$action['actor']		= $actor;
		$action['action']    	= $actionType;
		$action['date']			= date('Y-m-d H:i:s');
		$action['comment']		= trim($comment);
		$action['extra']		= $extra;
		$action['real_actor_id']= $real_actor_id;
		$action['real_actor']	= $real_actor;

		return M('action')->add($action);
	}

	/**
     +----------------------------------------------------------
     * 记录操作数据
     +----------------------------------------------------------
     * @param integer $actionID 操作id
     +----------------------------------------------------------
	 * @param array $changes 操作数据
     +----------------------------------------------------------
     */
    protected function writeActionLogData($actionID, $changes) {
		$model = M('action_detail');

		$insertData = array();
        foreach($changes as $change) {
            $change['action_id'] = $actionID;
			$insertData[] = $change;
        }

		if(!empty($insertData)) {
			return $model->addAll($insertData);
		}

		return false;
	}

	/**
     +----------------------------------------------------------
     * 比较两个文本之间的差别
     +----------------------------------------------------------
     * @param string $text1 文本1
     +----------------------------------------------------------
	 * @param integer $text2 文本2
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
     */
    private function diff($text1, $text2) {
        $text1 = str_replace('&nbsp;', '', trim($text1));
        $text2 = str_replace('&nbsp;', '', trim($text2));
        $w  = explode("\n", $text1);
        $o  = explode("\n", $text2);
        $w1 = array_diff_assoc($w,$o);
        $o1 = array_diff_assoc($o,$w);
        $w2 = array();
        $o2 = array();
        foreach($w1 as $idx => $val) {
			$w2[sprintf("%03d<", $idx)] = sprintf("%03d- ", $idx+1) . "<del>" . trim($val) . "</del>";
		}
        foreach($o1 as $idx => $val) {
			$o2[sprintf("%03d>",$idx)] = sprintf("%03d+ ", $idx+1) . "<ins>" . trim($val) . "</ins>";
		}

        $diff = array_merge($w2, $o2);
        ksort($diff);
        return implode("\n", $diff);
    }

	/**
     +----------------------------------------------------------
     * 获取操作记录
     +----------------------------------------------------------
     * @param integer $objectType 操作对象类型
     +----------------------------------------------------------
	 * @param integer $objectId 操作对象id
     +----------------------------------------------------------
	 * @return array
     +----------------------------------------------------------
     */
	protected function getActionLog($objectType, $objectId) {
		$model = M('action');

		$list = $model->where(array('object_type' => $objectType, 'object_id' => $objectId))
				->order('id ASC')
				->field('id, object_type, object_id, actor, actor_id, action, date, comment, extra, real_actor, real_actor_id')
				->select();

		$actionIds = getTwoDimensionData($list, 'id');
		$actionIds && $histories = $this->getActionLogData($actionIds);

		$action_object_type = C('ACTION_OBJECT_TYPE');

		$actions = array();
		$patten = fetch_lang('change_old_file_url');
		foreach($list as $value) {
			$value['history'] = $histories ? (isset($histories[$value['id']]) ? $histories[$value['id']] : '') : '';
			$value['object_type'] = $action_object_type[$value['object_type']];

			if($value['extra'] && $value['action'] == 'rdemandclosed'){
				$value['extra'] = '<a target="_blank" href="'.u('demand/view', array('id'=>$value['extra'])).'">#'.$value['extra'].'</a>';
			}

			if($value['extra'] && $value['action'] == 'rbugclosed'){
				$value['extra'] = '<a target="_blank" href="'.u('bug/view', array('id'=>$value['extra'])).'">#'.$value['extra'].'</a>';
			}
			$value['comment'] = preg_replace_callback($patten, function($match) {
				if($match[2] <= C('OLD_TASK_FILE_DEADLINE')) {
					return $match[1].C('OLD_TASK_FILE_PATH').$match[2].$match[3];
				} else {
					return $match[0];
				}
			}, $value['comment']);

			$actions[$value['id']] = $value;
		}

		return $actions;
	}

	/**
     +----------------------------------------------------------
     * 根据操作id获取相关操作记录
     +----------------------------------------------------------
     * @param integer $id 操作记录ID
     +----------------------------------------------------------
	 * @return array
     +----------------------------------------------------------
     */
	protected function printActionLogById($id) {
		$model = M('action');
		$action = $model->where(array('id' => $id))
				->field('id, object_type, object_id, actor, action, date, comment, extra')
				->find();
		if(!$action) {
			return false;
		}

		$histories = $this->getActionLogData(array($action['id']));

		// $action_object_type = C('ACTION_OBJECT_TYPE');
		$list = array();
		$action['history'] = $histories ? $histories[$action['id']] : '';
		// $action['object_type'] = $action_object_type[$action['object_type']];

		return $action;
	}

	/**
     +----------------------------------------------------------
     * 获取操作记录具体数据
     +----------------------------------------------------------
     * @param integer $actionIds 操作记录ID
     +----------------------------------------------------------
	 * @return array
     +----------------------------------------------------------
     */
	protected function getActionLogData($actionIds) {
		$condition = count($actionIds) > 1 ? array('action_id' => array('in', $actionIds)) : array('action_id' => array_shift($actionIds));
		$list = M('action_detail')->where($condition)->field('id, action_id, field, old, new, diff')->select();

		$log = array();
		foreach($list as $value) {
			$log[$value['action_id']][] = $value;
		}
		return $log;
	}

	//自动表单令牌验证
	protected function autoCheckToken($data) {
		if(C('TOKEN_ON')){
			$name   = C('TOKEN_NAME', null, '__hash__');
			if(!isset($data[$name]) || !isset($_SESSION[$name])) { // 令牌数据无效
				return false;
			}

			// 令牌验证
			list($key,$value)  =  explode('_',$data[$name]);
			if(isset($_SESSION[$name][$key]) && $value && $_SESSION[$name][$key] === $value) { // 防止重复提交
				return true;
			}
			// 开启TOKEN重置
			if(C('TOKEN_RESET')) unset($_SESSION[$name][$key]);
			return false;
		}
		return true;
	}

	//表单提交成功后销毁session， 防止重复提交
	protected function destoryToken($data) {
		if(C('TOKEN_ON')){
			$name   = C('TOKEN_NAME', null, '__hash__');

			if(!isset($data[$name]) || !isset($_SESSION[$name])) { // 令牌数据无效
				return false;
			}

			list($key,$value)  =  explode('_',$data[$name]);
			if(isset($_SESSION[$name][$key]) && $value && $_SESSION[$name][$key] === $value) { // 防止重复提交
				unset($_SESSION[$name][$key]); // 验证完成销毁session
			}
		}
	}

	/**
	 *	过滤角色视图权限
	 *	$param $alias sting 实际角色表别名，默认空
	 *	$param $bindParam boolean 是否使用参数绑定
	 *	$return 返回结果数组（未使用参数绑定：array('join' => array(), 'condition' => array())；使用参数绑定：array('join' => array(), 'condition' => array(), 'bindParam' => array())）
	 */
	protected function getViewManagerRole($alias = '', $usebindParam = false, $moduleName = CONTROLLER_NAME) {
		$list = array('join' => array(), 'condition' => array());
		$usebindParam && $list['bindParam'] = array();
		if(!$this->needCheckAccess) {
			return $list;
		}

		$alias = $alias ? $alias.'.' : '';

		//没有视图权限则无法访问数据
		$viewAccess = $this->viewAccessList[strtoupper($moduleName)];
		if(empty($viewAccess)) {
			$list['condition'][] = array($alias.'id' => '-1');
			return $list;
		}

		$bindParam = $roleTypeList = $myPersonList = array();
		foreach($viewAccess AS $tempUid => $tempUserInfo) {
			foreach($tempUserInfo AS $tempType => $tempTypeInfo) {
				foreach($tempTypeInfo AS $v) {
					if($tempType != 11) {	//对象类型为11
						continue;
					}
					if($v['view_range'] == '1') {
						return $list;
					}
					if($v['view_range'] == '2' && strlen($v['range_content']) > 0) {
						$tempContent = explode(',', $v['range_content']);
						foreach($tempContent AS $rangeContent) {
							$roleTypeList[$rangeContent] = $rangeContent;
						}
					}
					if($v['view_range'] == '3' && strlen($v['range_content']) > 0) {
						$tempViewAccessList = array();
						$tempContent = explode(',', $v['range_content']);

						foreach($tempContent AS $tempInfo) {
							switch($tempInfo) {
								case '1':
									$myPersonList[$tempUid] = $tempUid;
									break;
							}
						}
					}
				}
			}
		}

		$subCondition = array();
		if(!empty($roleTypeList)) {
			if(count($roleTypeList) > 1) {
				$sqlParam = array();
				foreach($roleTypeList AS $k => $v) {
					if($usebindParam) {
						$sqlParam[] = ':manager_view_role_alias_type_'.$k;
						$bindParam[':manager_view_role_alias_type_'.$k] = array($v, \PDO::PARAM_INT);
					} else {
						$sqlParam[] = $v;
					}
				}
				$subCondition[$alias.'role_type'] = array('IN', $sqlParam);
			} else {
				if($usebindParam) {
					$subCondition[$alias.'role_type'] = ':manager_view_role_alias_type';
					$bindParam[':manager_view_role_alias_type'] = array(array_pop($roleTypeList), \PDO::PARAM_INT);
				} else {
					$subCondition[$alias.'role_type'] = array_pop($roleTypeList);
				}
			}
		}

		if(!empty($myPersonList)) {
			if(count($myPersonList) > 1) {
				$sqlParam = array();
				foreach($myPersonList AS $k => $v) {
					if($usebindParam) {
						$sqlParam[] = ':manager_view_role_alias_person_'.$k;
						$bindParam[':manager_view_role_alias_person_'.$k] = array($v, \PDO::PARAM_INT);
					} else {
						$sqlParam[] = $v;
					}
				}
				$subCondition[$alias.'ower_by'] = array('IN', $sqlParam);
			} else {
				if($usebindParam) {
					$subCondition[$alias.'ower_by'] = ':manager_view_role_alias_person';
					$bindParam[':manager_view_role_alias_person'] = array(array_pop($myPersonList), \PDO::PARAM_INT);
				} else {
					$subCondition[$alias.'ower_by'] = array_pop($myPersonList);
				}
			}
		}

		if(empty($subCondition)) {
			$list['condition'][] = array($alias.'id' => '-1');
			return $list;
		}

		$subCondition['_logic'] = 'OR';
		$list['condition'][] = $subCondition;
		$bindParam && $list['bindParam'] = $bindParam;

		return $list;
	}

	/**
	 *	过滤物料视图权限
	 *	$param $alias sting 实际物料表别名，默认空，sql会以实际物料表名作为字段前缀
	 *	$param $bindParam boolean 是否使用参数绑定
	 *	$return 返回结果数组（未使用参数绑定：array('join' => array(), 'condition' => array())；使用参数绑定：array('join' => array(), 'condition' => array(), 'bindParam' => array())）
	 */
	protected function getViewManagerMateriel($alias = '', $usebindParam = false, $moduleName = CONTROLLER_NAME) {
		$list = array('join' => array(), 'condition' => array());
		$usebindParam && $list['bindParam'] = array();
		if(!$this->needCheckAccess) {
			return $list;
		}

		$alias = $alias ? $alias.'.' : '';

		//没有视图权限则无法访问数据
		$viewAccess = $this->viewAccessList[strtoupper($moduleName)];
		if(empty($viewAccess)) {
			$list['condition'][] = array($alias.'id' => '-1');
			return $list;
		}

		$bindParam = $accountList = $divisionList = $productLineList = $myPersonLineList = $myPersonDivisionList = array();
		foreach($viewAccess AS $tempUid => $tempUserInfo) {
			foreach($tempUserInfo AS $tempType => $tempTypeInfo) {
				foreach($tempTypeInfo AS $v) {
					if($tempType != 9) {	//物料类型为9
						continue;
					}
					if($v['view_range'] == '1') {
						return $list;
					}
					if($v['view_range'] == '2' && strlen($v['range_content']) > 0) {
						$tempContent = explode(',', $v['range_content']);
						foreach($tempContent AS $rangeContent) {
							$accountList[$rangeContent] = $rangeContent;
						}
					}
					if($v['view_range'] == '4' && strlen($v['range_content']) > 0) {
						$tempContent = explode(',', $v['range_content']);
						foreach($tempContent AS $rangeContent) {
							$divisionList[$rangeContent] = $rangeContent;
						}
					}
					if($v['view_range'] == '3' && strlen($v['range_content']) > 0) {
						$tempViewAccessList = array();
						$tempContent = explode(',', $v['range_content']);

						foreach($tempContent AS $tempInfo) {
							switch($tempInfo) {
								case '1':
									$myPersonLineList[$tempUid] = $tempUid;
									break;
								case '2':
									$myPersonDivisionList[$tempUid] = $tempUid;
									break;
							}
						}
					}
					if($v['view_range'] == '5' && strlen($v['range_content']) > 0) {
						$tempContent = explode(',', $v['range_content']);
						foreach($tempContent AS $rangeContent) {
							$productLineList[$rangeContent] = $rangeContent;
						}
					}
				}
			}
		}

		$subCondition = array();
		if(!empty($accountList)) {
			if(count($accountList) > 1) {
				$sqlParam = array();
				foreach($accountList AS $k => $v) {
					if($usebindParam) {
						$sqlParam[] = ':manager_view_materiel_alias_account_number_'.$k;
						$bindParam[':manager_view_materiel_alias_account_number_'.$k] = array($v, \PDO::PARAM_INT);
					} else {
						$sqlParam[] = $v;
					}
				}
				$subCondition[$alias.'erp_account'] = array('IN', $sqlParam);
			} else {
				if($usebindParam) {
					$subCondition[$alias.'erp_account'] = ':manager_view_materiel_alias_account_number';
					$bindParam[':manager_view_materiel_alias_account_number'] = array(array_pop($accountList), \PDO::PARAM_INT);
				} else {
					$subCondition[$alias.'erp_account'] = array_pop($accountList);
				}
			}
		}

		if(!empty($myPersonDivisionList)) {
			$tempMyPersonDivisionList = M('division')->where(array('person' => array('IN', $myPersonDivisionList)))->field('id')->select();
			if(!empty($tempMyPersonDivisionList)) {
				foreach($tempMyPersonDivisionList AS $tempValue) {
					$divisionList[$tempValue['id']] = $tempValue['id'];
				}
			}
		}

		if(!empty($divisionList)) {
			if(count($divisionList) > 1) {
				$tempCond = $tempParam = array();
				foreach($divisionList AS $k => $v) {
					$tempCond[] = ':division_'.$k;
					$tempParam[':division_'.$k] = array($v, \PDO::PARAM_INT);
				}
				$tempDivisionProlineList = M('materiel_proline_to_division')
				->where(array('did' => array('IN', $tempCond)))
				->bind($tempParam)
				->field('proline_id, category')
				->select();
			} else {
				$tempDivisionProlineList = M('materiel_proline_to_division')
				->where(array('did' => ':did'))
				->bind(array(':did' => array(array_pop($divisionList), \PDO::PARAM_INT)))
				->field('proline_id, category')
				->select();
			}

			if(!empty($tempDivisionProlineList)) {
				$sqlParam = array();
				foreach($tempDivisionProlineList AS $k => $v) {
					$tempDivisionCondition = array($alias.'pro_line' => ':materiel_view_access_division_'.$k);
					if($v['category'] == '2') {
						$tempDivisionCondition[$alias.'customized_type'] = array('IN', '0,1');
					} elseif($v['category'] == '3') {
						$tempDivisionCondition[$alias.'customized_type'] = '2';
					}
					$bindParam[':materiel_view_access_division_'.$k] = array($v['proline_id'], \PDO::PARAM_INT);
					$sqlParam[] = $tempDivisionCondition;
				}
				$sqlParam['_logic'] = 'OR';
				$subCondition[] = $sqlParam;
			}
		}

		if(!empty($myPersonLineList)) {
			$businessList = M('user_business')->alias('ub')
			->join('LEFT JOIN __BUSINESS__ b ON b.id=ub.business_id')
			->where(array('ub.user_id' => array('IN', $myPersonLineList), 'b.property' => 4))
			->field('extra')
			->select();

			if(!empty($businessList)) {
				$sqlParam = array();
				foreach($businessList AS $k => $tempValue) {
					if(empty($tempValue['extra'])) {
						continue;
					}

					$tempExtra = json_decode($tempValue['extra'], true);
					$tempProLineCondition = array($alias.'pro_line' => ':materiel_view_access_person_proline_'.$k);
					if($tempExtra['customized_type'] == '1') {
						$tempProLineCondition[$alias.'customized_type'] = array('IN', '0,1');
					} elseif($tempExtra['customized_type'] == '2') {
						$tempProLineCondition[$alias.'customized_type'] = '2';
					}
					$bindParam[':materiel_view_access_person_proline_'.$k] = array($tempExtra['product_line'], \PDO::PARAM_INT);
					$sqlParam[] = $tempProLineCondition;
				}
				$sqlParam['_logic'] = 'OR';
				$subCondition[] = $sqlParam;
			}
		}

		if(!empty($productLineList)) {
			if(count($productLineList) > 1) {
				$sqlParam = array();
				foreach($productLineList AS $k => $v) {
					if($usebindParam) {
						$sqlParam[] = ':manager_view_materiel_alias_proline_'.$k;
						$bindParam[':manager_view_materiel_alias_proline_'.$k] = array($v, \PDO::PARAM_INT);
					} else {
						$sqlParam[] = $v;
					}
				}
				$subCondition[$alias.'pro_line'] = array('IN', $sqlParam);
			} else {
				if($usebindParam) {
					$subCondition[$alias.'pro_line'] = ':manager_view_materiel_alias_proline';
					$bindParam[':manager_view_materiel_alias_proline'] = array(array_pop($productLineList), \PDO::PARAM_INT);
				} else {
					$subCondition[$alias.'pro_line'] = array_pop($productLineList);
				}
			}
		}

		if(empty($subCondition)) {
			$list['condition'][] = array($alias.'id' => '-1');
			return $list;
		}

		$subCondition['_logic'] = 'OR';
		$list['condition'][] = $subCondition;
		$bindParam && $list['bindParam'] = $bindParam;

		return $list;
	}

	/**
	 *	过滤产品线视图权限
	 *	$param $moduleName sting 当前使用的模块
	 *	$return 返回产品线id数组
	 */
	protected function getAllowAccessProlineIds($moduleName = CONTROLLER_NAME) {
		$condition = $bindParam = array();

		$viewAccess = $this->viewAccessList[strtoupper($moduleName)];
		//没有视图权限则无法访问数据
		if(!$this->needCheckAccess || empty($viewAccess)) {
			$tempProlineIds = M('materiel_proline')->where(array('company' => array('GT', 0)))->getField('id', true);
			$tempProlineIds[] = 0;
			return $tempProlineIds;
		}

		$productLineIds = $divisionList = $myPersonLineList = array();
		foreach($viewAccess AS $tempUid => $tempUserInfo) {
			foreach($tempUserInfo AS $tempType => $tempTypeInfo) {
				foreach($tempTypeInfo AS $v) {
					if($tempType != 5) {	//产品线类型为5
						continue;
					}
					if($v['view_range'] == '1') {
						$tempProlineIds = M('materiel_proline')->where(array('company' => array('GT', 0)))->getField('id', true);
						$tempProlineIds[] = 0;
						return $tempProlineIds;
					}
					if($v['view_range'] == '2' && strlen($v['range_content']) > 0) {
						$tempContent = explode(',', $v['range_content']);
						foreach($tempContent AS $rangeContent) {
							$productLineIds[$rangeContent] = $rangeContent;
						}
					}
					if($v['view_range'] == '4' && strlen($v['range_content']) > 0) {
						$tempContent = explode(',', $v['range_content']);
						foreach($tempContent AS $rangeContent) {
							$divisionList[$rangeContent] = $rangeContent;
						}
					}
					if($v['view_range'] == '3' && strlen($v['range_content']) > 0) {
						$tempViewAccessList = array();
						$tempContent = explode(',', $v['range_content']);
						foreach($tempContent AS $tempInfo) {
							switch($tempInfo) {
								case '1':
									$myPersonLineList[$tempUid] = $tempUid;
									break;
								case '2':
									$myPersonDivisionList[$tempUid] = $tempUid;
									break;
							}
						}
					}
				}
			}
		}

		if(!empty($myPersonLineList)) {
			$businessList = M('user_business')->alias('ub')
			->join('LEFT JOIN __BUSINESS__ b ON b.id=ub.business_id')
			->where(array('ub.user_id' => array('IN', $myPersonLineList), 'b.property' => 4))
			->field('extra')
			->select();

			if(!empty($businessList)) {
				$sqlParam = array();
				foreach($businessList AS $k => $tempValue) {
					if(empty($tempValue['extra'])) {
						continue;
					}

					$tempExtra = json_decode($tempValue['extra'], true);
					$productLineIds[$tempExtra['product_line']] = $tempExtra['product_line'];
				}
			}
		}

		if(!empty($myPersonDivisionList)) {
			$tempMyPersonDivisionList = M('division')->where(array('person' => array('IN', $myPersonDivisionList)))->field('id')->select();
			if(!empty($tempMyPersonDivisionList)) {
				foreach($tempMyPersonDivisionList AS $tempValue) {
					$divisionList[$tempValue['id']] = $tempValue['id'];
				}
			}
		}

		if(!empty($divisionList)) {
			$tempProlineIds = M('materiel_proline_to_division')->where(array('did' => array('IN', $divisionList)))->getField('proline_id', true);

			$productLineIds = !empty($tempProlineIds) ? array_merge($productLineIds, $tempProlineIds) : $productLineIds;
		}

		return array_unique($productLineIds);
	}

	/**
	 *	过滤产品线视图权限
	 *	$param $alias sting 产品线表别名，默认空，sql会以产品线表名作为字段前缀
	 *	$param $bindParam boolean 是否使用参数绑定
	 *	$return 返回产品线id数组
	 */
	protected function getViewManagerProductLine($alias = '', $usebindParam = false, $moduleName = CONTROLLER_NAME) {
		$alias = $alias ? $alias.'.' : '';
		$list = array('join' => array(), 'condition' => array(array($alias.'company' => array('GT', 0))));
		$usebindParam && $list['bindParam'] = array();
		if(!$this->needCheckAccess) {
			return $list;
		}

		//没有视图权限则无法访问数据
		$viewAccess = $this->viewAccessList[strtoupper($moduleName)];
		if(empty($viewAccess)) {
			$list['condition'][] = array($alias.'id' => '-1');
			return $list;
		}

		$bindParam = $productLineIds = $divisionList = $myPersonLineList = array();
		foreach($viewAccess AS $tempUid => $tempUserInfo) {
			foreach($tempUserInfo AS $tempType => $tempTypeInfo) {
				foreach($tempTypeInfo AS $v) {
					if($tempType != 5) {	//产品线类型为5
						continue;
					}
					if($v['view_range'] == '1') {
						return $list;
					}
					if($v['view_range'] == '2' && strlen($v['range_content']) > 0) {
						$tempContent = explode(',', $v['range_content']);
						foreach($tempContent AS $rangeContent) {
							$productLineIds[$rangeContent] = $rangeContent;
						}
					}
					if($v['view_range'] == '4' && strlen($v['range_content']) > 0) {
						$tempContent = explode(',', $v['range_content']);
						foreach($tempContent AS $rangeContent) {
							$divisionList[$rangeContent] = $rangeContent;
						}
					}
					if($v['view_range'] == '3' && strlen($v['range_content']) > 0) {
						$tempViewAccessList = array();
						$tempContent = explode(',', $v['range_content']);
						foreach($tempContent AS $tempInfo) {
							switch($tempInfo) {
								case '1':
									$myPersonLineList[$tempUid] = $tempUid;
									break;
								case '2':
									$myPersonDivisionList[$tempUid] = $tempUid;
									break;
							}
						}
					}
				}
			}
		}

		$subCondition = array();
		if(!empty($myPersonLineList)) {
			$businessList = M('user_business')->alias('ub')
			->join('LEFT JOIN __BUSINESS__ b ON b.id=ub.business_id')
			->where(array('ub.user_id' => array('IN', $myPersonLineList), 'b.property' => 4))
			->field('extra')
			->select();

			if(!empty($businessList)) {
				$sqlParam = array();
				foreach($businessList AS $k => $tempValue) {
					if(empty($tempValue['extra'])) {
						continue;
					}

					$tempExtra = json_decode($tempValue['extra'], true);
					$productLineIds[$tempExtra['product_line']] = $tempExtra['product_line'];
				}
			}
		}

		if(!empty($productLineIds)) {
			if(count($productLineIds) > 1) {
				$sqlParam = array();
				foreach($productLineIds AS $k => $v) {
					if($usebindParam) {
						$sqlParam[] = ':proline_view_access_id_'.$k;
						$bindParam[':proline_view_access_id_'.$k] = array($v, \PDO::PARAM_INT);
					} else {
						$sqlParam[] = $v;
					}
				}
				$subCondition[$alias.'id'] = array('IN', $sqlParam);
			} else {
				if($usebindParam) {
					$subCondition[$alias.'id'] = ':proline_view_access_id';
					$bindParam[':proline_view_access_id'] = array(array_pop($productLineIds), \PDO::PARAM_INT);
				} else {
					$subCondition[$alias.'id'] = array_pop($productLineIds);
				}
			}
		}

		if(!empty($myPersonDivisionList)) {
			$tempMyPersonDivisionList = M('division')->where(array('person' => array('IN', $myPersonDivisionList)))->field('id')->select();
			if(!empty($tempMyPersonDivisionList)) {
				foreach($tempMyPersonDivisionList AS $tempValue) {
					$divisionList[$tempValue['id']] = $tempValue['id'];
				}
			}
		}

		if(!empty($divisionList)) {
			$list['join'][] = 'LEFT JOIN __MATERIEL_PROLINE_TO_DIVISION__ AS prolineToDivisionAlias ON prolineToDivisionAlias.proline_id='.$alias.'id';
			if(count($divisionList) > 1) {
				$sqlParam = array();
				foreach($divisionList AS $k => $v) {
					if($usebindParam) {
						$sqlParam[] = ':proline_view_access_division_'.$k;
						$bindParam[':proline_view_access_division_'.$k] = array($v, \PDO::PARAM_INT);
					} else {
						$sqlParam[] = $v;
					}
				}
				$subCondition['prolineToDivisionAlias.did'] = array('IN', $sqlParam);
			} else {
				if($usebindParam) {
					$subCondition['prolineToDivisionAlias.did'] = ':proline_view_access_division';
					$bindParam[':proline_view_access_division'] = array(array_pop($divisionList), \PDO::PARAM_INT);
				} else {
					$subCondition['prolineToDivisionAlias.did'] = array_pop($divisionList);
				}
			}
		}

		if(empty($subCondition)) {
			$list['condition'][] = array($alias.'id' => '-1');
			return $list;
		}
		$subCondition['_logic'] = 'OR';
		$list['condition'][] = $subCondition;
		$bindParam && $list['bindParam'] = $bindParam;

		return $list;
	}

	/**
	 *	过滤客户视图权限
	 *	$param $alias sting 客户表别名，默认空，sql会以客户表名作为字段前缀
	 *	$param $bindParam boolean 是否使用参数绑定
	 *	$return 返回结果数组（未使用参数绑定：array('join' => array(), 'condition' => array())；使用参数绑定：array('join' => array(), 'condition' => array(), 'bindParam' => array())）
	 */
	protected function getViewManagerCustomer($alias = '', $usebindParam = false, $moduleName = CONTROLLER_NAME) {
		$alias = $alias ? $alias.'.' : '';
		$list = array('join' => array(), 'condition' => array(array($alias.'nickname' => array('NEQ', ''), $alias.'is_used' => '1')));
        $usebindParam && $list['bindParam'] = array();
        if(!$this->needCheckAccess) {
            return $list;
        }

        //没有视图权限则无法访问数据
		$viewAccess = $this->viewAccessList[strtoupper($moduleName)];
		if(empty($viewAccess)) {
			$list['condition'][] = array($alias.'id' => '-1');
			return $list;
		}

		$bindParam = $accountList = $divisionList = $myPersonList = $myTrackList = array();
		foreach($viewAccess AS $tempUid => $tempUserInfo) {
			foreach($tempUserInfo AS $tempType => $tempTypeInfo) {
				foreach($tempTypeInfo AS $v) {
					if($tempType != 6) {	//客户类型为6
						continue;
					}
					if($v['view_range'] == '1') {
						return $list;
					}
					if($v['view_range'] == '2' && strlen($v['range_content']) > 0) {
						$tempContent = explode(',', $v['range_content']);
						foreach($tempContent AS $rangeContent) {
							$accountList[$rangeContent] = $rangeContent;
						}
					}
					if($v['view_range'] == '4' && strlen($v['range_content']) > 0) {
						$tempContent = explode(',', $v['range_content']);
						foreach($tempContent AS $rangeContent) {
							$divisionList[$rangeContent] = $rangeContent;
						}
					}
					if($v['view_range'] == '3' && strlen($v['range_content']) > 0) {
						$tempViewAccessList = array();
						$tempContent = explode(',', $v['range_content']);
						foreach($tempContent AS $tempInfo) {
							switch($tempInfo) {
								case '1':
									$myPersonList[$tempUid] = $tempUid;
									break;
								case '2':
									$myTrackList[$tempUid] = $tempUid;
									break;
							}
						}
					}
				}
			}
		}

		$subCondition = array();
		if(!empty($accountList)) {
			if(count($accountList) > 1) {
				$sqlParam = array();
				foreach($accountList AS $k => $v) {
					if($usebindParam) {
						$sqlParam[] = ':manager_view_customer_alias_account_number_'.$k;
						$bindParam[':manager_view_customer_alias_account_number_'.$k] = array($v, \PDO::PARAM_INT);
					} else {
						$sqlParam[] = $v;
					}
				}
				$subCondition[$alias.'account_number'] = array('IN', $sqlParam);
			} else {
				if($usebindParam) {
					$subCondition[$alias.'account_number'] = ':manager_view_customer_alias_account_number';
					$bindParam[':manager_view_customer_alias_account_number'] = array(array_pop($accountList), \PDO::PARAM_INT);
				} else {
					$subCondition[$alias.'account_number'] = array_pop($accountList);
				}
			}
		}
		if(!empty($divisionList)) {
			$list['join'][] = 'LEFT JOIN __USER__  AS customerViewAccessUserAlias ON customerViewAccessUserAlias.user_id='.$alias.'user_id';
			if(count($divisionList) > 1) {
				$sqlParam = array();
				foreach($divisionList AS $k => $v) {
					if($usebindParam) {
						$sqlParam[] = ':customer_view_access_user_division_'.$k;
						$bindParam[':customer_view_access_user_division_'.$k] = array($v, \PDO::PARAM_INT);
					} else {
						$sqlParam[] = $v;
					}
				}
				$subCondition['customerViewAccessUserAlias.division'] = array('IN', $sqlParam);
			} else {
				if($usebindParam) {
					$subCondition['customerViewAccessUserAlias.division'] = ':customer_view_access_user_division';
					$bindParam[':customer_view_access_user_division'] = array(array_pop($divisionList), \PDO::PARAM_INT);
				} else {
					$subCondition['customerViewAccessUserAlias.division'] = array_pop($divisionList);
				}
			}
		}
		if(!empty($myPersonList)) {
			if(count($myPersonList) > 1) {
				$sqlParam = array();
				foreach($myPersonList AS $k => $v) {
					if($usebindParam) {
						$sqlParam[] = ':customer_view_access_saler_'.$k;
						$bindParam[':customer_view_access_saler_'.$k] = array($v, \PDO::PARAM_INT);
					} else {
						$sqlParam[] = $v;
					}
				}
				$subCondition[$alias.'user_id'] = array('IN', $sqlParam);
			} else {
				if($usebindParam) {
					$subCondition[$alias.'user_id'] = ':customer_view_access_saler';
					$bindParam[':customer_view_access_saler'] = array(array_pop($myPersonList), \PDO::PARAM_INT);
				} else {
					$subCondition[$alias.'user_id'] = array_pop($myPersonList);
				}
			}
		}
		if(!empty($myTrackList)) {
			if(count($myTrackList) > 1) {
				$sqlParam = array();
				foreach($myTrackList AS $k => $v) {
					if($usebindParam) {
						$sqlParam[] = ':customer_view_access_sales_assistant_'.$k;
						$bindParam[':customer_view_access_sales_assistant_'.$k] = array($v, \PDO::PARAM_INT);
					} else {
						$sqlParam[] = $v;
					}
				}
				$subCondition[$alias.'sales_assistant'] = array('IN', $sqlParam);
			} else {
				if($usebindParam) {
					$subCondition[$alias.'sales_assistant'] = ':customer_view_access_sales_assistant';
					$bindParam[':customer_view_access_sales_assistant'] = array(array_pop($myTrackList), \PDO::PARAM_INT);
				} else {
					$subCondition[$alias.'sales_assistant'] = array_pop($myTrackList);
				}
			}
		}

		if(empty($subCondition)) {
			$list['condition'][] = array($alias.'id' => '-1');
			return $list;
		}
		$subCondition['_logic'] = 'OR';
		$list['condition'][] = $subCondition;
		$bindParam && $list['bindParam'] = $bindParam;

		return $list;
	}

	/**
	 *	过滤供应商视图权限
	 *	$param $alias sting 供应商表别名，默认空，sql会以供应商表名作为字段前缀
	 *	$param $bindParam boolean 是否使用参数绑定
	 *	$return 返回结果数组（未使用参数绑定：array('join' => array(), 'condition' => array())；使用参数绑定：array('join' => array(), 'condition' => array(), 'bindParam' => array())）
	 */
	protected function getViewManagerSupplier($alias = '', $usebindParam = false, $moduleName = CONTROLLER_NAME) {
		$list = array('join' => array(), 'condition' => array());
		$usebindParam && $list['bindParam'] = array();

		if(!$this->needCheckAccess) {
			return $list;
		}

		$alias = $alias ? $alias.'.' : '';

		//没有视图权限则无法访问数据
		$viewAccess = $this->viewAccessList[strtoupper($moduleName)];
		if(empty($viewAccess)) {
			$list['condition'][] = array($alias.'id' => '-1');
			return $list;
		}

		$bindParam = $accountList = $myPersonList = array();
		foreach($viewAccess AS $tempUid => $tempUserInfo) {
			foreach($tempUserInfo AS $tempType => $tempTypeInfo) {
				foreach($tempTypeInfo AS $v) {
					if($tempType != 7) {	//供应商类型为7
						continue;
					}
					if($v['view_range'] == '1') {
						return $list;
					}
					if($v['view_range'] == '2' && strlen($v['range_content']) > 0) {
						$tempContent = explode(',', $v['range_content']);
						foreach($tempContent AS $rangeContent) {
							$accountList[$rangeContent] = $rangeContent;
						}
					}
					if($v['view_range'] == '3' && strlen($v['range_content']) > 0) {
						$tempViewAccessList = array();
						$tempContent = explode(',', $v['range_content']);
						foreach($tempContent AS $tempInfo) {
							switch($tempInfo) {
								case '1':
									$myPersonList[$tempUid] = $tempUid;
									break;
							}
						}
					}
				}
			}
		}

		$subCondition = array();
		if(!empty($accountList)) {
			if(count($accountList) > 1) {
				$sqlParam = array();
				foreach($accountList AS $k => $v) {
					if($usebindParam) {
						$sqlParam[] = ':manager_view_supplier_alias_account_number_'.$k;
						$bindParam[':manager_view_supplier_alias_account_number_'.$k] = array($v, \PDO::PARAM_INT);
					} else {
						$sqlParam[] = $v;
					}
				}
				$subCondition[$alias.'account_number'] = array('IN', $sqlParam);
			} else {
				if($usebindParam) {
					$subCondition[$alias.'account_number'] = ':manager_view_supplier_alias_account_number';
					$bindParam[':manager_view_supplier_alias_account_number'] = array(array_pop($accountList), \PDO::PARAM_INT);
				} else {
					$subCondition[$alias.'account_number'] = array_pop($accountList);
				}
			}
		}
		if(!empty($myPersonList)) {
			if(count($myPersonList) > 1) {
				$sqlParam = array();
				foreach($myPersonList AS $k => $v) {
					if($usebindParam) {
						$sqlParam[] = ':customer_view_access_buyer_'.$k;
						$bindParam[':customer_view_access_buyer_'.$k] = array($v, \PDO::PARAM_INT);
					} else {
						$sqlParam[] = $v;
					}
				}
				$subCondition[$alias.'salesman_id'] = array('IN', $sqlParam);
			} else {
				if($usebindParam) {
					$subCondition[$alias.'salesman_id'] = ':customer_view_access_buyer';
					$bindParam[':customer_view_access_buyer'] = array(array_pop($myPersonList), \PDO::PARAM_INT);
				} else {
					$subCondition[$alias.'salesman_id'] = array_pop($myPersonList);
				}
			}
		}

		if(empty($subCondition)) {
			$list['condition'][] = array($alias.'id' => '-1');
			return $list;
		}
		$subCondition['_logic'] = 'OR';
		$list['condition'][] = $subCondition;
		$bindParam && $list['bindParam'] = $bindParam;

		return $list;
	}

	/**
	 *	过滤用户视图权限
	 *	$param $alias sting 用户表别名，默认空，sql会以用户表名作为字段前缀
	 *	$param $bindParam boolean 是否使用参数绑定
	 *	$return 返回结果数组（未使用参数绑定：array('join' => array(), 'condition' => array())；使用参数绑定：array('join' => array(), 'condition' => array(), 'bindParam' => array())）
	 */
	protected function getViewManagerUser($alias = '', $usebindParam = false, $moduleName = CONTROLLER_NAME) {
		$list = array('join' => array(), 'condition' => array());
		$usebindParam && $list['bindParam'] = array();

		if(!$this->needCheckAccess) {
			return $list;
		}

		$alias = $alias ? $alias.'.' : '';

		//没有视图权限则无法访问数据
		$viewAccess = $this->viewAccessList[strtoupper($moduleName)];
		if(empty($viewAccess)) {
			$list['condition'][] = array($alias.'user_id' => '-1');
			return $list;
		}

		$bindParam = $deptList = $myPersonList = array();
		foreach($viewAccess AS $tempUid => $tempUserInfo) {
			foreach($tempUserInfo AS $tempType => $tempTypeInfo) {
				foreach($tempTypeInfo AS $v) {
					if($tempType != 8) {	//用户类型为8
						continue;
					}

					if($v['view_range'] == '1') {
						return $list;
					}
					if($v['view_range'] == '2' && strlen($v['range_content']) > 0) {	//所属部门
						$tempContent = explode(',', $v['range_content']);
						foreach($tempContent AS $rangeContent) {
							$deptList[$rangeContent] = $rangeContent;
						}
					}
					if($v['view_range'] == '3' && strlen($v['range_content']) > 0) {
						$tempViewAccessList = array();
						$tempContent = explode(',', $v['range_content']);
						foreach($tempContent AS $tempInfo) {
							switch($tempInfo) {
								case '1':
									$myPersonList[$tempUid] = $tempUid;
									break;
							}
						}
					}
				}				
			}
		}

		$subCondition = array();
		if(!empty($deptList)) {
			$list['join'][] = 'LEFT JOIN __DEPT__ AS userBelongDeptAlias ON userBelongDeptAlias.id='.$alias.'dept';
			$list['join'][] = 'LEFT JOIN __DEPT__ AS userBelongParentDeptAlias ON CONCAT(",", userBelongDeptAlias.path, ",") LIKE CONCAT("%,", userBelongParentDeptAlias.id, ",%")';
			if(count($deptList) > 1) {
				$sqlParam = array();
				foreach($deptList AS $k => $v) {
					if($usebindParam) {
						$sqlParam[] = ':manager_view_user_dept_alias_'.$k;
						$bindParam[':manager_view_user_dept_alias_'.$k] = array($v, \PDO::PARAM_INT);
					} else {
						$sqlParam[] = $v;
					}
				}
				$subCondition['userBelongParentDeptAlias.id'] = array('IN', $sqlParam);
			} else {
				if($usebindParam) {
					$subCondition['userBelongParentDeptAlias.id'] = ':manager_view_user_dept_alias';
					$bindParam[':manager_view_user_dept_alias'] = array(array_pop($deptList), \PDO::PARAM_INT);
				} else {
					$subCondition['userBelongParentDeptAlias.id'] = array_pop($deptList);
				}
			}
		}
		if(!empty($myPersonList)) {
			$list['join'][] = 'LEFT JOIN __USER__ AS parentUserAlias ON CONCAT(",", '.$alias.'path, ",") LIKE CONCAT("%,", parentUserAlias.user_id, ",%")';
			if(count($myPersonList) > 1) {
				$sqlParam = array();
				foreach($myPersonList AS $k => $v) {
					if($usebindParam) {
						$sqlParam[] = ':manager_view_parent_user_alias_'.$k;
						$bindParam[':manager_view_parent_user_alias_'.$k] = array($v, \PDO::PARAM_INT);
					} else {
						$sqlParam[] = $v;
					}
				}
				$subCondition['parentUserAlias.user_id'] = array('IN', $sqlParam);
			} else {
				if($usebindParam) {
					$subCondition['parentUserAlias.user_id'] = ':manager_view_parent_user_alias';
					$bindParam[':manager_view_parent_user_alias'] = array(array_pop($myPersonList), \PDO::PARAM_INT);
				} else {
					$subCondition['parentUserAlias.user_id'] = array_pop($myPersonList);
				}
			}
		}

		if(empty($subCondition)) {
			$list['condition'][] = array($alias.'user_id' => '-1');
			return $list;
		}

		$subCondition['_logic'] = 'OR';
		$list['condition'][] = $subCondition;
		$bindParam && $list['bindParam'] = $bindParam;

		return $list;
	}

	/**
     +----------------------------------------------------------
     * 根据研发任务视图权限生成相应的where条件
     +----------------------------------------------------------
	 * @param string $alias 多表联查情况下设置的表别名
     +----------------------------------------------------------
	 * @return array
     +----------------------------------------------------------
     */
	protected function taskManagerViewSql($alias = '') {
		$list = array('join' => array(), 'condition' => array());
		if(!$this->needCheckAccess) {
			return $list;
		}
		$alias = $alias ? $alias.'.' : '';

		//没有视图权限则无法访问数据
		$viewAccess = $this->viewAccessList['TASK'];
		if(empty($viewAccess)) {
			$list['condition'][] = array($alias.'id' => '-1');
			return $list;
		}

		$viewList = array();
		foreach($viewAccess AS $tempUid => $tempUserInfo) {
			foreach($tempUserInfo AS $tempType => $tempTypeInfo) {
				foreach($tempTypeInfo AS $v) {
					if($tempType != 3) {
						continue;
					}
					if($v['view_range'] == '1') {
						return $list;
					}
					if($v['view_range'] == '3' && strlen($v['range_content']) > 0) {
						$tempContent = explode(',', $v['range_content']);
						foreach($tempContent AS $rangeContent) {
							$viewList[$rangeContent][$tempUid] = $tempUid;
						}
					}
				}
			}
		}

		$condition = array();
		foreach($viewList AS $tempUserType => $val) {
			switch($tempUserType) {
				case '1':
					$condition[$alias.'create_by'] = count($val) > 1 ? array('IN', $val) : array_pop($val);
					break;
				case '2':
					$condition[$alias.'finished_by'] = count($val) > 1 ? array('IN', $val) : array_pop($val);
					break;
				case '3':
					$condition[$alias.'assign'] = count($val) > 1 ? array('IN', $val) : array_pop($val);
					break;
				case '4':
					$condition[$alias.'cancel_by'] = count($val) > 1 ? array('IN', $val) : array_pop($val);
					break;
				case '5':
					$condition[$alias.'closed_by'] = count($val) > 1 ? array('IN', $val) : array_pop($val);
					break;
				case '6':
					$condition[$alias.'original_demand_create_by'] = count($val) > 1 ? array('IN', $val) : array_pop($val);
					break;
				case '7':
					$condition[$alias.'related_create_by'] = count($val) > 1 ? array('IN', $val) : array_pop($val);
					break;
				case '8':
					if(count($val) > 1) {
						$subReviewSql = array();
						foreach($val AS $tempUid) {
							$subReviewSql[] = array('CONCAT(",", '.$alias.'mail_to, ",")' => array('LIKE', '%,'.$tempUid.',%'));
						}
						$subReviewSql['_logic'] = 'OR';
						$condition[] = $subReviewSql;
					} else {
						$tempUid = array_pop($val);
						$condition['CONCAT(",", '.$alias.'mail_to, ",")'] = array('LIKE', '%,'.$tempUid.',%');
					}
					break;
				case '9':
					$list['join'][] = 'LEFT JOIN __DEMAND__ manager_mail_demand_alias ON '.$alias.'related_id = manager_mail_demand_alias.id AND '.$alias.'related_type=1';
					$list['join'][] = 'LEFT JOIN __BUG__ manager_mail_bug_alias ON '.$alias.'related_id = manager_mail_bug_alias.id AND '.$alias.'related_type=2';
					if(count($val) > 1) {
						$subReviewSql = array();
						foreach($val AS $tempUid) {
							$subReviewSql[] = array('CONCAT(",", manager_mail_demand_alias.mail_to, ",")' => array('LIKE', '%,'.$tempUid.',%'));
							$subReviewSql[] = array('CONCAT(",", manager_mail_bug_alias.mail_to, ",")' => array('LIKE', '%,'.$tempUid.',%'));
						}
						$subReviewSql['_logic'] = 'OR';
						$condition[] = $subReviewSql;
					} else {
						$tempUid = array_pop($val);
						$condition['CONCAT(",", manager_mail_demand_alias.mail_to, ",")'] = array('LIKE', '%,'.$tempUid.',%');
						$condition['CONCAT(",", manager_mail_bug_alias.mail_to, ",")'] = array('LIKE', '%,'.$tempUid.',%');
					}
					break;
				case '10':
					$list['join'][] = 'LEFT JOIN __DEMAND__ manager_reviewed_demand_alias ON '.$alias.'related_id = manager_reviewed_demand_alias.id AND '.$alias.'related_type=1';
					$list['join'][] = 'LEFT JOIN __BUG__ manager_reviewed_bug_alias ON '.$alias.'related_id = manager_reviewed_bug_alias.id AND '.$alias.'related_type=2';
					if(count($val) > 1) {
						$subReviewSql = array();
						foreach($val AS $tempUid) {
							$subReviewSql[] = array('CONCAT(",", manager_reviewed_demand_alias.reviewed_by, ",")' => array('LIKE', '%,'.$tempUid.',%'));
							$subReviewSql[] = array('CONCAT(",", manager_reviewed_bug_alias.reviewed_by, ",")' => array('LIKE', '%,'.$tempUid.',%'));
						}
						$subReviewSql['_logic'] = 'OR';
						$condition[] = $subReviewSql;
					} else {
						$tempUid = array_pop($val);
						$condition['CONCAT(",", manager_reviewed_demand_alias.reviewed_by, ",")'] = array('LIKE', '%,'.$tempUid.',%');
						$condition['CONCAT(",", manager_reviewed_bug_alias.reviewed_by, ",")'] = array('LIKE', '%,'.$tempUid.',%');
					}
					break;
			}
		}

		if(empty($condition)) {
			$list['condition'][] = array($alias.'id' => '-1');
		} elseif(count($condition) > 1) {
			$condition['_logic'] = 'OR';
			$list['condition'][] = $condition;
		}

		return $list;
	}

	/**
     +----------------------------------------------------------
     * 根据研发需求视图权限生成相应的where条件
     +----------------------------------------------------------
	 * @param string $alias 多表联查情况下设置的表别名
     +----------------------------------------------------------
	 * @return array
     +----------------------------------------------------------
     */
	protected function demandManagerViewSql($alias = '') {
		$condition = array();
		if(!$this->needCheckAccess) {
			return $condition;
		}
		$alias = $alias ? $alias.'.' : '';

		//没有视图权限则无法访问数据
		$viewAccess = $this->viewAccessList['DEMAND'];
		if(empty($viewAccess)) {
			$condition[] = array($alias.'id' => '-1');
			return $condition;
		}

		$viewList = array();
		foreach($viewAccess AS $tempUid => $tempUserInfo) {
			foreach($tempUserInfo AS $tempType => $tempTypeInfo) {
				foreach($tempTypeInfo AS $v) {
					if($tempType != 2) {
						continue;
					}
					if($v['view_range'] == '1') {
						return $condition;
					}
					if($v['view_range'] == '3' && strlen($v['range_content']) > 0) {
						$tempContent = explode(',', $v['range_content']);
						foreach($tempContent AS $rangeContent) {
							$viewList[$rangeContent][$tempUid] = $tempUid;
						}
					}
				}
			}
		}

		foreach($viewList AS $tempUserType => $val) {
			switch($tempUserType) {
				case '1':
					$condition[$alias.'create_by'] = count($val) > 1 ? array('IN', $val) : array_pop($val);
					break;
				case '2':
					$condition[$alias.'assign'] = count($val) > 1 ? array('IN', $val) : array_pop($val);
					break;
				case '3':
					$condition[$alias.'closed_by'] = count($val) > 1 ? array('IN', $val) : array_pop($val);
					break;
				case '4':
					$condition[$alias.'original_demand_create_by'] = count($val) > 1 ? array('IN', $val) : array_pop($val);
					break;
				case '5':
					if(count($val) > 1) {
						$subReviewSql = array();
						foreach($val AS $tempUid) {
							$subReviewSql[] = array('CONCAT(",", '.$alias.'reviewed_by, ",")' => array('LIKE', '%,'.$tempUid.',%'));
						}
						$subReviewSql['_logic'] = 'OR';
						$condition[] = $subReviewSql;
					} else {
						$tempUid = array_pop($val);
						$condition['CONCAT(",", '.$alias.'reviewed_by, ",")'] = array('LIKE', '%,'.$tempUid.',%');
					}
					break;
				case '6':
					if(count($val) > 1) {
						$subReviewSql = array();
						foreach($val AS $tempUid) {
							$subReviewSql[] = array('CONCAT(",", '.$alias.'mail_to, ",")' => array('LIKE', '%,'.$tempUid.',%'));
						}
						$subReviewSql['_logic'] = 'OR';
						$condition[] = $subReviewSql;
					} else {
						$tempUid = array_pop($val);
						$condition['CONCAT(",", '.$alias.'mail_to, ",")'] = array('LIKE', '%,'.$tempUid.',%');
					}
					break;
			}
		}

		if(empty($condition)) {
			$condition[] = array($alias.'id' => '-1');
			return $condition;
		} elseif(count($condition) > 1) {
			$condition['_logic'] = 'OR';
		}

		return array($condition);
	}

	/**
     +----------------------------------------------------------
     * 根据研发BUG视图权限生成相应的where条件
     +----------------------------------------------------------
	 * @param string $alias 多表联查情况下设置的表别名
     +----------------------------------------------------------
	 * @return array
     +----------------------------------------------------------
     */
	protected function bugManagerViewSql($alias = '') {
		$condition = array();
		if(!$this->needCheckAccess) {
			return $condition;
		}

		$alias = $alias ? $alias.'.' : '';

		//没有视图权限则无法访问数据
		$viewAccess = $this->viewAccessList['BUG'];
		if(empty($viewAccess)) {
			$condition[] = array($alias.'id' => '-1');
			return $condition;
		}

		$viewList = array();
		foreach($viewAccess AS $tempUid => $tempUserInfo) {
			foreach($tempUserInfo AS $tempType => $tempTypeInfo) {
				foreach($tempTypeInfo AS $v) {
					if($tempType != 10) {
						continue;
					}
					if($v['view_range'] == '1') {
						return $condition;
					}
					if($v['view_range'] == '3' && strlen($v['range_content']) > 0) {
						$tempContent = explode(',', $v['range_content']);
						foreach($tempContent AS $rangeContent) {
							$viewList[$rangeContent][$tempUid] = $tempUid;
						}
					}
				}
			}
		}

		foreach($viewList AS $tempUserType => $val) {
			switch($tempUserType) {
				case '1':
					$condition[$alias.'create_by'] = count($val) > 1 ? array('IN', $val) : array_pop($val);
					break;
				case '2':
					$condition[$alias.'assign'] = count($val) > 1 ? array('IN', $val) : array_pop($val);
					break;
				case '3':
					$condition[$alias.'closed_by'] = count($val) > 1 ? array('IN', $val) : array_pop($val);
					break;
				case '4':
					$condition[$alias.'original_demand_create_by'] = count($val) > 1 ? array('IN', $val) : array_pop($val);
					break;
				case '5':
					$condition[$alias.'demand_create_by'] = count($val) > 1 ? array('IN', $val) : array_pop($val);
					break;
				case '6':
					if(count($val) > 1) {
						$subReviewSql = array();
						foreach($val AS $tempUid) {
							$subReviewSql[] = array('CONCAT(",", '.$alias.'reviewed_by, ",")' => array('LIKE', '%,'.$tempUid.',%'));
						}
						$subReviewSql['_logic'] = 'OR';
						$condition[] = $subReviewSql;
					} else {
						$tempUid = array_pop($val);
						$condition['CONCAT(",", '.$alias.'reviewed_by, ",")'] = array('LIKE', '%,'.$tempUid.',%');
					}
					break;
				case '6':
					if(count($val) > 1) {
						$subReviewSql = array();
						foreach($val AS $tempUid) {
							$subReviewSql[] = array('CONCAT(",", '.$alias.'mail_to, ",")' => array('LIKE', '%,'.$tempUid.',%'));
						}
						$subReviewSql['_logic'] = 'OR';
						$condition[] = $subReviewSql;
					} else {
						$tempUid = array_pop($val);
						$condition['CONCAT(",", '.$alias.'mail_to, ",")'] = array('LIKE', '%,'.$tempUid.',%');
					}
					break;
			}
		}

		if(empty($condition)) {
			$condition[] = array($alias.'id' => '-1');
			return $condition;
		} elseif(count($condition) > 1) {
			$condition['_logic'] = 'OR';
		}

		return array($condition);
	}

	/**
     +----------------------------------------------------------
     * 根据原始需求视图权限生成相应的where条件
     +----------------------------------------------------------
	 * @param string $alias 多表联查情况下设置的表别名
     +----------------------------------------------------------
	 * @return array
     +----------------------------------------------------------
     */
	protected function originalDemandManagerViewSql($alias = '') {
		$condition = array();
		if(!$this->needCheckAccess) {
			return $condition;
		}
		$alias = $alias ? $alias.'.' : '';

		//没有视图权限则无法访问数据
		$viewAccess = $this->viewAccessList['ORIGINALDEMAND'];
		if(empty($viewAccess)) {
			$condition[] = array($alias.'id' => '-1');
			return $condition;
		}

		$viewList = array();
		foreach($viewAccess AS $tempUid => $tempUserInfo) {
			foreach($tempUserInfo AS $tempType => $tempTypeInfo) {
				foreach($tempTypeInfo AS $v) {
					if($tempType != 4) {
						continue;
					}
					if($v['view_range'] == '1') {
						return $condition;
					}
					if($v['view_range'] == '3' && strlen($v['range_content']) > 0) {
						$tempContent = explode(',', $v['range_content']);
						foreach($tempContent AS $rangeContent) {
							$viewList[$rangeContent][$tempUid] = $tempUid;
						}
					}
				}
			}
		}

		foreach($viewList AS $tempUserType => $val) {
			switch($tempUserType) {
				case '1':
					$condition[$alias.'create_by'] = count($val) > 1 ? array('IN', $val) : array_pop($val);
					break;
				case '2':
					$condition[$alias.'assign'] = count($val) > 1 ? array('IN', $val) : array_pop($val);
					break;
				case '3':
					$condition[$alias.'check_by'] = count($val) > 1 ? array('IN', $val) : array_pop($val);
					break;
			}
		}

		if(empty($condition)) {
			$condition[] = array($alias.'id' => '-1');
			return $condition;
		} elseif(count($condition) > 1) {
			$condition['_logic'] = 'OR';
		}

		return array($condition);
	}

	/**
     +----------------------------------------------------------
     * 根据相关视图权限生成相应的where条件
     +----------------------------------------------------------
     * @param string $object 视图权限限制对象（任务:task 需求:demand bug:bug 原始需求:original_demand）
     +----------------------------------------------------------
	 * @param string $alias 多表联查情况下设置的表别名
     +----------------------------------------------------------
	 * @return array
     +----------------------------------------------------------
     */
	/*protected function managerViewSql($object, $alias = '') {
		if(!$this->roleId || !$this->needCheckAccess || !in_array($object, array('task', 'demand', 'bug', 'original_demand', 'user', 'attendance', 'ask_leave', 'change_rest', 'later_sign_in'))) {
			return array();
		}
		
		$join = $condition = array();

		$acl = M('role')->where(array('id' => $this->roleId))->getField('acl');
		!empty($acl) && $viewAccess = json_decode($acl, true);
		
		if(empty($viewAccess[$object]) && empty($viewAccess['user']) && empty($viewAccess['attendance_report'])) {
			return array();
		}

		$checkView = array(
			'demand'			=>	array('1', '2', '3', '7', '9', '10'),
			'task'				=>	array('1', '2', '3', '7', '5', '4', '6', '10', '11', '12'),
			'bug'				=>	array('1', '2', '3', '7', '6', '9', '10'),
			'original_demand'	=>	array('1', '2', '3', '8')
		);

		$real_manageView_data = array_intersect($viewAccess[$object], $checkView[$object]);
		
		$alias = $alias ? $alias.'.' : '';
		
		foreach($real_manageView_data as $val) {
			switch($val) {
				case '1':
					$condition[$alias.'create_by'] = $this->uid;
					break;
				case '5':
					$condition[$alias.'finished_by'] = $this->uid;
					break;
				case '2':
					$condition[$alias.'assign'] = $this->uid;
					break;
				case '4':
					$condition[$alias.'cancel_by'] = $this->uid;
					break;
				case '3':
					$condition[$alias.'closed_by'] = $this->uid;
					break;
				case '6':
					if($object == 'task') {
						$condition[$alias.'related_create_by'] = $this->uid;
					} elseif($object == 'bug') {
						$condition[$alias.'demand_create_by'] = $this->uid;
					}
					break;
				case '7':
					$condition[$alias.'original_demand_create_by'] = $this->uid;
					break;
				case '8':
					$condition[$alias.'check_by'] = $this->uid;
					break;
				case '9':
					$condition['CONCAT(",", '.$alias.'reviewed_by, ",")'] = array('LIKE', '%,'.$this->uid.',%');
					break;
				case '10':
					$condition['CONCAT(",", '.$alias.'mail_to, ",")'] = array('LIKE', '%,'.$this->uid.',%');
					break;
				case '11':
					$join[] = 'LEFT JOIN __DEMAND__ manager_mail_demand_alias ON '.$alias.'related_id = manager_mail_demand_alias.id AND '.$alias.'related_type=1';
					$condition['CONCAT(",", manager_mail_demand_alias.mail_to, ",")'] = array('LIKE', '%,'.$this->uid.',%');
					
					$join[] = 'LEFT JOIN __BUG__ manager_mail_bug_alias ON '.$alias.'related_id = manager_mail_bug_alias.id AND '.$alias.'related_type=2';
					$condition['CONCAT(",", manager_mail_bug_alias.mail_to, ",")'] = array('LIKE', '%,'.$this->uid.',%');
					break;
				case '12':
					$join[] = 'LEFT JOIN __DEMAND__ manager_reviewed_demand_alias ON '.$alias.'related_id = manager_reviewed_demand_alias.id AND '.$alias.'related_type=1';
					$condition['CONCAT(",", manager_reviewed_demand_alias.reviewed_by, ",")'] = array('LIKE', '%,'.$this->uid.',%');
					
					$join[] = 'LEFT JOIN __BUG__ manager_reviewed_bug_alias ON '.$alias.'related_id = manager_reviewed_bug_alias.id AND '.$alias.'related_type=2';
					$condition['CONCAT(",", manager_reviewed_bug_alias.reviewed_by, ",")'] = array('LIKE', '%,'.$this->uid.',%');
					break;
			}
		}
		
		$userObject = array('user', 'attendance', 'ask_leave', 'change_rest', 'later_sign_in');
		if(in_array($object, $userObject)) {
			$checkViewObj = $object == 'user' ? 'user' : 'attendance_report';
			$viewData = isset($viewAccess[$checkViewObj]) ? $viewAccess[$checkViewObj] : array();
			if(!empty($viewData)) {
				$where = '';
				foreach($viewData as $val) {
					if($val) {
						$where && $where .= ' OR';
						$where .= " CONCAT(',', ".$alias."path, ',') LIKE ',".$val.",%'";
					}
				}
				if(!empty($where)) {
					$condition['_string'] = $where;
				}
			}
		}
		
		if(empty($join)){
			if(empty($condition)) {
				return array();
			} elseif(count($condition) > 1) {
				$condition['_logic'] = 'OR';
				return array('_complex' => $condition);
			} elseif(count($condition) == 1) {
				return $condition;
			}
		}else{
			$list = array('join' => array(), 'condition' => array());
			if(count($condition) > 1) {
				$condition['_logic'] = 'OR';
				$list['condition'] = array('_complex' => $condition);
			} elseif(count($condition) == 1) {
				$list['condition'] = $condition;
			}
			$list['join'] = $join;

			return $list;
		}
	}*/

	/**
	 *	过滤任务视图权限
	 *	$param $alias sting 任务主表别名，默认空，sql会以主表表名作为字段前缀
	 *	$param $bindParam boolean 是否使用参数绑定
	 *	$return 返回结果数组（未使用参数绑定：array('join' => array(), 'condition' => array())；使用参数绑定：array('join' => array(), 'condition' => array(), 'bindParam' => array())）
	 */
	protected function getManagerView($alias = '', $usebindParam = false, $moduleName = CONTROLLER_NAME) {
		$list = array('join' => array(), 'condition' => array());
		$usebindParam && $list['bindParam'] = array();

		if(!$this->needCheckAccess) {
			return $list;
		}

		$alias = $alias ? $alias.'.' : '';
		//没有视图权限则无法访问数据
		$viewAccess = $this->viewAccessList[strtoupper($moduleName)];
		if(empty($viewAccess)) {
			$list['condition'][] = array($alias.'id' => '-1');
			return $list;
		}

		$whereSql = $bindParam = array();
		$uidKey = 0;
		foreach($viewAccess AS $tempUid => $tempUserAccessInfo) {
			$uidKey++;
			foreach($tempUserAccessInfo AS $tempType => $tempTypeInfo) {
				foreach($tempTypeInfo AS $tempInfo) {
					if($tempType != 1) {
						continue;
					}

					if($tempInfo['view_range'] == '1') {
						return $list;
					}

					if($tempInfo['view_range'] == '3' && strlen($tempInfo['range_content']) > 0) {
						$tempViewAccessList = array();
						$tempContent = explode(',', $tempInfo['range_content']);
						foreach($tempContent AS $v) {
							$tempViewAccessList[$v] = $v;
						}

						if($tempViewAccessList) {
							$subWhereSql = array();
							$typeIds = array();
							if($usebindParam) {
								$subWhereSql['workViewAccessAlias.uid'] = ':workViewAccessUidAlias_'.$uidKey;
								$bindParam[':workViewAccessUidAlias_'.$uidKey] = array($tempUid, \PDO::PARAM_INT);
								foreach($tempViewAccessList as $v) {
									$typeIds[] = ':type_'.$uidKey.'_'.$v;
									$bindParam[':type_'.$uidKey.'_'.$v] = array($v, \PDO::PARAM_INT);
								}
							} else {
								$subWhereSql['workViewAccessAlias.uid'] = $tempUid;
								$typeIds = $tempViewAccessList;
							}
							$subWhereSql['workViewAccessAlias.type'] = array('IN', $typeIds);
							$whereSql[] = $subWhereSql;
						}
					}
				}
			}
		}

		if(empty($whereSql)) {
			$list['condition'][] = array($alias.'id' => '-1');
			return $list;
		} elseif(count($whereSql) > 1) {
			$whereSql['_logic'] = 'OR';
		}

		$list['join'][] = 'LEFT JOIN __WORK_VIEW_ACCESS__ workViewAccessAlias ON workViewAccessAlias.wid='.$alias.'id';
		$list['condition'][] = $whereSql;
		$usebindParam && $list['bindParam'] = $bindParam;

		return $list;
	}

	/**
	 *	根据输入的用户和节点检测当前用户是否允许访问
	 *	$param $uids int||array 获取任务当前允许访问的用户
	 *	$param $isLimitAccess bollean 是否限制访问（只允许任务允许的人以及相关委托人）默认是
	 *	$param $controllerName string 控制器 默认当前操作控制器
	 *	$param $actionName string 操作方法 默认当前操作控制器
	 *	$return bollean 允许则为true,否则false
	 */
	protected function checkIsAllowAccess($uids, $isLimitAccess = true, $controllerName = CONTROLLER_NAME, $actionName = ACTION_NAME) {
		if(empty($uids)) {
			return false;
		}

		if(!$this->needCheckAccess && !$isLimitAccess) {
			return true;
		}

		if(!is_array($uids)) {
			$uids = array($uids);
		}
		if(in_array($this->uid, $uids)) {
			return true;
		}

		if(!isset($this->nodeAccessList[strtoupper(MODULE_NAME)][strtoupper($controllerName)][strtoupper($actionName)])){
			return false;
		}

		if(array_intersect($uids, $this->nodeAccessList[strtoupper(MODULE_NAME)][strtoupper($controllerName)][strtoupper($actionName)]['user_id'])) {
			return true;
		}

		return false;
	}

	/**
	 *	根据输入的用户和节点检测当前用户能操作该节点的用户列表
	 *	$param $uids int||array 获取任务当前允许访问的用户
	 *	$param $isLimitAccess bollean 是否限制访问（只允许任务允许的人以及相关委托人）默认否
	 *	$param $controllerName string 控制器 默认当前操作控制器
	 *	$param $actionName string 操作方法 默认当前操作控制器
	 *	$return array 允许访问的用户
	 */
	protected function getNodeEntrustUser($uids, $isLimitAccess = false, $controllerName = CONTROLLER_NAME, $actionName = ACTION_NAME) {
		$allowUserList = array();

		if(!$this->needCheckAccess && !$isLimitAccess) {
			$allowUserList[$this->uid] = $this->uid;
		}

		if(empty($uids)) {
			return $allowUserList;
		} elseif(!is_array($uids)) {
			$uids = array($uids);
		}
		if(in_array($this->uid, $uids)) {
			$allowUserList[$this->uid] = $this->uid;
		}

		if(!isset($this->nodeAccessList[strtoupper(MODULE_NAME)][strtoupper($controllerName)][strtoupper($actionName)])) {
			return $allowUserList;
		}

		$tempUids = array_intersect($uids, $this->nodeAccessList[strtoupper(MODULE_NAME)][strtoupper($controllerName)][strtoupper($actionName)]['user_id']);
		if(!empty($tempUids)) {
			foreach($tempUids AS $v) {
				$allowUserList[$v] = $v;
			}
		}

		return $allowUserList;
	}

	/**
     +----------------------------------------------------------
     * 根据传入的检测类型检测相关字段
     +----------------------------------------------------------
     * @param array $data 要检测的数据
     +----------------------------------------------------------
	 * @return array
     +----------------------------------------------------------
     */
	protected function checkFormField($data) {
		$result = array('error' => '', 'msg' => '', 'tips_id' => '');
		$error = false;

		if(empty($data)) {
			return $result;
		}

		foreach($data as $key => $val) {
			switch($val['type']) {
				//不能为空
				case 'mustInput':
					strlen($val['value']) <= 0 &&  $error = true;
					break;
				//检测两个值是否相等
				case 'mustEqualTo':
					strlen($val['value']) > 0 && strlen($val['extend']) > 0 && $val['extend'] != $val['value'] && $error = true;
					break;
				//必须email格式
				case 'mustEmail':
					strlen($val['value']) > 0 && !preg_match('/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/i', $val['value']) && $error = true;
					break;
				//长度必须大于
				case 'lengthMustMoreThan':
					strlen($val['value']) > 0 && strlen($val['value']) <= $val['extend'] && $error = true;
					break;
				//长度必须不大于
				case 'lengthNotMoreThan':
					strlen($val['value']) > 0 && strlen($val['value']) > $val['extend'] && $error = true;
					break;
				//长度必须小于
				case 'lengthMustLessThan':
					strlen($val['value']) > 0 && strlen($val['value']) >= $val['extend'] && $error = true;
					break;
				//长度必须不小于
				case 'lengthNotLessThan':
					strlen($val['value']) > 0 && strlen($val['value']) < $val['extend'] && $error = true;
					break;
				//必须为数字
				case 'mustNum':
					strlen($val['value']) > 0 && !is_numeric($val['value']) && $error = true;
					break;
				//值必须大于
				case 'mustMoreThan':
					strlen($val['value']) > 0 && $val['value'] <= $val['extend'] && $error = true;
					break;
				//值必须不大于
				case 'notMoreThan':
					strlen($val['value']) > 0 && $val['value'] > $val['extend'] && $error = true;
					break;
				//值必须小于
				case 'mustLessThan':
					strlen($val['value']) > 0 && $val['value'] >= $val['extend'] && $error = true;
					break;
				//值必须不小于
				case 'notLessThan':
					strlen($val['value']) > 0 && $val['value'] < $val['extend'] && $error = true;
					break;
				//值必须为整数
				case 'mustIntNum':
					strlen($val['value']) > 0 && (!is_numeric($val['value']) || strpos($val['value'], '.') !== false) && $error = true;
					break;
				//必须为url格式
				case 'mustUrl':
					!preg_match('/(http[s]?:\/\/)?[a-zA-Z0-9-]+(\.[a-zA-Z0-9]+)+/i', $val['value']) && $error = true;
					break;
				//由字母数字和下划线组成
				case 'mustLetter':
					strlen($val['value']) > 0 && !preg_match('/^\w+$/i', $val['value']) && $error = true;
					break;
				//检测手机号码格式
				case 'telephone':
					strlen($val['value']) > 0 && !preg_match('/^0?1((3|8|7)[0-9]|5[0-35-9]|4[579])\d{8}$/i', $val['value']) && $error = true;
					break;
				//检测固定电话格式
				case 'phone':
					strlen($val['value']) > 0 && !preg_match('/^\d{3}-\d{8}|\d{4}-\d{7,8}$/i', $val['value']) && $error = true;
					break;
				case 'mustQQ':
					strlen($val['value']) > 0 && !preg_match('/^[1-9][0-9]{4,}$/', $val['value']) && $error = true;
					break;
				case 'function':
					if(function_exists($val['extend'])) {
						if(!$val['extend']($val['value'])) {
							$error = true;
						}
					}
					break;
			}

			if($error) {
				break;
			}
		}

		$error && $result = array('error' => '1', 'msg' => $val['msg'], 'tips_id' => $val['tips_id']);
		return $result;
	}

	/**
     +----------------------------------------------------------
     * 发送邮件
     +----------------------------------------------------------
     * @param intval $objectId 要发送邮件的数据对象ID
     +----------------------------------------------------------
	 * @param string $name 要发送的数据对象名称
     +----------------------------------------------------------
	 * @param intval $actionLogId 要发送的操作记录id
     +----------------------------------------------------------
	 * @param intval $assign 指派给的用户id
     +----------------------------------------------------------
	 * @param intval $content 要发送的数据描述信息 for example:array(array('title' => '任务描述', 'desc' => '描述内容'))
     +----------------------------------------------------------
	 * @param string $url 要发送的对象的url地址
     +----------------------------------------------------------
	 * @param boolean $remind 是否对指派人发送邮件提醒
     +----------------------------------------------------------
	 * @param array $cc 要抄送的用户 for example：array(1,2,3)
     +----------------------------------------------------------
	 * @param: $attachment[array]	邮件中带附件	example:array(array('id' => '附件1ID', 'name' => '附件1', 'size' => '附件1大小'), array('id' => '附件2ID', 'name' => '附件2', 'size' => '附件2大小'))
     +----------------------------------------------------------
	 * @param string $abnormalMsg 异常信息，默认为空（如果为空则表示该邮件为普通邮件）
     +----------------------------------------------------------
	 * @param string $isOverdueFeedback 是否为超期反馈邮件
     +----------------------------------------------------------
	 * @return array
     +----------------------------------------------------------
     */
	protected function sendMail($objectId, $name, $actionLogId, $assign, $content, $url, $remind = false, $cc = array(), $attachment = array(), $abnormalMsg = '', $isOverdueFeedback = false) {
		$remindId = 0;
		if(!empty($assign) && $remind) {
			$remindId = $assign;
		} elseif(!empty($cc)) {
			$remindId = @array_shift($cc);
		}

		if(!empty($remindId)) {
			$assignToEmail = $assignToName = '';
			$emailToListInfo = $ccList = array();

			$emailToList = array($remindId => $remindId);
			$emailToList = !empty($assign) && $remindId != $assign ? array_merge($emailToList, array($assign => $assign)) : $emailToList;
			$emailToList = !empty($cc) ? array_merge($emailToList, $cc) : $emailToList;

			$condition = array('status' => array('gt', 0));
			$condition['user_id'] = count($emailToList) > 1 ? array('in', $emailToList) : array_shift($emailToList);
			$emailToListInfo = M('user')->where($condition)->field('user_id, email, user_name')->select();

			foreach($emailToListInfo as $val) {
				if(!empty($assign) && $val['user_id'] == $assign && $assign != $remindId) {
					$assignToUserName = $val['user_name'];
					continue;
				}

				if($val['user_id'] == $remindId) {
					$assignToEmail = $val['email'];
					$assignToEmailName = $val['user_name'];
					$assign == $remindId && $assignToUserName = $val['user_name'];
				} else {
					$ccList[] = array(
						'email' => $val['email'],
						'name' => $val['user_name']
					);
				}
			}

			$actionLog = $this->printActionLogById($actionLogId);
			$actionLog['comment'] = imageChangeToFullUrl($actionLog['comment']);

			$action_object_type = C('ACTION_OBJECT_TYPE');
			$actionLog['object_type_id'] = $actionLog['object_type'];
			$actionLog['object_type'] = $action_object_type[$actionLog['object_type']];

			$this->assign('actionLog', $actionLog);
			$this->assign('mail_background_img', (is_ssl()?'https://':'http://').$_SERVER['HTTP_HOST'].'/Public/image/img/');

			$objectType	= array(
				'user' => '用户操作',
				'demand' => '需求',
				'bug' => 'bug',
				'task' => '任务',
				'originalDemand' => '原始需求',
				'work' => '通用任务',
				'sales_tasks' => '销售任务',
			);

			$action_object_type = C('ACTION_OBJECT_TYPE');
			$title_type = !empty($abnormalMsg) ? '异常' : '普通';
			$titleObj = $isOverdueFeedback ? $objectType[$actionLog['object_type']].'超期反馈' : $objectType[$actionLog['object_type']];
			$subject = '【'.$title_type.'】TASK-'.$name.'-'.$titleObj;
			$this->assign('name', $titleObj.' #'.$objectId.':'.$name);
			$this->assign('url', $url);
			$this->assign('assignTo', $assignToUserName);
			$this->assign('content', $content);
			$this->assign('abnormal', $abnormalMsg);

			$extList = array('7z', 'accd', 'asp', 'avi', 'bat', 'bmp', 'bsp', 'chm', 'css', 'dat', 'dll', 'doc', 'docx', 'dwt', 'emf', 'eml', 'eps', 'exe', 'file', 'gif', 'gzip', 'html', 'ico', 'ind', 'ini', 'jpeg', 'jpg', 'js', 'jsp', 'lbi', 'midi', 'mov', 'mp3', 'mp4', 'mpeg', 'pdf', 'php', 'png', 'ppt', 'proj', 'pst', 'pub', 'rar', 'raw', 'read', 'rm', 'rmvb', 'sql', 'swf', 'tar', 'tif', 'txt', 'unknow', 'url', 'vsd', 'wav', 'wma', 'wmv', 'xls', 'xlsx', 'xmind', 'xml', 'zip');
			foreach($attachment as &$val) {
				$val['extClass'] = in_array(strtolower($val['ext']), $extList) ? strtolower($val['ext']) : 'unknow';
			}
            $this->assign('actionLogId', $actionLogId);
			$this->assign('mailAttachment', $attachment);
			
			$mailTpl = $isOverdueFeedback ? 'Public:feedBackMail' : 'Public:mail';

			$mailContent = $this->fetch($mailTpl);

			return sendMail($assignToEmail, $subject, $mailContent, $assignToEmailName, $ccList);
		}
	}

	/**
     +----------------------------------------------------------
     * 发送考勤相关邮件
     +----------------------------------------------------------
     * @param string $name 接收人姓名
     +----------------------------------------------------------
	 * @param string $email 接收邮件地址
     +----------------------------------------------------------
	 * @param intval $objectType 对象类型（与配置文件一致）
     +----------------------------------------------------------
	 * @param intval $mailType 邮件类型（1、申请提醒 2、审批结果）
     +----------------------------------------------------------
	 * @param string $url 邮件对应打开的url
     +----------------------------------------------------------
	 * @param string $template 申请详情html内容
     +----------------------------------------------------------
	 * @return array
     +----------------------------------------------------------
     */
	protected function sendAttendanceMail($name, $email, $objectType, $mailType, $url, $template) {
		$objectTypeList = array(
			'7' => '请假',
			'8' => '销假',
			'9' => '调休',
			'10' => '补签',
			'12' => '班车',
			'13' => '取消调休'
		);

		$this->assign('objectType', $objectTypeList[$objectType]);
		$this->assign('mailType', $mailType);
		$this->assign('url', $url);
		$this->assign('name', $name);
		$this->assign('template', $template);
		$this->assign('mail_background_img', (is_ssl()?'https://':'http://').$_SERVER['HTTP_HOST'].'/Public/image/img/');
		$mailContent = $this->fetch('Public:attendanceMail');
		
		$subjectName = $mailType == 1 ? $this->userName : $name;
		$subject = '【考勤】'.$subjectName.'-'.$objectTypeList[$objectType].($mailType == 1 ? '申请' : '申请的审批结果');

		return sendMail($email, $subject, $mailContent);
	}

	//检测节点是否有权限
	protected function checkNodeAccess($controller, $method) {
		if($this->needCheckAccess && !RBAC::accessDecision($this->nodeAccessList, MODULE_NAME, $controller, $method)) {
			return false;
		}

		return true;
	}

	/**
     +----------------------------------------------------------
     * 将删除数据放入回收站
     +----------------------------------------------------------
     * @param intval $tableName 要删除数据的表名
     +----------------------------------------------------------
	 * @param string $object_id 要删除数据的主键ID
     +----------------------------------------------------------
	 * @return boolean 返回删除结果
     +----------------------------------------------------------
     */
	protected function putData2Recycle($tableName, $object_id) {
		$delData = array(
			'table_name' => ':table_name',
			'object_id' => ':object_id',
			'operator_id' => ':operator_id',
			'operator' => ':operator',
			'create_time' => ':create_time'
		);
		$bindParam = array(
			':table_name' => $tableName,
			':object_id' => array($object_id, \PDO::PARAM_INT),
			':operator_id' => array($this->uid, \PDO::PARAM_INT),
			':operator' => $this->userName,
			':create_time' => date('Y-m-d H:i:s')
		);

		return M('recycle')->data($delData)->bind($bindParam)->add();
	}

	/**
     +----------------------------------------------------------
     * 通用任务操作视图权限记录表
     +----------------------------------------------------------
     * @param intval $wid 要操作的通用任务ID
     +----------------------------------------------------------
	 * @param boolean $startTrans 是否开启事务
     +----------------------------------------------------------
	 * @param intval $newCreate 提出人变化
     +----------------------------------------------------------
	 * @param intval $newAssign 指派人变化
     +----------------------------------------------------------
	 * @param intval $newFinish 完成人变化
     +----------------------------------------------------------
	 * @param intval $newClose 关闭人变化
     +----------------------------------------------------------
	 * @param intval $newCancel 取消人变化
     +----------------------------------------------------------
	 * @param intval $newPersonReview 负责评审人变化
     +----------------------------------------------------------
	 * @param intval $newPartReview 参与评审人变化
     +----------------------------------------------------------
	 * @param array $newCc 抄送人变化
     +----------------------------------------------------------
	 * @param array $newInviteReview 邀请参与评审变化
     +----------------------------------------------------------
	 * @return 开启事务，且返回false 则表示操作失败，事务需回滚
     +----------------------------------------------------------
     */
	protected function updateWorkViewAccess($wid, $startTrans=false, $newCreate=null, $newAssign=null, $newFinish=null, $newClose=null, $newCancel=null, $newPersonReview=null, $newPartReview=null, $newCc=array(), $newInviteReview=array()) {
		$model = M('work_view_access');

		$workModel = M('work');
		$workInfo = $workModel->where(array('id' => ':id'))->bind(':id', $wid, \PDO::PARAM_INT)->field('id, child_num, status, path, pid')->find();
		if(empty($workInfo)) {
			return array();
		}
		
		if((!empty($newCreate) || isset($newAssign) || !empty($newPersonReview) || !empty($newCc)) && $workInfo['child_num'] > 0) {	//存在子任务
			$childWid = $workModel->where(array('path' => array('LIKE', ':path')))->bind(':path', $workInfo['path'].',%')->getField('id', true);
		}

		$allow_child_view = 0;

		if($workInfo['pid'] > 0) {	//存在上级任务，需检查顶级是否允许子任务查看
			$firstId = substr($workInfo['path'], 0, strpos($workInfo['path'], ','));

			$allow_child_view = $workModel->where(array('id' => ':id'))->bind(array(':id' => array($firstId, \PDO::PARAM_INT)))->getField('allow_child_view');

			if($allow_child_view == '1') {	//查询当前
				$childViewTopList = $model->where(array('wid' => ':wid', 'type' => ':type', 'pid' => ':pid'))->bind(array(':wid' => array($firstId, \PDO::PARAM_INT), ':type' => array(10, \PDO::PARAM_INT), ':pid' => array($wid, \PDO::PARAM_INT)))->getField('uid', true);
				if(empty($childViewTopList)) {
					$childViewTopList = array();
				}

				$workViewAccessListTemp = $model->where(array('wid' => ':wid', 'pid' => ':pid'))->bind(array(':wid' => array($wid, \PDO::PARAM_INT), ':pid' => array(0, \PDO::PARAM_INT)))->field('id, uid, type')->select();

				$workViewAccessNewList = array();
				if($workViewAccessListTemp) {
					foreach($workViewAccessListTemp as $val) {
						if(in_array($val['type'], array(6, 7, 8, 9))) {
							$workViewAccessNewList[$val['type']][] = $val['uid'];
						} else {
							$workViewAccessNewList[$val['type']] = $val['uid'];
						}
					}
				}
			}
		}

		//$oldViewAccess = $model->where(array('wid' => ':wid', 'pid' => ':pid', 'type' => array('IN', array(':type1', ':type2', ':type3', ':type4', ':type5'))))->bind(array(':wid' => array($wid, \PDO::PARAM_INT), ':pid' => array(0, \PDO::PARAM_INT), ':type1' => 1, ':type2' => 2, ':type3' => 3, ':type4' => 4, ':type5' => 5))->getField('type, uid');
		
		$oldViewAccess = $model->where(array('wid' => ':wid', 'pid' => ':pid', 'type' => array('IN', '1,2,3,4,5')))->bind(array(':wid' => array($wid, \PDO::PARAM_INT), ':pid' => array(0, \PDO::PARAM_INT)))->getField('type, uid');
		
		$now_datetime = date('Y-m-d H:i:s');
		
		if(!empty($newCreate)) {	//修改或者新增提出人
			if(!empty($oldViewAccess[1]) && $oldViewAccess[1] != $newCreate) {	//编辑提出人
				$res = $model->data(array('uid' => ':uid'))->where(array('wid' => ':wid', 'type' => ':type', 'pid' => ':pid'))->bind(array(':wid' => array($wid, \PDO::PARAM_INT), ':type' => array(1, \PDO::PARAM_INT), ':uid' => array($newCreate, \PDO::PARAM_INT), ':pid' => array(0, \PDO::PARAM_INT)))->save();
				if($startTrans && $res === false) {
					return false;
				}
				
				if(!empty($childWid)) {	//查看子任务的权限
					$res = $model->where(array('wid' => array('IN', $childWid), 'type' => 1, 'pid' => $wid))->data(array('uid' => $newCreate))->save();
					if($startTrans && $res === false) {
						return false;
					}
				}
			} elseif(empty($oldViewAccess[1])) {	//新增任务
				$res = $model->data(array('uid' => ':uid', 'wid' => ':wid', 'type' => ':type', 'pid' => ':pid', 'create_time' => ':create_time'))->bind(array(':wid' => array($wid, \PDO::PARAM_INT), ':type' => array(1, \PDO::PARAM_INT), ':uid' => array($newCreate, \PDO::PARAM_INT), ':pid' => array(0, \PDO::PARAM_INT), ':create_time' => $now_datetime))->add();
				if($startTrans && $res === false) {
					return false;
				}

				//获取上级任务权限 code start
				if($workInfo['pid'] > 0) {
					$parentViewAccess = $model->where(array('wid' => ':wid', 'type' => array('IN', '1,2,7,8')))->bind(':wid', $workInfo['pid'], \PDO::PARAM_INT)->field('wid, pid, type, uid')->select();
					
					if(!empty($parentViewAccess)) {
						$insertData = array();
						foreach($parentViewAccess AS $value) {
							$temp = array('type' => $value['type']);
							$temp['wid'] = $wid;
							$temp['uid'] = $value['uid'];
							$temp['pid'] = $value['pid'] > 0 ? $value['pid'] : $value['wid'];
							$temp['create_time'] = $now_datetime;
							$insertData[] = $temp;
						}
						$res = $model->addAll($insertData);
						if($startTrans && !$res) {
							return false;
						}
					}
				}
				//获取上级任务权限 code end
			}
			$allow_child_view == '1' && $workViewAccessNewList[1] = $newCreate;
		}
		
		if(isset($newAssign)) {	//修改指派人
			if($newAssign > 0) {
				if(!empty($oldViewAccess[2])) {
					$res = $model->data(array('uid' => ':uid'))->where(array('wid' => ':wid', 'type' => ':type', 'pid' => ':pid'))->bind(array(':wid' => array($wid, \PDO::PARAM_INT), ':type' => array(2, \PDO::PARAM_INT), ':uid' => array($newAssign, \PDO::PARAM_INT), ':pid' => array(0, \PDO::PARAM_INT)))->save();
					if($startTrans && $res === false) {
						return false;
					}

					if(!empty($childWid)) {	//查看子任务的权限
						$res = $model->where(array('wid' => array('IN', $childWid), 'type' => 2, 'pid' => $wid))->data(array('uid' => $newAssign))->save();
						if($startTrans && $res === false) {
							return false;
						}
					}
				} else {
					$insertData = array();

					$insertData[] = array(
						'uid' => $newAssign,
						'wid' => $wid,
						'type' => 2,
						'pid' => 0,
						'create_time' => $now_datetime
					);

					if(!empty($childWid)) {
						foreach($childWid as $val) {
							$insertData[] = array(
								'uid' => $newAssign,
								'wid' => $val,
								'type' => 2,
								'pid' => $wid,
								'create_time' => $now_datetime
							);
						}
					}
					$res = $model->addAll($insertData);
					if($startTrans && !$res) {
						return false;
					}
				}

				$allow_child_view == '1' && $workViewAccessNewList[2] = $newAssign;
			} else {	//删除指派相关
				if(!empty($oldViewAccess[2])) {
					$res = $model->where(array('wid' => ':wid', 'type' => ':type', 'pid' => ':pid'))->bind(array(':wid' => array($wid, \PDO::PARAM_INT), ':type' => array(2, \PDO::PARAM_INT), ':pid' => array(0, \PDO::PARAM_INT)))->delete();
					if($startTrans && $res === false) {
						return false;
					}

					if(!empty($childWid)) {	//查看子任务的权限
						$res = $model->where(array('wid' => array('IN', $childWid), 'type' => 2, 'pid' => $wid))->delete();
						if($startTrans && $res === false) {
							return false;
						}
					}

					if($allow_child_view == '1') {
						unset($workViewAccessNewList[2]);
					}
				}
			}
		}

		if(!empty($newFinish)) {
			if(!empty($oldViewAccess[3])) {
				$res = $model->data(array('uid' => ':uid'))->where(array('wid' => ':wid', 'type' => ':type', 'pid' => ':pid'))->bind(array(':wid' => array($wid, \PDO::PARAM_INT), ':type' => array(3, \PDO::PARAM_INT), ':uid' => array($newFinish, \PDO::PARAM_INT), ':pid' => array(0, \PDO::PARAM_INT)))->save();
				if($startTrans && $res === false) {
					return false;
				}
			} else {
				$res = $model->data(array('uid' => ':uid', 'wid' => ':wid', 'type' => ':type', 'pid' => ':pid', 'create_time' => ':create_time'))->bind(array(':wid' => array($wid, \PDO::PARAM_INT), ':type' => array(3, \PDO::PARAM_INT), ':uid' => array($newFinish, \PDO::PARAM_INT), ':pid' => array(0, \PDO::PARAM_INT), ':create_time' => $now_datetime))->add();
				if($startTrans && $res === false) {
					return false;
				}
			}
			$allow_child_view == '1' && $workViewAccessNewList[3] = $newFinish;
		}

		if(!empty($newClose)) {
			if(!empty($oldViewAccess[4])) {
				$res = $model->data(array('uid' => ':uid'))->where(array('wid' => ':wid', 'type' => ':type', 'pid' => ':pid'))->bind(array(':wid' => array($wid, \PDO::PARAM_INT), ':type' => array(4, \PDO::PARAM_INT), ':uid' => array($newClose, \PDO::PARAM_INT), ':pid' => array(0, \PDO::PARAM_INT)))->save();
				if($startTrans && $res === false) {
					return false;
				}
			} else {
				$res = $model->data(array('uid' => ':uid', 'wid' => ':wid', 'type' => ':type', 'pid' => ':pid', 'create_time' => ':create_time'))->bind(array(':wid' => array($wid, \PDO::PARAM_INT), ':type' => array(4, \PDO::PARAM_INT), ':uid' => array($newClose, \PDO::PARAM_INT), ':pid' => array(0, \PDO::PARAM_INT), ':create_time' => $now_datetime))->add();
				if($startTrans && $res === false) {
					return false;
				}
			}
			$allow_child_view == '1' && $workViewAccessNewList[4] = $newClose;
		}

		if(!empty($newCancel)) {
			if(!empty($oldViewAccess[5])) {
				$res = $model->data(array('uid' => ':uid'))->where(array('wid' => ':wid', 'type' => ':type', 'pid' => ':pid'))->bind(array(':wid' => array($wid, \PDO::PARAM_INT), ':type' => array(5, \PDO::PARAM_INT), ':uid' => array($newCancel, \PDO::PARAM_INT), ':pid' => array(0, \PDO::PARAM_INT)))->save();
				if($startTrans && $res === false) {
					return false;
				}
			} else {
				$res = $model->data(array('uid' => ':uid', 'wid' => ':wid', 'type' => ':type', 'pid' => ':pid', 'create_time' => ':create_time'))->bind(array(':wid' => array($wid, \PDO::PARAM_INT), ':type' => array(5, \PDO::PARAM_INT), ':uid' => array($newCancel, \PDO::PARAM_INT), ':pid' => array(0, \PDO::PARAM_INT), ':create_time' => $now_datetime))->add();
				if($startTrans && $res === false) {
					return false;
				}
			}
			$allow_child_view == '1' && $workViewAccessNewList[5] = $newCancel;
		}

		if(!empty($newPersonReview)) {
			$existOldPersonReview = $model->where(array('uid' => ':uid', 'type' => ':type', 'pid' => ':pid', 'wid' => ':wid'))->bind(array(':uid' => array($newPersonReview, \PDO::PARAM_INT), ':type' => array(8, \PDO::PARAM_INT), ':pid' => array(0, \PDO::PARAM_INT), ':wid' => array($wid, \PDO::PARAM_INT)))->count('id');

			if(!$existOldPersonReview) {
				if(empty($childWid)) {
					$res = $model->data(array('uid' => ':uid', 'wid' => ':wid', 'type' => ':type', 'pid' => ':pid', 'create_time' => ':create_time'))->bind(array(':wid' => array($wid, \PDO::PARAM_INT), ':type' => array(8, \PDO::PARAM_INT), ':uid' => array($newPersonReview, \PDO::PARAM_INT), ':pid' => array(0, \PDO::PARAM_INT), ':create_time' => $now_datetime))->add();
					if($startTrans && $res === false) {
						return false;
					}
				} else {
					$insertData = array();
					$insertData[] = array(
						'wid' => $wid,
						'uid' => $newPersonReview,
						'pid' => 0,
						'type' => 8,
						'create_time' => $now_datetime
					);

					foreach($childWid as $value) {
						$insertData[] = array('wid' => $value, 'uid' => $newPersonReview, 'pid' => $wid, 'type' => 8, 'create_time' => $now_datetime);
					}
					$res = $model->addAll($insertData);
					if($startTrans && !$res) {
						return false;
					}
				}

				if($allow_child_view == '1') {
					$workViewAccessNewList[8][] = $newPersonReview;
				}
			}
		}

		if(!empty($newPartReview)) {
			$existOldPartReview = $model->where(array('uid' => ':uid', 'type' => ':type', 'pid' => ':pid', 'wid' => ':wid'))->bind(array(':uid' => array($newPartReview, \PDO::PARAM_INT), ':type' => array(6, \PDO::PARAM_INT), ':pid' => array(0, \PDO::PARAM_INT), ':wid' => array($wid, \PDO::PARAM_INT)))->count('id');

			if(!$existOldPartReview) {
				$res = $model->data(array('uid' => ':uid', 'wid' => ':wid', 'type' => ':type', 'pid' => ':pid', 'create_time' => ':create_time'))->bind(array(':wid' => array($wid, \PDO::PARAM_INT), ':type' => array(6, \PDO::PARAM_INT), ':uid' => array($newPartReview, \PDO::PARAM_INT), ':pid' => array(0, \PDO::PARAM_INT), ':create_time' => $now_datetime))->add();
				if($startTrans && $res === false) {
					return false;
				}

				if($allow_child_view == '1') {
					$workViewAccessNewList[6][] = $newPartReview;
				}
			}
		}

		if(!empty($newCc)) {
			$insertCcList = array();
			if(empty($childWid)) {
				$oldCcList = $model->where(array('wid' => ':wid', 'pid' => ':pid', 'type' => ':type'))->bind(array(':wid' => array($wid, \PDO::PARAM_INT), ':pid' => array(0, \PDO::PARAM_INT), ':type' => array('7', \PDO::PARAM_INT)))->getField('uid', true);
				$newInsertTemp = !empty($oldCcList) ? array_diff($newCc, $oldCcList) : $newCc;
				!empty($newInsertTemp) && $insertCcList[$wid] = $newInsertTemp;
			} else {
				$oldCcList = $model->where(array('wid' => array('IN', $childWid), 'type' => '7', 'pid' => $wid))->field('uid, wid')->select();

				$oldPCcList = $model->where(array('wid' => ':wid', 'type' => ':type', 'pid' => ':pid'))->bind(array(':wid' => array($wid, \PDO::PARAM_INT), ':type' => array(7, \PDO::PARAM_INT), ':pid' => array(0, \PDO::PARAM_INT)))->field('uid, wid')->find();
				!empty($oldPCcList) && $oldCcList[] = $oldPCcList;

				$oldCcIds = array();
				if(!empty($oldCcList)) {
					foreach($oldCcList as $value) {
						$oldCcIds[$value['wid']][] = $value['uid'];
					}
				}

				$whereCcIds = $childWid;
				$whereCcIds[] = $wid;
				foreach($whereCcIds as $value) {
					$newInsertTemp = array_diff($newCc, !empty($oldCcIds[$value]) ? $oldCcIds[$value] : array());
					!empty($newInsertTemp) && $insertCcList[$value] = $newInsertTemp;
				}
			}

			if(!empty($insertCcList)) {
				$insertData = array();
				foreach($insertCcList as $work_id => $value) {
					foreach($value as $v) {
						$insertData[] = array('wid' => $work_id, 'pid' => $work_id == $wid ? 0 : $wid, 'type' => '7', 'uid' => $v, 'create_time' => $now_datetime);

						if($allow_child_view == '1') {
							$workViewAccessNewList[7][] = $v;
						}
					}
				}

				$res = $model->addAll($insertData);
				if($startTrans && !$res) {
					return false;
				}
			}
		}

		if(!empty($newInviteReview)) {
			$oldInviteList = $model->where(array('wid' => ':wid', 'pid' => ':pid', 'type' => ':type'))->bind(array(':wid' => array($wid, \PDO::PARAM_INT), ':pid' => array(0, \PDO::PARAM_INT), ':type' => array('9', \PDO::PARAM_INT)))->getField('uid', true);
			$oldInviteList = isset($oldInviteList) ? $oldInviteList : array();
			$insertInviteList = array_diff($newInviteReview, $oldInviteList);
			if(!empty($insertInviteList)) {
				$insertData = array();
				foreach($insertInviteList as $value) {
					$insertData[] = array('wid' => $wid, 'pid' => 0, 'type' => '9', 'uid' => $value, 'create_time' => $now_datetime);
					if($allow_child_view == '1') {
						$workViewAccessNewList[9][] = $value;
					}
				}
				$res = $model->addAll($insertData);
				if($startTrans && !$res) {
					return false;
				}
			}

			$delInviteList = array_diff($oldInviteList, $newInviteReview);
			if(!empty($delInviteList)) {
				$delIds = array();
				foreach($delInviteList as $value) {
					$delIds[] = $value;

					if($allow_child_view == '1' && $key = array_search($value, $workViewAccessNewList[9])) {
						unset($workViewAccessNewList[9][$key]);
					}
				}
				$res = $model->where(array('wid' => $wid, 'pid' => 0, 'type' => '9', 'uid' => array('IN', $delIds)))->delete();
				if($startTrans && $res === false) {
					return false;
				}
			}
		}

		if($allow_child_view == '1') {
			$workViewAccessNewIds = array();
			foreach($workViewAccessNewList as $value) {
				if(is_array($value)) {
					foreach($value as $v) {
						$workViewAccessNewIds[$v] = $v;
					}
				} else {
					$workViewAccessNewIds[$value] = $value;
				}
			}

			$insertAllowChildViewIds = array_diff($workViewAccessNewIds, $childViewTopList);
			$delAllowChildViewIds = array_diff($childViewTopList, $workViewAccessNewIds);

			$parentWorkPath = substr($workInfo['path'], 0, strrpos($workInfo['path'], ','));
			$parentWorkIds = explode(',', $parentWorkPath);

			if(!empty($insertAllowChildViewIds)) {
				$insertAllowChildViewIds = array_unique($insertAllowChildViewIds);
				$insertAllowChildViewData = array();
				foreach($insertAllowChildViewIds as $v) {
					foreach($parentWorkIds as $value) {
						$insertAllowChildViewData[] = array('wid' => $value, 'pid' => $wid, 'type' => '10', 'uid' => $v, 'create_time' => $now_datetime);
					}
				}
				$res = $model->addAll($insertAllowChildViewData);
				if($startTrans && !$res) {
					return false;
				}
			}

			if(!empty($delAllowChildViewIds)) {
				$res = $model->where(array('wid' => array('IN', $parentWorkIds), 'type' => '10', 'pid' => $wid, 'uid' => array('IN', $delAllowChildViewIds)))->delete();
				if($startTrans && $res === false) {
					return false;
				}
			}
		}

		return true;
	}
	
	/**
     +----------------------------------------------------------
     * 根据用户群组id集合获取用户id集合
     +----------------------------------------------------------
     * @param str or array $groupIds 用户群组id集合
     +----------------------------------------------------------
	 * @return array 用户id集合
     +----------------------------------------------------------
     */
	public function getUserIdsByGroup($groupIds){
		$userIds = array();
		if(empty($groupIds)){
			return $userIds;
		}
		$groupModel = M('customize_user_group');
		$groupMemberModel = M('customize_user_group_member');
		$userGroupIds = $deptGroupIds = array();
		$types = $groupModel->where(array('id' => array('in', $groupIds)))->getField('id, type');
		if(!empty($types)){
			foreach($types as $id => $type){
				if($type == 1){
					$userGroupIds[] = $id;
				}else{
					$deptGroupIds[] = $id;
				}
			}
		}
		
		if(!empty($userGroupIds)){
			$userGroup = $groupMemberModel->where(array('group_id' => array('in', $userGroupIds)))->getField('member_id', true);
			$userIds = !empty($userGroup) ? array_merge($userIds, $userGroup) : $userIds;
		}
		
		if(!empty($deptGroupIds)){
			$deptIds = $groupMemberModel->where(array('group_id' => array('in', $deptGroupIds)))->getField('member_id', true);
			
			$condition = array('p.id' => array('in', $deptIds));
			$subSql = M('dept')->alias('c')->join("LEFT JOIN __DEPT__ p ON CONCAT(',', c.path, ',') LIKE CONCAT('%,', p.id, ',%')")
			->where($condition)->group('c.id')->field('c.id')->buildSql();
			
			$list = M('user')->alias('u')
					->where(array('u.status' => array('gt', 0), 'u.dept' => array('exp', "IN($subSql)")))
					->field('u.user_id')
					->select();
			$deptGroup = array();
			if(!empty($list)){
				foreach($list as $v){
					$deptGroup[] = $v['user_id'];
				}
			}
			$userIds = !empty($deptGroup) ? array_merge($userIds, $deptGroup) : $userIds;
		}
		
		return $userIds;
	}

	/**
     +----------------------------------------------------------
     * 创建通用任务
     +----------------------------------------------------------
     * @param str $title 任务标题
	 +----------------------------------------------------------
     * @param str $content 任务描述内容
	 +----------------------------------------------------------
     * @param int $assign 指派给
	 +----------------------------------------------------------
     * @param str $expect_deadline 期望截止日期
     +----------------------------------------------------------
	 * @param int $create_by 创建人，默认为当前操作人
     +----------------------------------------------------------
	 * @param array $ccList 抄送给
     +----------------------------------------------------------
	 * @return int OR boolean 成功返回新增任务id，失败返回false
     +----------------------------------------------------------
     */
	public function createCommonWork($title, $content, $assign, $expect_deadline, $create_by = NULL, $ccList = array()) {
		if(!isset($create_by) || $create_by == $this->uid) {
			$create_by = $this->uid;
			$create_dept = $this->dept;
		} else {
			$createUserInfo = M('user')->where(array('user_id' => ':user_id'))->bind(':user_id', $create_by, \PDO::PARAM_INT)->field('user_id, user_name, dept')->find();
			$create_dept = $createUserInfo['dept'];
		}
		$nowDateTime = date('Y-m-d H:i:s');
		$ccIds = '';
		if(!empty($ccList)) {
			$ccIds = implode(',', $ccList);
		}

		$insertData = array(
			'name' 					=> ':name',
			'pid' 					=> ':pid',
			'version' 				=> ':version',
			'path' 					=> ':path',
			'priority' 				=> ':priority',
			'estimate' 				=> ':estimate',
			'consumed' 				=> ':consumed',
			'surplus' 				=> ':surplus',
			'expect_deadline' 		=> ':expect_deadline',
			'deadline' 				=> ':deadline',
			'work_type' 			=> ':work_type',
			'create_by' 			=> ':create_by',
			'create_time'			=> ':create_time',
			'create_by_dept' 		=> ':create_by_dept',
			'assign' 				=> ':assign',
			'assign_time' 			=> ':assign_time',
			'plan_start' 			=> ':plan_start',
			'real_start' 			=> ':real_start',
			'allow_start_time' 		=> ':allow_start_time',
			'finish_by' 			=> ':finish_by',
			'finish_time' 			=> ':finish_time',
			'cancel_by' 			=> ':cancel_by',
			'cancel_time' 			=> ':cancel_time',
			'closed_by' 			=> ':closed_by',
			'closed_time' 			=> ':closed_time',
			'last_edit_by' 			=> ':last_edit_by',
			'last_edit_time' 		=> ':last_edit_time',
			'status' 				=> ':status',
			'stage' 				=> ':stage',
			'activated_num' 		=> ':activated_num',
			'operator' 				=> ':operator',
			'child_num' 			=> ':child_num',
			'level' 				=> ':level',
			'cc' 					=> ':cc',
			'related_task' 			=> ':related_task',
			'pversion' 				=> ':pversion',
			'part_in_review' 		=> ':part_in_review',
			'allow_child_view' 		=> ':allow_child_view',
			'special_work_type' 	=> ':special_work_type',
			'satisfaction_star_num' => ':satisfaction_star_num',
			'feedback_object' 		=> ':feedback_object',
			'feedback_level' 		=> ':feedback_level'
		);
		$bindParam = array(
			':name' 					=> $title,
			':pid' 						=> array('0', \PDO::PARAM_INT),
			':version' 					=> array('1', \PDO::PARAM_INT),
			':path' 					=> '',
			':priority' 				=> array('3', \PDO::PARAM_INT),
			':estimate' 				=> array('0', \PDO::PARAM_INT),
			':consumed' 				=> array('0', \PDO::PARAM_INT),
			':surplus' 					=> array('0', \PDO::PARAM_INT),
			':expect_deadline' 			=> $expect_deadline,
			':deadline' 				=> '0000-00-00',
			':work_type' 				=> array('0', \PDO::PARAM_INT),
			':create_by' 				=> array($create_by, \PDO::PARAM_INT),
			':create_time'				=> $nowDateTime,
			':create_by_dept' 			=> array($create_dept, \PDO::PARAM_INT),
			':assign' 					=> array($assign, \PDO::PARAM_INT),
			':assign_time' 				=> $nowDateTime,
			':plan_start' 				=> '0000-00-00 00:00:00',
			':real_start' 				=> '0000-00-00 00:00:00',
			':allow_start_time' 		=> '0000-00-00 00:00:00',
			':finish_by' 				=> array('0', \PDO::PARAM_INT),
			':finish_time' 				=> '0000-00-00 00:00:00',
			':cancel_by' 				=> array('0', \PDO::PARAM_INT),
			':cancel_time' 				=> '0000-00-00 00:00:00',
			':closed_by' 				=> array('0', \PDO::PARAM_INT),
			':closed_time' 				=> '0000-00-00 00:00:00',
			':last_edit_by' 			=> array($create_by, \PDO::PARAM_INT),
			':last_edit_time' 			=> $nowDateTime,
			':status' 					=> array('0', \PDO::PARAM_INT),
			':stage' 					=> array('0', \PDO::PARAM_INT),
			':activated_num' 			=> array('0', \PDO::PARAM_INT),
			':operator' 				=> array($assign, \PDO::PARAM_INT),
			':child_num' 				=> array('0', \PDO::PARAM_INT),
			':level' 					=> array('1', \PDO::PARAM_INT),
			':cc' 						=> $ccIds,
			':related_task' 			=> '',
			':pversion' 				=> array('0', \PDO::PARAM_INT),
			':part_in_review' 			=> '',
			':allow_child_view' 		=> array('0', \PDO::PARAM_INT),
			':special_work_type' 		=> array('0', \PDO::PARAM_INT),
			':satisfaction_star_num' 	=> array('0', \PDO::PARAM_INT),
			':feedback_object' 			=> '',
			':feedback_level' 			=> array('0', \PDO::PARAM_INT),
		);

		$model = M('work');
		$newWid = $model->data($insertData)->bind($bindParam)->add();
		if($newWid === false) {
			return false;
		}

		$res = $model->where(array('id' => ':id'))->data(array('path' => ':path'))->bind(array(':id' => array($newWid, \PDO::PARAM_INT), ':path' => $newWid))->save();
		if($res === false) {
			return false;
		}

		$insertData = array(
			'name' => ':name',
			'wid' => ':wid',
			'version' => ':version',
			'spec' => ':spec',
			'reason' => ':reason'
		);
		$bindParam = array(
			':name' => $title,
			':wid' => array($newWid, \PDO::PARAM_INT),
			':version' => array(1, \PDO::PARAM_INT),
			':spec' => $content,
			':reason' => array(0, \PDO::PARAM_INT),
		);
		$res = M('work_content')->bind($bindParam)->add($insertData);
		if($res === false) {
			return false;
		}

		//更新视图权限
		$updateWorkView = $this->updateWorkViewAccess($newWid, true, $create_by, $assign, null, null, null, null, null, $ccList);
		if($updateWorkView === false) {
			return false;
		}

		//写入操作记录
		$newActionLogIds = $this->createActionLog(13, $newWid, 'created', '', '', $create_by);
		if(!$newActionLogIds) {
			return false;
		}
        $this->sendWebMsg($newWid, $title, $newActionLogIds, $assign, array(array('title' => '任务描述', 'desc' => imageChangeToFullUrl($content))), U('Commontasks/view', array('id' => $newWid), 'html', true), 1, $ccList, array());

		return $newWid;
	}
	
	/**
	 +----------------------------------------------------------
	 * 站内信
	 +----------------------------------------------------------
	 * @param int $type 消息类型
	 +----------------------------------------------------------
	 * @param string $title 要发送的消息的标题
	 +----------------------------------------------------------
	 * @param int $sender 发送人
	 +----------------------------------------------------------
	 * @param int $receiver 接收人
	 +----------------------------------------------------------
	 * @param int $content 要发送的数据描述信息 
	 +----------------------------------------------------------
	 * @param array $cc 要抄送的用户 for example：array(1,2,3)
	 +----------------------------------------------------------
	 * @param: $attachment[array]	邮件中带附件	example:array(array('id' => '附件1ID', 'name' => '附件1', 'size' => '附件1大小'), array('id' => '附件2ID', 'name' => '附件2', 'size' => '附件2大小'))
	 +----------------------------------------------------------
	 * @return boolean
	 +----------------------------------------------------------
	 */
	protected function sendMsg($type, $title, $sender = NULL , $receiver, $content, $cc = array(), $attachment = array()) {
		$receivers = array();
		if(empty($receiver) && empty($cc)){
			return false;
		}
		if(!empty($cc)){
			array_push($cc, $receiver);
			$receiver = $cc;
		}else{
			$receivers[0] = $receiver;
		}
		
		if(empty($sender)){
			$sender = $this->uid;
		}
		$model = M('web_message');
		$nowDate = date('Y-m-d H:i:s');
		foreach ($receivers as $v){
			$insertData = $bindData = array();
			if(empty($v)){
				continue;
			}
			$insertData['type'] = ':type';
			$insertData['name'] = ':name';
			$insertData['receiver'] = ':receiver';
			$insertData['sender'] = ':sender';
			$insertData['create_time'] = ':create_time';
			$insertData['content'] = ':content';
			$insertData['status'] = ':status';
			$insertData['all_receiver'] = ':all_receiver';
				
			$bindData[':type'] = $type;
			$bindData[':name'] = $title;
			$bindData[':receiver'] = $v;
			$bindData[':sender'] = $sender;
			$bindData[':create_time'] = $nowDate;
			$bindData[':content'] = $content;
			$bindData[':status'] = 0;
			$bindData[':all_receiver'] = implode(',', $receivers);
			$res = $model->data($insertData)->bind($bindData)->add();
			if($res === false){
				return false;
			}
		}
		return true;
	}

    /**
    +----------------------------------------------------------
     * 系统发送站内信
    +----------------------------------------------------------
     * @param intval $objectId 要发送站内信的数据对象ID
    +----------------------------------------------------------
     * @param string $name 要发送的数据对象名称
    +----------------------------------------------------------
     * @param intval $actionLogId 要发送的操作记录id
    +----------------------------------------------------------
     * @param intval $assign 指派给的用户id
    +----------------------------------------------------------
     * @param intval $content 要发送的数据描述信息 for example:array(array('title' => '任务描述', 'desc' => '描述内容'))
    +----------------------------------------------------------
     * @param string $url 要发送的对象的url地址
    +----------------------------------------------------------
     * @param boolean $remind 是否对指派人发送站内信提醒
    +----------------------------------------------------------
     * @param array $cc 要抄送的用户 for example：array(1,2,3)
    +----------------------------------------------------------
     * @param: $attachment[array]	发送的站内信中带附件	example:array(array('id' => '附件1ID', 'name' => '附件1', 'size' => '附件1大小'), array('id' => '附件2ID', 'name' => '附件2', 'size' => '附件2大小'))
    +----------------------------------------------------------
     * @param string $abnormalMsg 异常信息，默认为空, (超期反馈时为超期详情)
    +----------------------------------------------------------
     * @param boolean $isOverdueFeedback 是否为超期反馈
    +----------------------------------------------------------
     * @param int $type 消息类型 1.私人消息,2公共消息
    +----------------------------------------------------------
     * @param int $sender 发送人
    +----------------------------------------------------------
     * @return array
    +----------------------------------------------------------
     */
    protected function sendWebMsg($objectId, $name, $actionLogId, $assign, $content, $url, $remind = false, $cc = array(), $attachment = array(), $abnormalMsg = '', $isOverdueFeedback = false, $type = 2, $sender = NULL) {
        $remindId = 0;
        if(!empty($assign) && $remind) {
            $remindId = $assign;
        } elseif(!empty($cc)) {
            $remindId = @array_shift($cc);
        }

        if(!empty($remindId)) {
            $assignTo = $assignToName = '';
            $sendListInfo = $ccList = array();

            $sendToList = array($remindId => $remindId);
            $sendToList = !empty($assign) && $remindId != $assign ? array_merge($sendToList, array($assign => $assign)) : $sendToList;
            $sendToList = !empty($cc) ? array_merge($sendToList, $cc) : $sendToList;

            $condition = array('status' => array('gt', 0));
            $condition['user_id'] = count($sendToList) > 1 ? array('in', $sendToList) : array_shift($sendToList);
            $sendListInfo = M('user')->where($condition)->field('user_id, user_name')->select();
            $all_receiver = '';

            foreach($sendListInfo as $val) {
                if(!empty($assign) && $val['user_id'] == $assign && $assign != $remindId) {
                    $assignToUserName = $val['user_name'];
                    !empty($remind) && $all_receiver .= $val['user_id'] . ',';
                    continue;
                }

                if($val['user_id'] == $remindId && $assign == $remindId) {
                    $assignToUserName = $val['user_name'];
                    !empty($remind) && $all_receiver .= $val['user_id'] . ',';
                } else {
                    $ccList[] = array(
                        'user_id' => $val['user_id'],
                        'name' => $val['user_name']
                    );
                    $all_receiver .= $val['user_id'] . ',';
                }
            }

            $actionLog = $this->printActionLogById($actionLogId);
            $actionLog['comment'] = imageChangeToFullUrl($actionLog['comment']);

            $action_object_type = C('ACTION_OBJECT_TYPE');
            $actionLog['object_type_id'] = $actionLog['object_type'];
            $actionLog['object_type'] = $action_object_type[$actionLog['object_type']];

            $actionLogs = $this->getActionLog($actionLog['object_type_id'], $objectId);
            if(!empty($actionLogs)){
                foreach ($actionLogs as $k=>$v){
                    $actionLogs[$k]['object_type_id'] = $actionLog['object_type_id'];
                }
            }
            $this->assign('actionLog', $actionLogs);

            $objectType	= array(
                'user' => '用户操作',
                'demand' => '需求',
                'bug' => 'bug',
                'task' => '任务',
                'originalDemand' => '原始需求',
                'work' => '通用任务',
                'sales_tasks' => '销售任务',
            );

            $title_type = !empty($abnormalMsg) ? '异常' : '普通';
            $titleObj = $isOverdueFeedback ? $objectType[$actionLog['object_type']].'超期反馈' : $objectType[$actionLog['object_type']];
            $subject = '【'.$title_type.'】'.$name.'-'.$titleObj;
            $this->assign('name', $titleObj.' #'.$objectId.':'.$name);
            $this->assign('url', $url);
            $this->assign('assignTo', $assignToUserName);
            $this->assign('content', $content);
            $this->assign('abnormal', $abnormalMsg);

            $extList = array('7z', 'accd', 'asp', 'avi', 'bat', 'bmp', 'bsp', 'chm', 'css', 'dat', 'dll', 'doc', 'docx', 'dwt', 'emf', 'eml', 'eps', 'exe', 'file', 'gif', 'gzip', 'html', 'ico', 'ind', 'ini', 'jpeg', 'jpg', 'js', 'jsp', 'lbi', 'midi', 'mov', 'mp3', 'mp4', 'mpeg', 'pdf', 'php', 'png', 'ppt', 'proj', 'pst', 'pub', 'rar', 'raw', 'read', 'rm', 'rmvb', 'sql', 'swf', 'tar', 'tif', 'txt', 'unknow', 'url', 'vsd', 'wav', 'wma', 'wmv', 'xls', 'xlsx', 'xmind', 'xml', 'zip');
            foreach($attachment as &$val) {
                $val['extClass'] = in_array(strtolower($val['ext']), $extList) ? strtolower($val['ext']) : 'unknow';
            }
            $this->assign('actionLogId', $actionLogId);
            $this->assign('msgAttachment', $attachment);
            $isOverdueFeedback && $this->assign('overdueReason', end($actionLogs));

            $msgTpl = $isOverdueFeedback ? 'Public:feedbackMsg' : 'Public:webMsg';

            $sendContent = $this->fetch($msgTpl);

            $msgData = array();
            $now = date('Y-m-d H:i:s');
            if($type == 1){
                $sender = empty($sender) ? $this->uid : $sender;
            }else{
                $sender = empty($sender) ? 0 : $sender;
            }
            $all_receiver = substr($all_receiver, 0, -1);
            !empty($remind) && $msgData[] = array(
                'pid' => 0,
                'type'  => $type,
                'name'  => $subject,
                'receiver'  =>  $assign,
                'sender'    =>  $sender,
                'create_time'   =>  $now,
                'content'   => $sendContent,
                'status'    =>  0,
                'all_receiver'  => $all_receiver
            );
            foreach ($ccList as $k => $v){
                $msgData[] = array(
                    'pid' => 0,
                    'type'  => $type,
                    'name'  => $subject,
                    'receiver'  =>  $v['user_id'],
                    'sender'    =>  $sender,
                    'create_time'   =>  $now,
                    'content'   => $sendContent,
                    'status'    =>  0,
                    'all_receiver'  => $all_receiver
                );
            }

            $res = M('web_message')->addAll($msgData);
            if($res === false) {
                return false;
            }
            return true;
        }
    }
}