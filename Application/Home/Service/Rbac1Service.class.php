<?php
namespace Home\Service;

class Rbac1Service
{
	//检查当前操作是否需要认证
    static public function checkAccess($controller = CONTROLLER_NAME, $action = ACTION_NAME) {
		if( C('USER_AUTH_ON') ){
			$_module	=	array();
			$_action	=	array();
            if("" != C('REQUIRE_AUTH_MODULE')) {
                //需要认证的模块
                $_module['yes'] = explode(',',strtoupper(C('REQUIRE_AUTH_MODULE')));
            }else {
                //无需认证的模块
                $_module['no'] = explode(',',strtoupper(C('NOT_AUTH_MODULE')));
            }
            //检查当前模块是否需要认证
            if((!empty($_module['no']) && !in_array(strtoupper($controller),$_module['no'])) || (!empty($_module['yes']) && in_array(strtoupper($controller),$_module['yes']))) {
				if("" != C('REQUIRE_AUTH_ACTION')) {
					//需要认证的操作
					$_action['yes'] = explode(',',strtoupper(C('REQUIRE_AUTH_ACTION')));
				}else {
					//无需认证的操作
					$_action['no'] = explode(',',strtoupper(C('NOT_AUTH_ACTION')));
				}
				//检查当前操作是否需要认证
				if((!empty($_action['no']) && !in_array(strtoupper($action),$_action['no'])) || (!empty($_action['yes']) && in_array(strtoupper($action),$_action['yes']))) {
					return true;
				}else {
					return false;
				}
            }else {
                return false;
            }
        }
        return false;
    }

	//检查当前操作是否需要登录才能访问
    static public function checkLoginAccess($controller = CONTROLLER_NAME, $action = ACTION_NAME) {
		if( C('USER_AUTH_ON') ){
			$_module	=	array();
			$_action	=	array();
			//无需登录可访问的模块
			$_module['no'] = explode(',',strtoupper(C('NOT_LOGIN_MODULE')));
            //检查当前模块是否需要登录才能访问
			if(!empty($_module['no']) && in_array(strtoupper($controller), $_module['no'])) {
				return false;
			} else {
				//无需登录可访问的操作
				$_action['no'] = explode(',',strtoupper(C('NOT_LOGIN_ACTION')));

				//检查当前操作是否需要登录才能访问
				if(!empty($_action['no']) && in_array(strtoupper($action),$_action['no'])) {
					return false;
				} else {
					return true;
				}
			}
        }
        return false;
    }

	//权限认证的过滤器方法
    static public function accessDecision($accessList, $appName = MODULE_NAME, $controller = CONTROLLER_NAME, $action = ACTION_NAME) {
		//检查是否需要认证
        if(self::checkAccess($controller, $action)) {
            //存在认证识别号，则进行进一步的访问决策
            $accessGuid   =   md5($appName.$controller.$action);
            if(empty($_SESSION[C('USER_AUTH_KEY')]['uid']) || !in_array($_SESSION[C('USER_AUTH_KEY')]['uid'], explode(',', C('ADMIN_AUTH_KEY')))) {
				if(C('USER_AUTH_TYPE') != 2 && isset($_SESSION[C('USER_AUTH_KEY')]['CHECK_GUID_ACCESS'][$accessGuid]) && $_SESSION[C('USER_AUTH_KEY')]['CHECK_GUID_ACCESS'][$accessGuid] === true) {
					// 如果当前操作已经认证过，无需再次认证
					return true;
				}

                //判断是否为组件化模式，如果是，验证其全模块名
                if(!isset($accessList[strtoupper($appName)][strtoupper($controller)][strtoupper($action)])) {
                    $_SESSION[C('USER_AUTH_KEY')]['CHECK_GUID_ACCESS'][$accessGuid]  =   false;
                    return false;
                }
                else {
                    $_SESSION[C('USER_AUTH_KEY')]['CHECK_GUID_ACCESS'][$accessGuid]	=	true;
                }
            }else{
                //管理员无需认证
				return true;
			}
        }
        return true;
    }

	static public function getAccsessData($authId) {
		$authId = empty($authId) ? 0 : $authId;
		$accessList = array();
		if(C('USER_AUTH_TYPE') == 2) {
			//实时验证
			$accessList = self::getAccessList($authId);
		} else {
			if(isset($_SESSION[C('USER_AUTH_KEY')]['_ACCESS_LIST'])) {
				$accessList = $_SESSION[C('USER_AUTH_KEY')]['_ACCESS_LIST'];
			} else {
				$accessList = $_SESSION[C('USER_AUTH_KEY')]['_ACCESS_LIST'] = self::getAccessList($authId);
			}
		}

		return $accessList;
	}

	//获取用户权限
	static public function getAccessList($authId) {
		$entrustList = M('entrust_list')->where(array('be_entrusted_uid' => ':be_entrusted_uid'))->bind(':be_entrusted_uid', $authId, \PDO::PARAM_INT)->field('entrust_uid, object_type, object_id')->select();

		$accountList = $roleList = array();
		if(!empty($entrustList)) {
			foreach($entrustList AS $val) {
				$val['object_type'] == '1' && $accountList[$val['object_id']] = $val['object_id'];
				$val['object_type'] == '2' && $roleList[$val['entrust_uid']][$val['object_id']] = $val;
			}
		}

		$condition1 = array('r.status' => 1, 'f.status' => 1, 'n.status' => 1);
		if(empty($accountList)) {
			$condition1['ur.user_id'] = $authId;
		} else {
			$accountList[$authId] = $authId;
			$condition1['ur.user_id'] = array('IN', $accountList);
		}

		$userRoleModel = M('user_role_list');

		$nodeList = $userRoleModel->alias('ur')
		->join('LEFT JOIN __NEW_ROLE__ r ON r.id=ur.role_id')
		->join('LEFT JOIN __ROLE_FUNCTION_LIST__ rf ON rf.role_id=r.id')
		->join('LEFT JOIN __FUNCTIONAL_MODULE_LIST__  f ON f.id=rf.function_id')
		->join('LEFT JOIN __FUNCTIONAL_MODULE_NODE_LIST__ fm ON fm.function_id=f.id')
		->join('LEFT JOIN __NODE__ n ON n.id=fm.node_id')
		->where($condition1)
		->order('n.sort ASC')
		->field('n.id, n.name, n.pid, n.type, n.level, ur.user_id, f.id AS function_id, f.node_module, f.is_support_commission')
		->select();

		if(!empty($roleList)) {
			$condition2 = array('r.status' => 1, 'f.status' => 1, 'n.status' => 1);
			$subWhereSql = array();
			foreach($roleList AS $tempUid => $val) {
				$tempEntrustRoelId = array();
				$tempSubWhereSql = array('ur.user_id' => $tempUid);
				foreach($val AS $tempRoleId => $v) {
					$tempEntrustRoelId[$tempRoleId] = $tempRoleId;
				}
				if(count($tempEntrustRoelId) > 1) {
					$tempSubWhereSql['rf.role_id'] = array('IN', $tempEntrustRoelId);
				} else {
					$tempSubWhereSql['rf.role_id'] = array_pop($tempEntrustRoelId);
				}
				$subWhereSql[] = $tempSubWhereSql;
			}
			$subWhereSql['_logic'] = 'OR';
			$condition2['_complex'] = $subWhereSql;

			$nodeList2 = $userRoleModel->alias('ur')
			->join('LEFT JOIN __NEW_ROLE__ r ON r.id=ur.role_id')
			->join('LEFT JOIN __ROLE_FUNCTION_LIST__ rf ON rf.role_id=r.id')
			->join('LEFT JOIN __FUNCTIONAL_MODULE_LIST__  f ON f.id=rf.function_id')
			->join('LEFT JOIN __FUNCTIONAL_MODULE_NODE_LIST__ fm ON fm.function_id=f.id')
			->join('LEFT JOIN __NODE__ n ON n.id=fm.node_id')
			->where($condition2)
			->order('n.sort ASC')
			->field('n.id, n.name, n.pid, n.type, n.level, ur.user_id, f.id AS function_id, f.node_module, f.is_support_commission')
			->select();

			$nodeList = empty($nodeList) ? array() : $nodeList;
			$nodeList = empty($nodeList2) ? $nodeList : array_merge($nodeList, $nodeList2);
		}

		if(empty($nodeList)) {
			return array();
		}

		$tempNodeList = $firstNodeList = $moduleList = $functionList = $moduleIds = $moduleNameList = array();
		foreach($nodeList AS $val) {
			if(!$val['is_support_commission'] && $val['user_id'] != $authId) {
				continue;
			}
			$tempNodeList[$val['pid']][] = array('id' => $val['id'], 'name' => $val['name'], 'pid' => $val['pid'], 'type' => $val['type'], 'user_id' => $val['user_id']);
			$val['pid'] <= 0 && $firstNodeList[$val['pid']] = array('id' => $val['id'], 'name' => $val['name'], 'pid' => $val['pid']);
			// if(!isset($moduleList[$val['node_module']][$val['user_id']][$val['function_id']])) {
				// $moduleList[$val['node_module']][$val['user_id']][$val['function_id']] = array('uid' => array($val['user_id'] => $val['user_id']));
			// } else {
				// $moduleList[$val['node_module']][$val['user_id']][$val['function_id']]['uid'][$val['user_id']] = $val['user_id'];
			// }
			// $moduleList[$val['node_module']][$val['user_id']][$val['function_id']] = $val['function_id'];
			$functionList[$val['function_id']] = $val['function_id'];
			$moduleIds[$val['node_module']] = $val['node_module'];
		}
		if($moduleIds) {
			$moduleNameList = M('node')->where(array('id' => array('IN', $moduleIds)))->getField('id, name', true);
		}
		foreach($nodeList AS $val) {
			if(!$val['is_support_commission'] && $val['user_id'] != $authId) {
				continue;
			}
			if(!isset($moduleNameList[$val['node_module']])) {
				continue;
			}
			$tempName = strtoupper($moduleNameList[$val['node_module']]);
			$moduleList[$tempName][$val['user_id']][$val['function_id']] = $val['function_id'];
		}
		

		foreach($firstNodeList AS $appInfo) {
			if(!isset($tempNodeList[$appInfo['id']])) {
				continue;
			}

			$access[strtoupper($appInfo['name'])] = array();
			foreach($tempNodeList[$appInfo['id']] AS $moduleInfo) {
				if(!isset($tempNodeList[$moduleInfo['id']])) {
					continue;
				}
				$access[strtoupper($appInfo['name'])][strtoupper($moduleInfo['name'])] = array();
				foreach($tempNodeList[$moduleInfo['id']] AS $actionInfo) {
					if(isset($access[strtoupper($appInfo['name'])][strtoupper($moduleInfo['name'])][strtoupper($actionInfo['name'])])) {
						$access[strtoupper($appInfo['name'])][strtoupper($moduleInfo['name'])][strtoupper($actionInfo['name'])]['user_id'][$actionInfo['user_id']] = $actionInfo['user_id'];
					} else {
						$access[strtoupper($appInfo['name'])][strtoupper($moduleInfo['name'])][strtoupper($actionInfo['name'])] = array(
							'id' => $actionInfo['id'],
							'type' => $actionInfo['type'],
							'moduleName' => strtoupper($moduleInfo['name']),
							'user_id' => array($actionInfo['user_id'] => $actionInfo['user_id'])
						);
					}
				}
			}
		}

		//获取视图权限 code start
		$functionAccessList = $viewAccess = array();
		if(!empty($functionList)) {
			$tempFunctionAccessList = M('functional_module_view_access_list')->where(array('function_id' => array('IN', $functionList)))->field('id, function_id, type, view_range, range_content')->select();

			if(!empty($tempFunctionAccessList)) {
				foreach($tempFunctionAccessList AS $v) {
					$functionAccessList[$v['function_id']][$v['id']] = $v;
				}
			}
		}
		if(!empty($moduleList)) {
			foreach($moduleList AS $tempModuleName => $tempModuleInfo) {
				foreach($tempModuleInfo AS $tempUid => $tempUserInfo) {
					$tempViewAccess = array();
					foreach($tempUserInfo AS $tempFunctionId) {
						if(!isset($functionAccessList[$tempFunctionId])) {
							continue;
						}
						foreach($functionAccessList[$tempFunctionId] AS $tempViewInfo) {
							$tempViewAccess[$tempViewInfo['type']][$tempViewInfo['id']] = array('view_range' => $tempViewInfo['view_range'], 'range_content' => $tempViewInfo['range_content']);
						}
					}
					!empty($tempViewAccess) && $viewAccess[$tempModuleName][$tempUid] = $tempViewAccess;
				}
			}
		}
		//code end

		return array('node' => $access, 'view' => $viewAccess);
	}
}