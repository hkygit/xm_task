<?php
namespace Home\Model;
use Think\Model;

class BaseModel extends Model {
	Protected $_formField = array();
	protected $error = '';
	protected $tips_id = '';

	/**
     * 创建数据对象 但不保存到数据库
     * @access public
     * @param mixed $data 创建数据
     * @param string $type 状态
     * @return mixed
     */
	public function createData($data='',$type='') {
		// 如果没有传值默认取POST数据
        if(empty($data)) {
            $data   =   I('post.', '', 'trim');
        }elseif(is_object($data)){
            $data   =   get_object_vars($data);
        }
        // 验证数据
        if(empty($data) || !is_array($data)) {
            $this->error = L('_DATA_TYPE_INVALID_');
            return false;
        }

        // 状态
        $type = $type?:(!empty($data[$this->getPk()])?self::MODEL_UPDATE:self::MODEL_INSERT);

        // 检查字段映射
		$data =	$this->parseFieldsMap($data,0);

		//数据自动检测
		$res = $this->checkFormField($data, $type);
		if($res['error']) {
			$this->error = $res['msg'];
			$this->tips_id = $res['tips_id'];
			return false;
		}

        // 检测提交字段的合法性
        if(isset($this->options['field'])) { // $this->field('field1,field2...')->create()
            $fields =   $this->options['field'];
            unset($this->options['field']);
        }elseif($type == self::MODEL_INSERT && isset($this->insertFields)) {
            $fields =   $this->insertFields;
        }elseif($type == self::MODEL_UPDATE && isset($this->updateFields)) {
            $fields =   $this->updateFields;
        }
        if(isset($fields)) {
            if(is_string($fields)) {
                $fields =   explode(',',$fields);
            }
            // 判断令牌验证字段
            if(C('TOKEN_ON'))   $fields[] = C('TOKEN_NAME', null, '__hash__');
            foreach ($data as $key=>$val){
                if(!in_array($key,$fields)) {
                    unset($data[$key]);
                }
            }
        }

        // 表单令牌验证
        if(!$this->autoBaseCheckToken($data)) {
            $this->error = L('_TOKEN_ERROR_');
            return false;
        }

        // 验证完成生成数据对象
        if($this->autoCheckFields) { // 开启字段检测 则过滤非法字段数据
            $fields =   $this->getDbFields();
            foreach ($data as $key=>$val){
                if(!in_array($key,$fields)) {
                    unset($data[$key]);
                }elseif(MAGIC_QUOTES_GPC && is_string($val)){
                    $data[$key] =   stripslashes($val);
                }
            }
        }

        // 创建完成对数据进行自动处理
        $this->autoOperation($data,$type);
        // 赋值当前数据对象
        $this->data =   $data;
        // 返回创建的数据以供其他调用
        return $data;
	}

	 /**
     * 自动表单处理
     * @access public
     * @param array $data 创建数据
     * @param string $type 创建类型
     * @return mixed
     */
    private function autoOperation(&$data,$type) {
    	if(isset($this->options['auto']) && false === $this->options['auto']){
    		// 关闭自动完成
    		return $data;
    	}
        if(!empty($this->options['auto'])) {
            $_auto   =   $this->options['auto'];
            unset($this->options['auto']);
        }elseif(!empty($this->_auto)){
            $_auto   =   $this->_auto;
        }
        // 自动填充
        if(isset($_auto)) {
            foreach ($_auto as $auto){
                // 填充因子定义格式
                // array('field','填充内容','填充条件','附加规则',[额外参数])
                if(empty($auto[2])) $auto[2] =  self::MODEL_INSERT; // 默认为新增的时候自动填充
                if( $type == $auto[2] || $auto[2] == self::MODEL_BOTH) {
                    if(empty($auto[3])) $auto[3] =  'string';
                    switch(trim($auto[3])) {
                        case 'function':    //  使用函数进行填充 字段的值作为参数
                        case 'callback': // 使用回调方法
                            $args = isset($auto[4])?(array)$auto[4]:array();
                            if(isset($data[$auto[0]])) {
                                array_unshift($args,$data[$auto[0]]);
                            }
                            if('function'==$auto[3]) {
                                $data[$auto[0]]  = call_user_func_array($auto[1], $args);
                            }else{
                                $data[$auto[0]]  =  call_user_func_array(array(&$this,$auto[1]), $args);
                            }
                            break;
                        case 'field':    // 用其它字段的值进行填充
                            $data[$auto[0]] = $data[$auto[1]];
                            break;
                        case 'ignore': // 为空忽略
                            if($auto[1]===$data[$auto[0]])
                                unset($data[$auto[0]]);
                            break;
						case 'default':
                            if(empty($data[$auto[0]]))
                                $data[$auto[0]] = $auto[1];
                            break;
                        case 'string':
                        default: // 默认作为字符串填充
                            $data[$auto[0]] = $auto[1];
                    }
                    if(isset($data[$auto[0]]) && false === $data[$auto[0]] )   unset($data[$auto[0]]);
                }
            }
        }
        return $data;
    }

	// 自动表单令牌验证
    // TODO  ajax无刷新多次提交暂不能满足
    private function autoBaseCheckToken($data) {
        // 支持使用token(false) 关闭令牌验证
        if(isset($this->options['token']) && !$this->options['token']) return true;
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

	public function getErrorTipsId(){
        return $this->tips_id;
    }


	protected function checkFormField($data, $type) {
		$result = array('error' => '', 'msg' => '', 'tips_id' => '');
		$error = false;

		if(empty($this->_formField)) {
			return $result;
		}

		foreach($this->_formField as $key => $val) {
			$model = isset($val['model']) && in_array($val['model'], array(1,2,3)) ? $val['model'] : 3;

			if($type == $model || $model == 3) {
				$fieldValue = $data[$val['field']];
				switch($val['type']) {
					//不能为空
					case 'mustInput':
						strlen($fieldValue) <= 0 &&  $error = true;
						break;
					//检测两个值是否相等
					case 'mustEqualTo':
						strlen($data[$val['extend']]) > 0 && $data[$val['extend']] != $fieldValue && $error = true;
						break;
					//必须email格式
					case 'mustEmail':
						strlen($fieldValue) > 0 && !preg_match('/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/i', $fieldValue) && $error = true;
						break;
					//长度必须大于
					case 'lengthMustMoreThan':
						strlen($fieldValue) > 0 && mb_strlen($fieldValue, C('DB_CHARSET')) <= $val['extend'] && $error = true;
						break;
					//长度必须不大于
					case 'lengthNotMoreThan':
						strlen($fieldValue) > 0 && mb_strlen($fieldValue, C('DB_CHARSET')) > $val['extend'] && $error = true;
						break;
					//长度必须小于
					case 'lengthMustLessThan':
						strlen($fieldValue) > 0 && mb_strlen($fieldValue, C('DB_CHARSET')) >= $val['extend'] && $error = true;
						break;
					//长度必须不小于
					case 'lengthNotLessThan':
						strlen($fieldValue) > 0 && mb_strlen($fieldValue, C('DB_CHARSET')) < $val['extend'] && $error = true;
						break;
					//必须为数字
					case 'mustNum':
						strlen($fieldValue) > 0 && !is_numeric($fieldValue) && $error = true;
						break;
					//值必须大于
					case 'mustMoreThan':
						strlen($fieldValue) > 0 && $fieldValue <= $val['extend'] && $error = true;
						break;
					//值必须不大于
					case 'notMoreThan':
						strlen($fieldValue) > 0 && $fieldValue > $val['extend'] && $error = true;
						break;
					//值必须小于
					case 'mustLessThan':
						strlen($fieldValue) > 0 && $fieldValue >= $val['extend'] && $error = true;
						break;
					//值必须不小于
					case 'notLessThan':
						strlen($fieldValue) > 0 && $fieldValue < $val['extend'] && $error = true;
						break;
					//值必须为整数
					case 'mustIntNum':
						strlen($fieldValue) > 0 && (!is_numeric($fieldValue) || strpos($fieldValue, '.') !== false) && $error = true;
						break;
                    //值必须为正数
                    case 'mustPositiveNum':
                        !preg_match('/^\d+(\.\d+)?$/', $fieldValue) && $error = true;
                        break;
                    //值正数部分长度不得大于
                    case 'PositiveLengthNotMoreThan':
                        strlen($fieldValue) > 0 && mb_strlen(round($fieldValue, 0), C('DB_CHARSET')) > $val['extend'] && $error = true;
                        break;
					//必须为url格式
					case 'mustUrl':
						!preg_match('/(http[s]?:\/\/)?[a-zA-Z0-9-]+(\.[a-zA-Z0-9]+)+/i', $fieldValue) && $error = true;
						break;
					//由字母数字和下划线组成
					case 'mustLetter':
						strlen($fieldValue) > 0 && !preg_match('/^\w+$/i', $fieldValue) && $error = true;
						break;
					//检测手机号码格式
					case 'telephone':
						strlen($fieldValue) > 0 && !preg_match('/^0?1((3|8|7)[0-9]|5[0-35-9]|4[579])\d{8}$/i', $fieldValue) && $error = true;
						break;
					//检测固定电话格式
					case 'phone':
						strlen($fieldValue) > 0 && !preg_match('/^\d{3}-\d{8}|\d{4}-\d{7,8}$/i', $fieldValue) && $error = true;
						break;
					case 'mustQQ':
						strlen($fieldValue) > 0 && !preg_match('/^[1-9][0-9]{4,}$/', $fieldValue) && $error = true;
						break;
					case 'in':
						if(strlen($fieldValue) > 0) {
							if(is_array($val['extend'])) {
								!in_array($fieldValue, $val['extend']) && $error = true;
							} else {
								$arr = explode(',', $val['extend']);
								!in_array($fieldValue, $arr) && $error = true;
							}
						}
						break;
					case 'function':
						if(strlen($fieldValue) > 0 && function_exists($val['extend'])) {
							if(!$val['extend']($fieldValue)) {
								$error = true;
							}
						}
						break;
					case 'mustIP':
						strlen($fieldValue) > 0 && !preg_match('/^(?:(?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))\.){3}(?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))$/', $fieldValue) && $error = true;
						break;
				}
			}

			if($error) {
				break;
			}
		}

		$error && $result = array('error' => '1', 'msg' => $val['msg'], 'tips_id' => $val['tips_id']);
		return $result;
	}

	public function getCreatedData() {
		return $this->data;
	}
}