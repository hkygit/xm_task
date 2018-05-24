<?php
/**
+----------------------------------------------------------
 * 实例化服务
+----------------------------------------------------------
 * @param  string   $name	要实例化的服务名称
+----------------------------------------------------------
 * @param  array    $params	传入的参数
+----------------------------------------------------------
 * @return object
+----------------------------------------------------------
 */
function service($name, $params=array()) {
    return X($name, $params=array(), 'Service');
}

/**
+----------------------------------------------------------
 * 调用接口服务
+----------------------------------------------------------
 * @param  string   $name	要实例化的接口名称
+----------------------------------------------------------
 * @param  array    $params	传入的参数
+----------------------------------------------------------
 * @param  string   $layer	接口后缀名
+----------------------------------------------------------
 * @return object
+----------------------------------------------------------
 */
function X($name, $params=array(), $layer='Service') {
    static $_service = array();

    $class = MODULE_NAME.'\\'.$layer.'\\'.$name.$layer;
    if(isset($_service[$class])) {
        return $_service[$class];
    }
    $filename = APP_PATH.str_replace('\\', '/', $class).EXT;

    if(is_file($filename)) {
        // Win环境下面严格区分大小写
        if (IS_WIN && false === strpos(str_replace('/', '\\', realpath($filename)), $class . EXT)){
            return ;
        }

        if(!require_cache($filename)) {
            return ;
        }
    }

    if(class_exists($class)) {
        $obj   =  new $class($params);
        $_service[$class] =  $obj;
        return $obj;
    }
}

/**
+----------------------------------------------------------
 * 获取对应的语言包数据
+----------------------------------------------------------
 * @param  string    $name	要获取的语言包键值（多层级可用点，例如：ACTION_HISTORY.common.created）
+----------------------------------------------------------
 * @param  array    $value	替换语言包对应变量
+----------------------------------------------------------
 * @return void
+----------------------------------------------------------
 */
function fetch_lang($name=null, $value=null) {
    static $_lang = array();
    // 空参数返回所有定义
    if (empty($name)) {
        return $_lang;
    }
    // 判断语言获取(或设置)
    // 若不存在,返回false
    if (is_string($name)) {
        if(strpos($name, '.') === false) {
            $name   =   strtoupper($name);
            $langs = isset($_lang[$name]) ? $_lang[$name] : null;
        } else {
            $lang_keys = explode('.', $name);
            $name   =   strtoupper($name);

            $langs = $_lang;
            foreach($lang_keys as $k => $lang_key) {
                $lang_key = $k == 0 ? strtoupper($lang_key) : $lang_key;
                if(!isset($langs[$lang_key])) {
                    $langs = null;
                    break;
                }

                $langs = $langs[$lang_key];
            }
        }

        if (is_null($value)){
            return $langs;
        }elseif(is_array($value)){
            // 支持变量
            $replace = array_keys($value);
            foreach($replace as &$v){
                $v = '{$'.$v.'}';
            }
            return str_replace($replace,$value,$langs);
        }
        $_lang[$name] = $value; // 语言定义
        return null;
    }
    // 批量定义
    if (is_array($name))
        $_lang = array_merge($_lang, array_change_key_case($name, CASE_UPPER));
    return null;
}

/**
+----------------------------------------------------------
 * 根据传入的操作记录数据输出相关信息
+----------------------------------------------------------
 * @param  array    $action
+----------------------------------------------------------
 * @return void
+----------------------------------------------------------
 */
function printActionLog($action) {
    $objectType = $action['object_type'];
    $actionType = strtolower($action['action']);

    $action_info = $action;
    if(isset($action_info['extra'])) {
        unset($action_info['extra']);
    }
    if(isset($action_info['history'])) {
        unset($action_info['history']);
    }

    if($action_info['actor'] == '') {
        $action_info['actor'] = '系统';
    } elseif($action_info['actor_id'] != $action_info['real_actor_id']) {
        $action_info['actor'] .= '('.$action_info['real_actor'].' 代操作)';
    }
    $desc = fetch_lang('ACTION_HISTORY.'.$objectType.'.action.'.$actionType, $action_info);
    empty($desc) && $desc = fetch_lang('ACTION_HISTORY.common.'.$actionType, $action_info);

    if(!isset($desc)) {
        return false;
    }

    if(is_array($desc)) {
        if(isset($desc['extra'])) {
            if(is_array($desc['extra']) && isset($desc['extra']['func']) && function_exists($desc['extra']['func'])) {
                $desc_extra = call_user_func($desc['extra']['func'], $action['extra']);
            } else {
                $desc_extra = fetch_lang('ACTION_HISTORY.'.$objectType.'.'.$desc['extra'].'.'.$action['extra']);
            }
        }
        if(isset($desc_extra)) {
            if(empty($desc_extra)) {
                return str_replace('{$extra}', $desc_extra, $desc['main_1']);
            } else {
                return str_replace('{$extra}', $desc_extra, $desc['main']);
            }
        } else {
            return $desc['main'];
        }
    } else {
        return str_replace('{$extra}', $action['extra'], $desc);
    }

    return $desc;
}

/**
+----------------------------------------------------------
 * 根据传入的操作记录具体数据输出相关信息
+----------------------------------------------------------
 * @param  integer    $objectType	对象类型
+----------------------------------------------------------
 * @param  array    $history	操作记录具体数据
+----------------------------------------------------------
 * @return void
+----------------------------------------------------------
 */
function printActionLogData($objectType, $history) {
    if(empty($history)) {
        return false;
    }

    $objectTypeList = C('ACTION_OBJECT_TYPE');
    $objectType = $objectTypeList[$objectType];
    $history['field'] 		= 	fetch_lang('ACTION_FIELD.'.$objectType.'.'.$history['field']);

    if(is_array($history['field'])) {
        if(is_array($history['field']) && isset($history['field']['func']) && function_exists($history['field']['func'])) {
            $history_new = call_user_func($history['field']['func'], $history['new']);
            $history_old = call_user_func($history['field']['func'], $history['old']);

            return sprintf(
                fetch_lang('action_log_diff1'),
                isset($history['field']['name']) ? $history['field']['name'] : $history['field'],
                $history_old !== false ? $history_old : '',
                $history_new !== false ? $history_new : ''
            );
        }

        return sprintf(
            fetch_lang('action_log_diff1'),
            isset($history['field']['name']) ? $history['field']['name'] : $history['field'],
            isset($history['field']['value'][$history['old']]) ? $history['field']['value'][$history['old']] : $history['old'],
            isset($history['field']['value'][$history['new']]) ? $history['field']['value'][$history['new']] : $history['new']
        );
    }

    if($history['diff'] != '') {
        $history['diff'] 		= 	str_replace(array('<ins>', '</ins>', '<del>', '</del>'), array('[ins]', '[/ins]', '[del]', '[/del]'), $history['diff']);
        $history['diff'] 		=  	htmlspecialchars($history['diff']);
        $history['diff'] 		=	str_replace(array('[ins]', '[/ins]', '[del]', '[/del]'), array('<ins>', '</ins>', '<del>', '</del>'), $history['diff']);
        $history['diff'] 		= 	nl2br($history['diff']);
        $history['noTagDiff'] 	= 	preg_replace('/&lt;\/?([a-z][a-z0-9]*)[^\/]*\/?&gt;/Ui', '', $history['diff']);
        return sprintf(fetch_lang('action_log_diff2'), $history['field'], $history['noTagDiff'], $history['diff']);
    }

    return sprintf(fetch_lang('action_log_diff1'), $history['field'], $history['old'], $history['new']);
}

/**
+----------------------------------------------------------
 * 根据用户id获取用户名
+----------------------------------------------------------
 * @param  integer    $uid
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function getUserById($uid) {
    if(!$uid) {
        return false;
    }

    return M('user')->where(array('user_id' => (int)$uid))->getField('user_name');
}

/**
+----------------------------------------------------------
 * 根据用户id获取上级领导ID
+----------------------------------------------------------
 * @param  integer    $uid
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function getLeaderIdById($uid) {
    if(!$uid) {
        return false;
    }

    return M('user')->where(array('user_id' => (int)$uid))->getField('pid');
}

function getWorkById($id) {
    if(!$id) {
        return false;
    }

    return M('work')->where(array('id' => ':id'))->bind(':id', $id, \PDO::PARAM_INT)->getField('name');
}

/**
+----------------------------------------------------------
 * 根据事业部id获取事业部名
+----------------------------------------------------------
 * @param  integer    $id
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function getDivisionById($id) {
    if(!$id) {
        return false;
    }
    return M('division')->where(array('id' => (int)$id))->getField('name');
}

/**
+----------------------------------------------------------
 * 根据产品线id获取产品线名
+----------------------------------------------------------
 * @param  integer    $id
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function getProlineById($id) {
    if(!$id) {
        return false;
    }
    return M('materiel_proline')->where(array('id' => (int)$id))->getField('name');
}

/**
+----------------------------------------------------------
 * 根据销售产品id获取销售产品名称
+----------------------------------------------------------
 * @param  integer    $id
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function getSaleProductById($id) {
    if(!$id) {
        return false;
    }
    return M('sale_product')->where(array('id' => (int)$id))->getField('name');
}


/**
+----------------------------------------------------------
 * 根据用户id获取用户名
+----------------------------------------------------------
 * @param  string    $ids 用户ID以逗号分隔的字符串或者数组
+----------------------------------------------------------
 * @return string	逗号分隔的用户名
+----------------------------------------------------------
 */
function getUserInIds($ids){
    if(!$ids) {
        return '';
    }
    $namesArr = M('user')->where(array('user_id'=>array('in', $ids)))->getField('user_name', true);
    $namesStr = !empty($namesArr) ? implode(',', $namesArr) : '';
    return $namesStr;
}

/**
+----------------------------------------------------------
 * 根据用户角色id获取角色名
+----------------------------------------------------------
 * @param  integer    $id
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function getRoleById($id) {
    if(!$id) {
        return false;
    }

    return M('role')->where(array('id' => (int)$id))->getField('name');
}

/**
+----------------------------------------------------------
 * 根据产品id获取产品名
+----------------------------------------------------------
 * @param  integer    $pid
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function getProductById($pid) {
    if(!$pid) {
        return false;
    }

    return M('product')->where(array('id' => (int)$pid, 'status' => 1))->getField('name');
}

/**
+----------------------------------------------------------
 * 根据项目id获取项目名
+----------------------------------------------------------
 * @param  integer    $pid
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function getProjectById($pid) {
    if(!$pid) {
        return false;
    }

    return M('project')->where(array('id' => (int)$pid, 'status' => array('in', '1,2,-2,-4')))->getField('name');
}


/**
+----------------------------------------------------------
 * 根据产品分类id获取产品分类
+----------------------------------------------------------
 * @param  integer    $pid
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function getProductTypeById($id) {
    if(!$id) {
        return false;
    }

    return M('productType')->where(array('id' => (int)$id, 'status' => 1))->getField('name');
}

/**
+----------------------------------------------------------
 * 根据部门id获取部门名
+----------------------------------------------------------
 * @param  integer    $deptid
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function getDeptById($deptid) {
    if(!$deptid) {
        return false;
    }

    return M('dept')->where(array('id' => (int)$deptid))->getField('name');
}

/**
+----------------------------------------------------------
 * 根据人获取部门名
+----------------------------------------------------------
 * @param  integer    $deptid
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function getDeptIdByUserId($user_id) {
    if(!$user_id) {
        return false;
    }

    return M('user')->where(array('user_id' => (int)$user_id))->getField('dept');
}

/**
+----------------------------------------------------------
 * 根据部门id获取部门名
+----------------------------------------------------------
 * @param  string    $deptids
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function getFormatDeptByIds($deptids) {
    $deptids = base64_decode($deptids);
    if(strlen($deptids) <= 0) {
        return false;
    }

    $deptNames = M('dept')->where(array('id' => array('in', $deptids)))->getField('name', true);
    $names = '';
    foreach($deptNames as $name){
        $names .= $name.'、';
    }

    return rtrim($names, '、');
}

/**
+----------------------------------------------------------
 * 根据原始需求id获取原始需求名称
+----------------------------------------------------------
 * @param  integer    $oid
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function getOriDemandById($oid){
    if(!$oid) {
        return false;
    }

    return M('original_demand')->where(array('id' => (int)$oid))->getField('name');
}

/**
+----------------------------------------------------------
 * 根据需求id获取需求名称
+----------------------------------------------------------
 * @param  integer    $did
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function getDemandById($did){
    if(!$did) {
        return false;
    }

    return M('demand')->where(array('id' => (int)$did))->getField('name');
}

/**
+----------------------------------------------------------
 * 根据任务id获取任务名称
+----------------------------------------------------------
 * @param  integer    $tid
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function getTaskById($tid){
    if(!$tid) {
        return false;
    }

    return M('task')->where(array('id' => (int)$tid))->getField('name');
}


/**
+----------------------------------------------------------
 * 根据用户id获取用户所在部门名称
+----------------------------------------------------------
 * @param  integer    $uid
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function getDeptNameByUid($uid){
    if(!$uid) {
        return false;
    }
    $dept = M('user')->where(array('user_id' => (int)$uid))->getField('dept');
    return getDeptById((int)$dept);
}


/**
+----------------------------------------------------------
 * 检测是否有重复登录账号
+----------------------------------------------------------
 * @param  string    $account
+----------------------------------------------------------
 * @return bool
+----------------------------------------------------------
 */
function checkLoginAccount($account) {
    if(!$account) {
        return false;
    }

    $user = M('user')->where(array('login_account' => $account))->field('user_id, status')->find();
    return empty($user) || $user['status'] == '-1' ? true : false;
}

/**
+----------------------------------------------------------
 * 检测是否有重复工号
+----------------------------------------------------------
 * @param  string    $jobNum
+----------------------------------------------------------
 * @return bool
+----------------------------------------------------------
 */
function checkJobNumber($jobNum) {
    if(!$jobNum) {
        return false;
    }

    $user = M('user')->where(array('job_number' => $jobNum))->field('user_id, status')->find();
    return empty($user) || $user['status'] == '-1' ? true : false;
}

/**
+----------------------------------------------------------
 * 检测是否可注册用户名
+----------------------------------------------------------
 * @param  string    $userName
+----------------------------------------------------------
 * @return bool
+----------------------------------------------------------
 */
function checkUserName($userName) {
    if(!$userName) {
        return false;
    }

    $user = M('user')->where("user_name='%s'", $userName)->field('user_id, status')->find();
    return empty($user) || $user['status'] == '-1' ? true : false;
}

/**
+----------------------------------------------------------
 * 检测Ip是否已被绑定
+----------------------------------------------------------
 * @param  string    $ip
+----------------------------------------------------------
 * @return bool
+----------------------------------------------------------
 */
function checkBindIp($ip) {
    if(!$ip) {
        return false;
    }

    $user = M('user')->where(array('job_ip' => $ip))->field('user_id, status')->find();
    return empty($user) || $user['status'] == '-1' ? true : false;
}

/**
+----------------------------------------------------------
 * 检测邮箱是否存在
+----------------------------------------------------------
 * @param  string    $email
+----------------------------------------------------------
 * @return bool
+----------------------------------------------------------
 */
function checkUserEmail($email) {
    if(!$email) {
        return false;
    }

    $user = M('user')->where(array('email' => $email))->field('user_id, status')->find();
    return empty($user) || $user['status'] == '-1' ? true : false;
}

/**
+----------------------------------------------------------
 * 检测需求名称是否重复
+----------------------------------------------------------
 * @param  integer    $name
+----------------------------------------------------------
 * @return bool
+----------------------------------------------------------
 */
function checkDemandName($name) {
    if(!$name) {
        return false;
    }

    $demand = M('demand')->where("name='%s'", $name)->getField('id');
    return empty($demand) ? true : false;
}

/**
+----------------------------------------------------------
 * 检测bug标题是否重复
+----------------------------------------------------------
 * @param  integer    $name
+----------------------------------------------------------
 * @return bool
+----------------------------------------------------------
 */
function checkBugName($name) {
    if(!$name) {
        return false;
    }

    $bug = M('bug')->where("name='%s'", $name)->getField('id');
    return empty($bug) ? true : false;
}

/**
+----------------------------------------------------------
 * 检测需求单号是否重复
+----------------------------------------------------------
 * @param  string    $orderSn
+----------------------------------------------------------
 * @return bool
+----------------------------------------------------------
 */
function checkDemandOrderSn($orderSn) {
    if(!$orderSn) {
        return true;
    }

    $demand = M('demand')->where("order_sn='%s'", $orderSn)->getField('id');
    return empty($demand) ? true : false;
}

/**
+----------------------------------------------------------
 * 获取指定部门下的所有子部门的ID集合
+----------------------------------------------------------
 * @param  array    $deptIds
+----------------------------------------------------------
 * @return array
+----------------------------------------------------------
 */
function childDeptIds($deptIds = 0) {

    if(is_numeric($deptIds)){
        $deptIds = array((int)$deptIds);
    }

    $tree = $deptIds;
    do{
        $deptIds = M('dept')->where(array('pid'=>array('in', $deptIds)))->getField('id', true);
        $deptIds = !empty($deptIds) ? $deptIds : array();
        $tree = array_merge($tree, $deptIds);

    }while(!empty($deptIds));
    return $tree;
}

/**
+----------------------------------------------------------
 *	对数组进行重新组合，让参数$index成为新数组的索引，$value成为值
+----------------------------------------------------------
 *	$param string $index 新数组的索引
+----------------------------------------------------------
 *	$param string $value 新数组的值
+----------------------------------------------------------
 *	return 返回新数组
+----------------------------------------------------------
 */
function changeArray($arr, $index, $value) {
    $newarr = array();

    if(empty($arr)) {
        return false;
    }

    foreach($arr as $k => $v) {
        $newarr[$v[$index]] = $v[$value];
    }

    return $newarr;
}

/**
+----------------------------------------------------------
 *	获取二维数组第二维的某个索引的值组成的数组$index成为新数组的索引，$value成为值
+----------------------------------------------------------
 *	$param array $array 要操作的数据
+----------------------------------------------------------
 *	$param string $index 二维数组的某个键值
+----------------------------------------------------------
 *	return 返回新数组
+----------------------------------------------------------
 */
function getTwoDimensionData($array, $index) {
    $result = array();

    if(!empty($array) && is_array($array)) {
        foreach($array as $value) {
            $result[] = $value[$index];
        }
    }

    return $result;
}

/**
+----------------------------------------------------------
 *	获取格式化的当前时间
+----------------------------------------------------------
 *	return 返回格式化的当前时间
+----------------------------------------------------------
 */
function create_now_datetime() {
    return date('Y-m-d H:i:s');
}

/**
+----------------------------------------------------------
 * 邮件发送
+----------------------------------------------------------
 * @param: $email[string]       接收人邮件地址
+----------------------------------------------------------
 * @param: $subject[string]     邮件标题
+----------------------------------------------------------
 * @param: $content[string]     邮件内容
+----------------------------------------------------------
 * @param: $realname[string]        接收人姓名
+----------------------------------------------------------
 * @param: $cc[array]  以email和用户名组成的二维数组。email必须有，name可选、example:array(array('email' => 'xxx@xx.xx', 'name' => 'xx'), array('email': 'xxx@xx.x'))
+----------------------------------------------------------
 * @return boolean
 */
function sendMail($email, $subject, $content, $realname = '', $cc = array(), $attachment = array()) {
    import("Home.Util.PHPMailer.PHPMailer");
    $mail = new \PHPMailer();
    $mail->IsSMTP();
    $mail->Host = C('MAIL_SMTP');
    $mail->SMTPAuth = true;         //启用smtp认证
    $mail->Username = C('MAIL_USER');   // 你的邮箱地址
    $mail->Password = C('MAIL_PWD');      //你的邮箱密码

    $mail->From = C('MAIL_USER');            //发件人地址（也就是你的邮箱）
    $mail->FromName = "TASK系统";              //发件人姓名

    $email = trim($email);
    $mail->AddAddress("{$email}", $realname);
    $mail->AddReplyTo(C('MAIL_USER'));

    if(!empty($cc)) {
        foreach($cc as $val) {
            $val['email'] = str_replace(' ', '', $val['email']);
            $val['name'] = empty($val['name']) ? '' : str_replace(' ', '', $val['name']);
            $mail->AddCC($val['email'], $val['name']);
        }
    }

    if(!empty($attachment)) {
        foreach($attachment as $val) {
            $mail->AddAttachment($val['path'], $val['name']);
        }
    }

    $mail->WordWrap = 50;
    $mail->IsHTML(true);

    $mail->CharSet = C('DEFAULT_CHARSET');    //设置邮件编码
    $subject = stripslashes($subject);
    $mail->Subject = $subject;          //邮件主题
    $content = stripslashes($content);
    $mail->Body    = $content;        //邮件内容
    $mail->AltBody = "Your mail clients don't support HTML show"; //邮件正文不支持HTML的备用显示

    if(!$mail->Send()) {
        return $mail->ErrorInfo;
    } else {
        return true;
    }
}

//过滤非安全字符
function remove_xss($val) {
    // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
    // this prevents some character re-spacing such as <java\0script>
    // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
    $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

    // straight replacements, the user should never need these since they're normal characters
    // this prevents like <IMG SRC=@avascript:alert('XSS')>
    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&*()';
    $search .= '~`";:?+/={}[]-_|\'\\';
    for ($i = 0; $i < strlen($search); $i++) {
        // ;? matches the ;, which is optional
        // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

        // @ @ search for the hex values
        $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
        // @ @ 0{0,7} matches '0' zero to seven times
        $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
    }

    // now the only remaining whitespace attacks are \t, \n, and \r
    $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', /*'style',*/ 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', /*'title',*/ 'base');
    $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $ra = array_merge($ra1, $ra2);

    $found = true; // keep replacing as long as the previous round replaced something
    while ($found == true) {
        $val_before = $val;
        for ($i = 0; $i < sizeof($ra); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                    $pattern .= '|';
                    $pattern .= '|(&#0{0,8}([9|10|13]);)';
                    $pattern .= ')*';
                }
                $pattern .= $ra[$i][$j];
            }
            $pattern .= '/i';
            $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
            $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
            if ($val_before == $val) {
                // no replacements were made, so exit the loop
                $found = false;
            }
        }
    }
    return $val;
}

//正则替换字符串中图片的相对url为带域名的绝对url
function imageChangeToFullUrl($string) {
    return preg_replace_callback('/<img.*?src=\"(.*?)\".*?\/>/i', function($matches) {
        $parse_url = parse_url($matches[1]);
        $url = '';
        if(empty($parse_url['host'])) {
            $domain = $_SERVER['HTTP_HOST'];
            if(C('APP_SUB_DOMAIN_DEPLOY') ) { // 开启子域名部署
                $domain = $domain=='localhost'?'localhost':'www'.strstr($_SERVER['HTTP_HOST'],'.');
                // '子域名'=>array('模块[/控制器]');
                foreach (C('APP_SUB_DOMAIN_RULES') as $key => $rule) {
                    $rule   =   is_array($rule)?$rule[0]:$rule;
                    if(false === strpos($key,'*') && 0=== strpos($url,$rule)) {
                        $domain = $key.strstr($domain,'.'); // 生成对应子域名
                        $url    =  substr_replace($url,'',0,strlen($rule));
                        break;
                    }
                }
            }

            $url = (is_ssl()?'https://':'http://').$domain.'/'.$matches[1];
        } else {
            $url = $matches[1];
        }
        return  str_replace($matches[1], $url, $matches[0]);
    }, $string);
}

//字符串中图片（本地图片，不支持网络图片）和文本分离
function imageTextToSeparate($string) {
    $files = array('text'=>'', 'img'=>array());

    $text = preg_replace('/<img.*?\/>/', '', $string);
    if($text){
        $files['text'] = $text;
    }

    $matches = array();
    if(preg_match_all('/<img.*?src=\"(.*?)\".*?\/>/i', $string, $matches)){
        foreach($matches[1] as $v){
            $parse_url = parse_url($v);

            if((!empty($parse_url['host']) && ($parse_url['host'] == $_SERVER['HTTP_HOST'])) || empty($parse_url['host'])){
                if(C('HOST_TRUE_PATH')){
                    $path = C('HOST_TRUE_PATH').$parse_url['path'];
                    $files['img'][] = $path;
                }elseif(defined('ROOT_PATH')){
                    $path = ROOT_PATH.'/'.$parse_url['path'];
                    $files['img'][] = $path;
                }
            }
        }
    }

    return $files;
}

/**
+----------------------------------------------------------
 * 截取当前编码下的字符串
+----------------------------------------------------------
 * @param: string $str       要截取的字符串
+----------------------------------------------------------
 * @param: intval $length     要截取的长度
+----------------------------------------------------------
 * @param: string $append     超出长度截取附加的字符串，默认省略号
+----------------------------------------------------------
 * @return string
 */
function sub_str($str, $length = 0, $append = '...') {
    $str = trim($str);
    $strlength = strlen($str);

    if ($length == 0 || $length >= $strlength) {
        return $str;
    } elseif ($length < 0) {
        $length = $strlength + $length;
        if($length < 0) {
            $length = $strlength;
        }
    }

    if (function_exists('mb_substr')) {
        $newstr = mb_substr($str, 0, $length, C('DB_CHARSET'));
    } elseif (function_exists('iconv_substr')) {
        $newstr = iconv_substr($str, 0, $length, C('DB_CHARSET'));
    } else {
        $newstr = substr($str, 0, $length);
    }

    if($str != $newstr) {
        $newstr .= $append;
    }

    return $newstr;
}

/**
+----------------------------------------------------------
 * 根据附件数据返回附件html
+----------------------------------------------------------
 * @param: intval $id      		附件id
+----------------------------------------------------------
 * @param: string $title      	附件名称
+----------------------------------------------------------
 * @param: string $createTime	附件上传时间
+----------------------------------------------------------
 * @param: intval $size     	附件大小 单位byte
+----------------------------------------------------------
 * @param: string $ext     		附件后缀
+----------------------------------------------------------
 * @return string
 */

function getAttachmentHtml($id, $title, $createTime, $size, $ext) {
    $kbSize = round($size/1024, 2);
    $mbSize = round($size/1024/1024, 2);
    $formatSize = $mbSize >= 1 ? $mbSize.'MB' : $kbSize.'KB';
    $url = U('file/downloads', array('id' => $id));

    $extList = array('7z', 'accd', 'asp', 'avi', 'bat', 'bmp', 'bsp', 'chm', 'css', 'dat', 'dll', 'doc', 'docx', 'dwt', 'emf', 'eml', 'eps', 'exe', 'file', 'gif', 'gzip', 'html', 'ico', 'ind', 'ini', 'jpeg', 'jpg', 'js', 'jsp', 'lbi', 'midi', 'mov', 'mp3', 'mp4', 'mpeg', 'pdf', 'php', 'png', 'ppt', 'proj', 'pst', 'pub', 'rar', 'raw', 'read', 'rm', 'rmvb', 'sql', 'swf', 'tar', 'tif', 'txt', 'unknow', 'url', 'vsd', 'wav', 'wma', 'wmv', 'xls', 'xlsx', 'xmind', 'xml', 'zip');
    $extClass = strtolower($ext);
    $extClass = in_array($extClass, $extList) ? $extClass : 'unknow';

    return <<<EOT
<div class="att_box att_$extClass" title="{$title}.{$ext}">
	<div class="att_info">
		{$title}.{$ext}
	</div>
	<div class="att_action">
		<i class="fl">$createTime</i>

		<div class="fl att_action_list">
			<a href="$url" target="_blank">下载</a><i>($formatSize)</i>
		</div>
	</div>
</div>
EOT;
}

/**
+----------------------------------------------------------
 * 获取某个年份的排班数据
+----------------------------------------------------------
 * @param: intval $year      		年份
+----------------------------------------------------------
 * @return array
 */
function getYearScheduling($year) {
    if(!$year) {
        return false;
    }

    $scheduling = S('task_scheduling_'.$year);
    if(empty($scheduling)) {
        $list = M('scheduling')->where(array('dates' => array(array('egt', $year.'-01-01'), array('elt', $year.'-12-31'))))->field('dates, is_working, remark, special, work_type, is_festival')->order('work_type ASC, dates ASC')->select();
        $scheduling = array();
        foreach($list as $val) {
            $unixTime = strtotime($val['dates']);
            $month = date('n', $unixTime);
            $day = date('j', $unixTime);
            $week = date('N', $unixTime);

            $scheduling[$val['work_type']][$month][$day] = array('is_working' => $val['is_working'], 'day' => $day, 'special' => $val['special'], 'remark' => $val['remark'], 'is_festival' => $val['is_festival'], 'week' => $week);
            if($val['is_working'] == '1') {
                $scheduling[$val['work_type']][$month]['all'] = isset($scheduling[$val['work_type']][$month]['all']) ? $scheduling[$val['work_type']][$month]['all'] + 1 : 1;
            }
        }

        S('task_scheduling_'.$year, $scheduling);
    }

    return $scheduling;
}

/**
+----------------------------------------------------------
 * 获取某个人的上班时间设置
+----------------------------------------------------------
 * @param: intval $uid      		用户id
+----------------------------------------------------------
 * @param: intval $dept      		所属部门id(如果此参数为空，则会去数据库查询)
+----------------------------------------------------------
 * @return array
 */
function getWorkTimeSettingByUid($uid, $dept = 0) {
    $work_time_setting = getWorkTimeSetting();
    if(!$dept) {
        $dept = M('user')->where(array('user_id' => ':user_id'))->bind(':user_id', $uid, \PDO::PARAM_INT)->getField('dept');
    }

    if(!empty($work_time_setting[1][2][$uid])) {	//检测是否有个人的个性定制考勤
        $real_work_time = $work_time_setting[1][2][$uid];
    } elseif(!empty($work_time_setting[1][1][$dept])) {	//检测是否有部门的个性定制考勤
        $real_work_time = $work_time_setting[1][1][$dept];
    } else {
        $deptInfo = M('dept')->where(array('id' => ':id'))->cache(3600)->bind(':id', $dept, \PDO::PARAM_INT)->field('name, path')->find();
        if(empty($deptInfo)) {
            return false;
        }
        $deptPathIds = explode(',', $deptInfo['path']);
        array_pop($deptPathIds);
        if(!empty($deptPathIds)) {
            $deptPathIds = array_reverse($deptPathIds);
            foreach($deptPathIds as $v) {
                if(!empty($work_time_setting[1][1][$v])) {
                    $real_work_time = $work_time_setting[1][1][$v];
                    break;
                }
            }
        }

        if(empty($real_work_time)) {
            if(empty($work_time_setting[0])) {
                return false;
            }
            $real_work_time = $work_time_setting[0];
        }
    }

    return $real_work_time;
}

//获取上班时间设置数据
function getWorkTimeSetting() {
    $work_time_setting = S('work_time_setting');
    if(empty($work_time_setting)) {
        $work_time_setting_info = M('config')->where(array('name' => 'work_time_setting'))->field('name, value')->find();
        $work_time_setting = array();
        if(!empty($work_time_setting_info['value'])) {
            $work_time_setting = json_decode($work_time_setting_info['value'], true);
        }

        if(empty($work_time_setting)) {
            return false;
        }
        S('work_time_setting', $work_time_setting);
    }

    return $work_time_setting;
}

/**
+----------------------------------------------------------
 * 获取今天第几个考勤签到
+----------------------------------------------------------
 * @param  date    $date 	考勤日期
+----------------------------------------------------------
 * @param  boolean    $onlyRead 	是否只读，不加1
+----------------------------------------------------------
 * @return intval
+----------------------------------------------------------
 */
function signInNum($date, $onlyRead = false) {
    $key = 'signInNum'.$date;

    $signNum = S($key);
    if($signNum === false) {
        $signNum = M('attendance')->where(array('dates' => $date))->count('id');
        S($key, $signNum, 24*3600);
    } else {
        if(!$onlyRead) {
            $signNum += 1;
            S($key, $signNum, 24*3600);
        }
    }

    return $signNum;
}

/**
+----------------------------------------------------------
 * 获取今天第几个考勤签出
+----------------------------------------------------------
 * @param  date    $date 	考勤日期
+----------------------------------------------------------
 * @param  boolean    $onlyRead 	是否只读，不加1
+----------------------------------------------------------
 * @return intval
+----------------------------------------------------------
 */
function checkoutNum($date, $onlyRead = false) {
    $key = 'checkoutNum'.$date;

    $checkoutNum = S($key);
    if($checkoutNum === false) {
        $checkoutNum = M('attendance')->where(array('dates' => $date, 'check_out_type' => array('gt', 0)))->count('id');
        S($key, $checkoutNum, 24*3600);
    } else {
        if(!$onlyRead) {
            $checkoutNum += 1;
            S($key, $checkoutNum, 24*3600);
        }
    }

    return $checkoutNum;
}

//获取总考勤人数
function getSignTotalNum() {
    $key = 'signInTotalNum';
    $num = S($key);

    if(!$num) {
        $num = M('user')->where(array('status' => array('egt', '1'), 'check_attendance' => '1'))->count('user_id');
        S($key, $num);
    }

    return $num;
}

/**
+----------------------------------------------------------
 * 获取时间段称呼(上午，中午等)
+----------------------------------------------------------
 * @param  intval    $time 	要格式化的时间戳
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function getTimePeriodName($time) {
    $date = date('Y-m-d', $time);

    $dateObj = new \DateTime($date.' 06:00');
    $afternoonStart = $dateObj->getTimestamp();
    $dateObj = new \DateTime($date.' 11:00');
    $middayStart = $dateObj->getTimestamp();
    $dateObj = new \DateTime($date.' 13:00');
    $middayEnd = $dateObj->getTimestamp();
    $dateObj = new \DateTime($date.' 18:00');
    $nightStart = $dateObj->getTimestamp();

    if($time < $afternoonStart) {
        $name = '凌晨';
    } elseif($time < $middayStart) {
        $name = '上午';
    } elseif($time < $middayEnd) {
        $name = '中午';
    } elseif($time < $nightStart) {
        $name = '下午';
    } else {
        $name = '晚上';
    }

    return $name;
}

/**
+----------------------------------------------------------
 * 获取某条请假记录在某天的请假时间
+----------------------------------------------------------
 * @param  date    $date 	指定的日期
+----------------------------------------------------------
 * @param  datetime    $leaveStart 	请假记录的请假开始时间
+----------------------------------------------------------
 * @param  datetime    $leaveEnd 	请假记录的请假结束时间
+----------------------------------------------------------
 * @param  array    $work_time_setting 	上班时间设置数据
+----------------------------------------------------------
 * @return intval
+----------------------------------------------------------
 */
function getLeaveTime($date, $leaveStart, $leaveEnd, $work_time_setting) {
    $leaveAllTime = 0;

    $dateObj = \DateTime::createFromFormat('Y-m-d H:i', $date.' '.$work_time_setting[0]);
    $work_start_time = $dateObj->getTimestamp();	//上班时间
    $dateObj = \DateTime::createFromFormat('Y-m-d H:i', $date.' '.$work_time_setting[1]);
    $work_end_time = $dateObj->getTimestamp();	//下班时间
    $dateObj = \DateTime::createFromFormat('Y-m-d H:i', $date.' '.$work_time_setting[2]);
    $rest_start_time = $dateObj->getTimestamp();	//休息开始时间
    $dateObj = \DateTime::createFromFormat('Y-m-d H:i', $date.' '.$work_time_setting[3]);
    $rest_end_time = $dateObj->getTimestamp();	//休息结束时间

    $leaveStartTime = strtotime($leaveStart);
    $leaveEndTime = strtotime($leaveEnd);

    if($leaveStartTime <= $work_end_time && $leaveEndTime >= $work_start_time) {
        $leaveStartTime = $leaveStartTime < $work_start_time ? $work_start_time : $leaveStartTime;
        $leaveEndTime = $leaveEndTime > $work_end_time ? $work_end_time : $leaveEndTime;

        $leaveStartTime = $leaveStartTime >= $rest_start_time && $leaveStartTime < $rest_end_time ? $rest_start_time : $leaveStartTime;
        $leaveEndTime = $leaveEndTime > $rest_start_time && $leaveEndTime <= $rest_end_time ? $rest_end_time : $leaveEndTime;

        $leaveAllTime = $leaveEndTime - $leaveStartTime;
        if($leaveStartTime <= $rest_start_time && $leaveEndTime >= $rest_end_time) {
            $leaveAllTime = $leaveAllTime - ($rest_end_time - $rest_start_time);
        }
    }

    return $leaveAllTime;
}


/**
+----------------------------------------------------------
 * 截取字符串，保留指定长度的尾部
+----------------------------------------------------------
 * @param: string $str       要截取的字符串
+----------------------------------------------------------
 * @param: intval $length     要保留尾部的长度
+----------------------------------------------------------
 * @param: string $append     超出长度截取附加的字符串，默认省略号
+----------------------------------------------------------
 * @return string
 */
function sub_first_str($str, $length = 0, $append = '...') {
    $str = trim($str);
    $strlength = mb_strlen($str, C('DB_CHARSET'));

    if ($length == 0 || $length >= $strlength) {
        return $str;
    }

    $start = $strlength - $length;
    $newstr = mb_substr($str, $start, $length, C('DB_CHARSET'));

    if($str != $newstr) {
        $newstr = $append.$newstr;
    }

    return $newstr;
}

/**
+----------------------------------------------------------
 * 根据传入的签到时间和签出时间获取考勤信息
+----------------------------------------------------------
 * @param: date $date       考勤日期
+----------------------------------------------------------
 * @param: array $user       考勤人员信息（array('user_id' => '用户id', 'dept' => '所属部门id', 'work_attendance' => '考勤类型')）
+----------------------------------------------------------
 * @param: timestamp $sign_in_time     签到时间(unix时间戳)
+----------------------------------------------------------
 * @param: timestamp $check_out_time     签出时间(unix时间戳)
+----------------------------------------------------------
 * @return array 返回考勤信息(除了签到签出记录生成时间和ip)
 */
function getAddendanceInfo($date, $user, $sign_in_time, $check_out_time = '', $thisDayAttendanceInfo = array(), $last_attendance = array(), $nextDayAttendanceInfo = array(), $executeSql = true) {
    if(!$sign_in_time) {
        return false;
    }

    $dateObj = new \DateTime($date);
    $signDate = $dateObj->format('Y-m-d');
    $thisYear = $dateObj->format('Y');
    $thisMon = $dateObj->format('n');
    $thisDay = $dateObj->format('j');

    $scheduling = getYearScheduling($thisYear);
    if(!$scheduling || empty($scheduling[$user['work_attendance']][$thisMon][$thisDay])) {
        return false;
    }
    $todayScheduling = $scheduling[$user['work_attendance']][$thisMon][$thisDay];

    $work_time_setting = getWorkTimeSettingByUid($user['user_id'], $user['dept']);
    if(!$work_time_setting) {
        return false;
    }

    $thisWeek = $dateObj->format('N');
    if($thisWeek == 6) {	//检测是否调休
        $changeRestCond = array('status' => '1', 'uid' => $user['user_id']);
        $changeRestCond['_complex'] = array('work_date' => $signDate, 'rest_date' => $signDate, '_logic' => 'OR');
        $changeRestInfo = M('change_rest')->where($changeRestCond)->field('work_date, rest_date')->find();

        if(!empty($changeRestInfo)) {
            if($changeRestInfo['work_date'] == $signDate) {
                $todayScheduling['is_working'] = '1';
            } elseif($changeRestInfo['rest_date'] == $signDate) {
                $todayScheduling['is_working'] = '0';
            }
        }
    }

    $workStartObj = new \DateTime($signDate.' '.$work_time_setting[0]);
    $work_start_time = $workStartObj->getTimestamp();	//上班时间

    $sign_in_type = 1;	//签到类型
    $sign_in_result = 1;	//正常
    $later_time = 0;	//迟到时间
    $replace_later_time = 0;

    if($check_out_time) {
        $check_out_type = 1;	//日常签出
        $check_out_result = 1;	//正常
    } else {
        $check_out_type = 0;	//日常签出
        $check_out_result = 0;	//正常
    }
    $early_time = 0;	//早退时间
    $overtime = 0;	//加班时间
    $points = 0;	//积分
    $replace_points = 0;	//迟到抵消积分

    //休息日
    if($todayScheduling['is_working'] == '0') {
        if($todayScheduling['special'] > 0) {
            $sign_in_type = 3;	//假日签到
        } else {
            $sign_in_type = 2;	//周末签到
        }
        $sign_in_result = 2;	//加班

        if($check_out_time) {
            $check_out_result = 2;	//加班
            $check_out_type = $sign_in_type;

            if($sign_in_time <= $work_start_time) {	//周末从平时上班时间开始算加班时间
                $overtime_start_time = $work_start_time;
            } else {
                $overtime_start_time = $sign_in_time;
            }

            $overtime = $check_out_time > $overtime_start_time ? round(($check_out_time - $overtime_start_time)/3600, 2) : 0;
            $points = floor($overtime);
            $points = $points > $work_time_setting[7] ? $work_time_setting[7] : $points;	//不能超过周末最大积分
        }
    } else {
        $workStartObj->add(new \DateInterval('PT'.$work_time_setting[4].'M'));
        $work_start_flextime = $workStartObj->getTimestamp();	//上班弹性开始时间
        $dateObj = new \DateTime($signDate.' '.$work_time_setting[1]);
        $work_end_time = $dateObj->getTimestamp();	//下班时间
        $dateObj = new \DateTime($signDate.' '.$work_time_setting[2]);
        $rest_start_time = $dateObj->getTimestamp();	//午休开始时间
        $dateObj = new \DateTime($signDate.' '.$work_time_setting[3]);
        $rest_end_time = $dateObj->getTimestamp();	//午休结束时间
        $dateObj = new \DateTime($signDate.' '.$work_time_setting[5]);
        $overtime_start_time = $dateObj->getTimestamp();	//加班开始时间

        //检测是否在签到时间内存在请假 code start
        $leave_all_time_for_in = 0;
        if($sign_in_time < $rest_end_time && $sign_in_time >= $rest_start_time) {
            $check_in_time = $rest_start_time;
        } elseif($sign_in_time < $work_start_time) {
            $check_in_time = $work_start_time;
        } else {
            $check_in_time = $sign_in_time;
        }

        $askLeaveModel = M('ask_leave');
        $askLeaveCond = array('uid' => $user['user_id'], 'status' => 1, 'start_time' => array('elt', date('Y-m-d H:i:s', $check_in_time)), 'end_time' => array('gt', date('Y-m-d H:i:s', $work_start_time)));
        $ask_leave = $askLeaveModel->where($askLeaveCond)->field('start_time, end_time')->select();

        if(!empty($ask_leave)) {
            foreach($ask_leave as $val) {
                $ask_leave_start_time = strtotime($val['start_time']);
                $ask_leave_end_time = strtotime($val['end_time']);
                if($ask_leave_start_time < $rest_end_time && $ask_leave_start_time >= $rest_start_time) {
                    $ask_leave_start_time = $rest_start_time;
                }
                if($ask_leave_end_time < $rest_end_time && $ask_leave_end_time >= $rest_start_time) {
                    $ask_leave_end_time = $rest_end_time;
                }
                if($ask_leave_start_time <= $check_in_time && $ask_leave_end_time > $check_in_time) {
                    $sign_in_type = 4;	//请假签到
                }

                if($ask_leave_start_time < $work_start_time) {
                    $ask_leave_start_time = $work_start_time;
                }
                if($ask_leave_end_time > $check_in_time) {
                    $ask_leave_end_time = $check_in_time;
                }

                $leave_time = $ask_leave_end_time - $ask_leave_start_time;
                if($ask_leave_start_time <= $rest_start_time && $ask_leave_end_time >= $rest_end_time) {
                    $leave_time = $leave_time - ($rest_end_time - $rest_start_time);
                }
                $leave_all_time_for_in += $leave_time;
            }
        }

        if($sign_in_time > $work_start_flextime) {
            $laterTime = 0;
            if($sign_in_time < $rest_start_time) {
                $laterTime = $sign_in_time - $work_start_time;
            } elseif($sign_in_time >= $rest_start_time && $sign_in_time < $rest_end_time) {
                $laterTime = $rest_start_time - $work_start_time;
            } elseif($sign_in_time >= $work_end_time) {
                $laterTime = $work_end_time - $work_start_time - ($rest_end_time - $rest_start_time);
            } else {
                $laterTime = $sign_in_time - $work_start_time - ($rest_end_time - $rest_start_time);
            }

            if($leave_all_time_for_in > 0 && $laterTime <= $leave_all_time_for_in) {
                $sign_in_result = 3;	//请假
            } else {
                $sign_in_result = 4;	//迟到
                $later_time = ceil(($laterTime - $leave_all_time_for_in)/60);
            }
        } elseif($sign_in_type == 4) {
            $sign_in_result = 3;	//请假
        }
        //检测是否在签到时间内存在请假 code end

        if($check_out_time) {
            //检测是否在签出时间内存在请假 code start
            $leave_all_time_for_out = 0;

            if($check_out_time < $rest_end_time && $check_out_time >= $rest_start_time) {
                $check_time_for_out = $rest_end_time;
            } elseif($check_out_time > $work_end_time) {
                $check_time_for_out = $work_end_time;
            } else {
                $check_time_for_out = $check_out_time;
            }

            $askLeaveCond = array('uid' => $user['user_id'], 'status' => 1, 'start_time' => array('lt', date('Y-m-d H:i:s', $work_end_time)), 'end_time' => array('egt', date('Y-m-d H:i:s', $check_time_for_out)));
            $ask_leave_for_out = $askLeaveModel->where($askLeaveCond)->field('start_time, end_time')->select();

            if(!empty($ask_leave_for_out)) {
                foreach($ask_leave_for_out as $val) {
                    $ask_leave_start_time = strtotime($val['start_time']);
                    $ask_leave_end_time = strtotime($val['end_time']);
                    if($ask_leave_start_time < $rest_end_time && $ask_leave_start_time >= $rest_start_time) {
                        $ask_leave_start_time = $rest_start_time;
                    }
                    if($ask_leave_end_time < $rest_end_time && $ask_leave_end_time >= $rest_start_time) {
                        $ask_leave_end_time = $rest_end_time;
                    }
                    if($ask_leave_start_time <= $check_time_for_out && $ask_leave_end_time >= $check_time_for_out) {
                        $check_out_type = 4;	//请假签出
                    }

                    if($ask_leave_end_time > $work_end_time) {
                        $ask_leave_end_time = $work_end_time;
                    }
                    if($ask_leave_start_time < $check_time_for_out) {
                        $ask_leave_start_time = $check_time_for_out;
                    }

                    $leave_time = $ask_leave_end_time - $ask_leave_start_time;
                    if($ask_leave_start_time <= $rest_start_time && $ask_leave_end_time >= $rest_end_time) {
                        $leave_time = $leave_time - ($rest_end_time - $rest_start_time);
                    }
                    $leave_all_time_for_out += $leave_time;
                }
            }

            //提早签出
            if($check_out_time < $work_end_time) {
                $earlyTime = 0;
                if($check_out_time > $rest_end_time) {
                    $earlyTime = $work_end_time - $check_out_time;
                } elseif($check_out_time >= $rest_start_time) {
                    $earlyTime = $work_end_time - $rest_end_time;
                } elseif($check_out_time <= $work_start_time) {
                    $earlyTime = $work_end_time - $work_start_time - ($rest_end_time - $rest_start_time);
                } else {
                    $earlyTime = $work_end_time - $check_out_time - ($rest_end_time - $rest_start_time);
                }

                if($leave_all_time_for_out > 0 && $earlyTime <= $leave_all_time_for_out) {
                    $check_out_result = 3;	//请假
                } else {
                    $check_out_result = 4;	//早退
                    $early_time = ceil(($earlyTime - $leave_all_time_for_out)/60);
                }
            } elseif($check_out_type == 4) {
                $check_out_result = 3;	//请假
            }
            if($check_out_time >= $work_end_time) {	//计算加班时间和积分
                $oldOvertime = $check_out_time > $overtime_start_time ? floor(($check_out_time - $overtime_start_time)/36)/100 : 0;
                $oldPoints = floor($oldOvertime);
                $oldPoints = $oldPoints > $work_time_setting[6] ? $work_time_setting[6] : $oldPoints;	//不能超过平时加班最大积分

                if($later_time > 0 && $later_time <= 30) {	//迟到用加班时间抵消
                    $needReplaceOvertime = ceil($later_time/10)*3600;
                    $overtimeFromEndWork = $check_out_time - $work_end_time;
                    if($overtimeFromEndWork >= $needReplaceOvertime) {	//加班时间超过需要抵消迟到的时间
                        $replace_later_time = $later_time;
                        $later_time = 0;
                        $sign_in_result = 1;
                        $replace_type = 2;

                        $replaceOverStartTime = $work_end_time + 3600;
                        $overtime_start_time = MAX($overtime_start_time, $replaceOverStartTime);
                    }
                }

                $overtime = $check_out_time > $overtime_start_time ? floor(($check_out_time - $overtime_start_time)/36)/100 : 0;

                $points = floor($overtime);
                $points = $points > $work_time_setting[6] ? $work_time_setting[6] : $points;	//不能超过平时加班最大积分
                $replace_points = $oldPoints - $points;

                if($overtime > 0) {
                    $check_out_result = 2;	//加班
                }
            }
            //检测是否在签出时间内存在请假 code end
        }

        $attendanceModel = M('attendance');
        if(empty($thisDayAttendanceInfo)) {
            $thisDayAttendanceInfo = $attendanceModel->where(array('uid' => ':uid', 'dates' => ':dates'))->bind(array(':uid' => array($user['user_id'], \PDO::PARAM_INT), ':dates' => $signDate))->field('id, replace_type, replace_points')->find();
        }
        //获取当日考勤数据
        if ($later_time > 0 && $later_time <= 30) {	//当日迟到30分钟以内
            if(empty($last_attendance)) {
                $last_attendance = $attendanceModel->where(array('uid' => ':uid', 'dates' => ':dates'))->bind(array(':uid' => array($user['user_id'], \PDO::PARAM_INT), ':dates' => date('Y-m-d', strtotime('-1 day', strtotime($signDate)))))->field('id, overtime, points')->find();
            }

            //加班超过4小时
            if (!empty($last_attendance) && $last_attendance['overtime'] >= 4) {
                $replace_later_time = $later_time;
                $later_time = 0;
                $sign_in_result = $leave_all_time_for_in > 0 ? 3 : 1;
                $replace_type = 1;
                $replace_points = $last_attendance['points'] > 0 ? 1 : 0;

                if($executeSql && $thisDayAttendanceInfo['replace_type'] != '1' && $replace_points > 0) {
                    $res = $attendanceModel->where(array('id' => ':id'))->bind(':id', $last_attendance['id'], \PDO::PARAM_INT)->setDec('points', $replace_points);
                    if($res === false) {
                        return false;
                    }
                }
            }
        }

        if($executeSql && $replace_type != '1' && !empty($thisDayAttendanceInfo) && $thisDayAttendanceInfo['replace_type'] == '1' && $thisDayAttendanceInfo['replace_points'] > 0) {
            $res = $attendanceModel->where(array('uid' => ':uid', 'dates' => ':dates'))->bind(array(':uid' => array($user['user_id'], \PDO::PARAM_INT), ':dates' => date('Y-m-d', strtotime('-1 day', strtotime($signDate)))))->setInc('points', $thisDayAttendanceInfo['replace_points']);
            if($res === false) {
                return false;
            }
        }
    }

    if(!empty($nextDayAttendanceInfo) && $nextDayAttendanceInfo['replace_type'] == '1' && $nextDayAttendanceInfo['replace_points'] > 0) {
        $points -= $nextDayAttendanceInfo['replace_points'];
        $points <= 0 && $points = 0;
    }

    return array(
        'sign_in_type'			=>	$sign_in_type,
        'sign_in_time'			=>	date('Y-m-d H:i:s', $sign_in_time),
        'sign_in_result'		=>	$sign_in_result,
        'late_time'				=>	$later_time,
        'replace_later_time'	=>	$replace_later_time,
        'check_out_type'		=> 	$check_out_type,
        'check_out_time'		=>	$check_out_time ? date('Y-m-d H:i:s', $check_out_time) : '0000-00-00 00:00:00',
        'check_out_result'		=>	$check_out_result,
        'early_time'			=>	$early_time,
        'overtime'				=>	$overtime,
        'points'				=>	$points,
        'replace_type'			=>	isset($replace_type) ? $replace_type : 0,
        'replace_points'		=>	$replace_points
    );
}

/**
+----------------------------------------------------------
 * 获取某个人，每天正常工作时间
+----------------------------------------------------------
 * @param  int			$uid 	用户id
+----------------------------------------------------------
 * @return string		秒数
+----------------------------------------------------------
 */
function getWorkDayTime($uid, $dept = 0) {
    $work_time_setting = getWorkTimeSettingByUid($uid, $dept);

    $dateObj = \DateTime::createFromFormat('H:i', $work_time_setting[0]);
    $work_start_time = $dateObj->getTimestamp();	//上班时间
    $dateObj = \DateTime::createFromFormat('H:i', $work_time_setting[1]);
    $work_end_time = $dateObj->getTimestamp();	//下班时间
    $dateObj = \DateTime::createFromFormat('H:i', $work_time_setting[2]);
    $rest_start_time = $dateObj->getTimestamp();	//休息开始时间
    $dateObj = \DateTime::createFromFormat('H:i', $work_time_setting[3]);
    $rest_end_time = $dateObj->getTimestamp();	//休息结束时间

    $day_work_time = $work_end_time - $work_start_time - ($rest_end_time - $rest_start_time);

    return $day_work_time;

}

/**
+----------------------------------------------------------
 * 根据某个人的请假开始和结束时间，获取总请假时间
+----------------------------------------------------------
 * @param: array		$user       考勤人员信息（array('user_id' => '用户id', 'dept' => '所属部门id', 'work_attendance' => '考勤类型')）
+----------------------------------------------------------
 * @param  datetime    $startTime 	请假开始时间
+----------------------------------------------------------
 * @param  datetime    $endTime 	请假结束时间
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function getLeaveTotalTime($user, $startTime, $endTime) {

    $work_time_setting = getWorkTimeSettingByUid($user['user_id'], $user['dept']);
    if(empty($work_time_setting)) {
        return false;
    }

    $startObj = new \DateTime($startTime);
    $startDate = $startObj->format('Y-m-d');
    $startYear = $startObj->format('Y');

    $endObj = new \DateTime($endTime);
    $endDate = $endObj->format('Y-m-d');

    $scheduling = getYearScheduling($startYear);

    //获取这个时间段的排班数据
    $leaveScheduling = array();
    $iDate = $startDate;
    do{
        $dateObj = new \DateTime($iDate);
        $thisYear = $dateObj->format('Y');
        $thisMon = $dateObj->format('n');
        $thisDay = $dateObj->format('j');
        if($thisYear != $startYear){
            $scheduling = getYearScheduling($thisYear);
        }
        if(!$scheduling || !$scheduling[$user['work_attendance']][$thisMon][$thisDay]){
            return false;
        }
        $leaveScheduling[$iDate] = $scheduling[$user['work_attendance']][$thisMon][$thisDay];
        $dateObj = $dateObj->add(new \DateInterval('P1D'));
        $iDate = $dateObj->format('Y-m-d');
    }while(strtotime($iDate) <= strtotime($endDate));

    $totalLeaveTime = 0;
    foreach($leaveScheduling as $date => $val){
        if($val['week'] == 6){		//是否调休
            $changeRestCond = array('status' => '1', 'uid' => $user['user_id']);
            $changeRestCond['_complex'] = array('work_date' => $date, 'rest_date' => $date, '_logic' => 'OR');
            $changeRestInfo = M('change_rest')->where($changeRestCond)->field('work_date, rest_date')->find();

            if(!empty($changeRestInfo)) {
                if($changeRestInfo['work_date'] == $date) {
                    $val['is_working'] = '1';
                } elseif($changeRestInfo['rest_date'] == $date) {
                    $val['is_working'] = '0';
                }
            }
        }

        if($val['is_working'] == '1'){
            $totalLeaveTime = $totalLeaveTime + getLeaveTime($date, $startTime, $endTime, $work_time_setting);
        }
    }
    return $totalLeaveTime;
}

//根据当前excel列字母获取下一列字母
function getNextExcelColum($charset) {
    $zCode = ord('Z');

    if(strlen($charset) <= 1 && ord($charset) < $zCode) {
        $charsetCode = ord($charset);
        $charsetCode++;
        return chr($charsetCode);
    }

    if($charset == 'Z') {
        return 'AA';
    }

    $firstCharset = substr($charset, 0, 1);
    $secondCharset = substr($charset, 1, 1);
    $secondCharsetCode = ord($secondCharset);

    if($secondCharsetCode >= $zCode) {
        $firstCharsetCode = ord($firstCharset);
        $firstCharsetCode++;
        return chr($firstCharsetCode).'A';
    }

    $secondCharsetCode++;
    return $firstCharset.chr($secondCharsetCode);
}

/**
+----------------------------------------------------------
 * 获取某个日期之前的若干个工作日
+----------------------------------------------------------
 * @param: array		$user       考勤人员信息（array('user_id' => '用户id', 'dept' => '所属部门id', 'work_attendance' => '考勤类型')）
+----------------------------------------------------------
 * @param: intval		$offset     日期偏移量，表示几个工作日之前，默认为1
+----------------------------------------------------------
 * @param  date		    $fixedDate 		指定日期，默认当前日期
+----------------------------------------------------------
 * @return array
+----------------------------------------------------------
 */
function getSomeWorkDaysAgo($user, $offset = 1, $fixedDate = '') {

    if(!$fixedDate){
        $fixedDate = date('Y-m-d');
    }

    $work_time_setting = getWorkTimeSettingByUid($user['user_id'], $user['dept']);
    if(empty($work_time_setting)) {
        return false;
    }

    $dateObj = new \DateTime($work_time_setting[0]);
    $work_start_time = $dateObj->getTimestamp();	//上班时间
    $dateObj = new \DateTime($work_time_setting[1]);
    $work_end_time = $dateObj->getTimestamp();	//下班时间
    $dateObj = new \DateTime($work_time_setting[2]);
    $rest_start_time = $dateObj->getTimestamp();	//午休开始时间
    $dateObj = new \DateTime($work_time_setting[3]);
    $rest_end_time = $dateObj->getTimestamp();	//午休结束时间
    $dayWorkingTimes = $work_end_time - $work_start_time - ($rest_end_time - $rest_start_time);

    $dates = array();
    $nature_day = $working_day = 1;

    while($working_day <= $offset){
        $timestamp = strtotime($fixedDate) - $nature_day * 24 * 60 * 60;
        $currDate = date('Y-m-d', $timestamp);

        $dateObj = new \DateTime($currDate);
        $thisYear = $dateObj->format('Y');
        $thisMon = $dateObj->format('n');
        $thisDay = $dateObj->format('j');
        $scheduling = getYearScheduling($thisYear);
        if(!$scheduling || !$scheduling[$user['work_attendance']][$thisMon][$thisDay]){
            return false;
        }

        $curScheduling = $scheduling[$user['work_attendance']][$thisMon][$thisDay];

        //是否调休
        if($curScheduling['week'] == '6'){
            $changeRestCond = array('status' => 1, 'uid' => $user['user_id']);
            $changeRestCond['_complex'] = array('work_date' => $currDate, 'rest_date' => $currDate, '_logic' => 'OR');
            $changeRestInfo = M('change_rest')->where($changeRestCond)->field('work_date, rest_date')->find();

            if(!empty($changeRestInfo)) {
                if($changeRestInfo['work_date'] == $currDate) {
                    $curScheduling['is_working'] = '1';
                } elseif($changeRestInfo['rest_date'] == $currDate) {
                    $curScheduling['is_working'] = '0';
                }
            }
        }

        //是否请假
        if($curScheduling['is_working'] == '1'){
            $askLeaveList = M('ask_leave')->where(array('start_time' => array('lt', ':work_time_1'), array('end_time' => array('gt', ':work_time_0')), 'uid' => ':uid', 'status' => 1))->bind(array(':work_time_1' => $currDate.' '.$work_time_setting[1].':00', ':work_time_0' => $currDate.' '.$work_time_setting[0].':00', ':uid' => array($user['user_id'], \PDO::PARAM_INT)))->field('start_time, end_time')->select();

            if(!empty($askLeaveList)) {
                $leaveTime = 0;
                foreach($askLeaveList as $v) {
                    $leaveTime = ($leaveTime > 0) ? $leaveTime + getLeaveTime($currDate, $v['start_time'], $v['end_time'], $work_time_setting) : getLeaveTime($currDate, $v['start_time'], $v['end_time'], $work_time_setting);
                }

                if($leaveTime >= $dayWorkingTimes) {
                    $curScheduling['is_working'] = '0';
                }
            }
        }

        $nature_day++;
        if($curScheduling['is_working'] == '1'){
            $dates[] = $currDate;
            $working_day++;
        }
    }

    return $dates;
}
/**
 * 根据id或的试卷类型名称
 *
 **/
function getExamTypeNameById($type) {
    return M('exam_question_type')->where(array('id' => $type))->getField('name');
}

/**
 * 根据id获得试卷类型名称路径
 *
 **/
function getExamTypePathById($type) {
    $typePathInfo = array();
    $model = M('exam_question_type');
    $typeInfo = $model->where(array('id' => $type))->field('path')->find();
    $typePath = $model->where(array('id' => array('in', $typeInfo['path'])))->getField('id, name');

    $typePathInfo['type_path_name'] = '';
    foreach(explode(',', $typeInfo['path']) as $val) {
        !empty($typePath[$val]) && $typePathInfo['type_path_name'] .= $typePath[$val].'/';
    }
    return $typePathInfo['type_path_name'] = '/'.rtrim($typePathInfo['type_path_name'], '/');
}

/**
 * 根据id获得问卷类型名称路径
 *
 **/
function getQuesTypePathById($type) {
    $typePathInfo = array();
    $model = M('questionnaire_question_type');
    $typeInfo = $model->where(array('id' => $type))->field('path')->find();
    $typePath = $model->where(array('id' => array('in', $typeInfo['path'])))->getField('id, name');

    $typePathInfo['type_path_name'] = '';
    foreach(explode(',', $typeInfo['path']) as $val) {
        !empty($typePath[$val]) && $typePathInfo['type_path_name'] .= $typePath[$val].'/';
    }
    return $typePathInfo['type_path_name'] = '/'.rtrim($typePathInfo['type_path_name'], '/');
}

/**
+----------------------------------------------------------
 * 重新生成题库缓存数据
 * @param: intval	$tid	要生成的题库分类(0则为临时题库)
+----------------------------------------------------------
 * @param: intval	$uid	临时题库的所有人(正式题库为0)
+----------------------------------------------------------
 * @return array
+----------------------------------------------------------
 */
function createExamQuestionCache($tid = 0, $uid = 0) {
    if($tid > 0) {
        $questionList = M('exam_question_to_types')->alias('t')
            ->join('LEFT JOIN __EXAM_OFFICIAL_QUESTION_LIBRARY__ q ON q.id=t.qid')
            ->where(array('t.tid' => ':tid', 'q.status' => ':status'))
            ->bind(array(':tid' => array($tid, \PDO::PARAM_INT), ':status' => array('1', \PDO::PARAM_INT)))
            ->order('t.create_time ASC')
            ->field('q.id, q.question_model, q.questions, q.select_options, q.correct_answers, q.answer_analysis, q.level')
            ->select();
        $questionCacheList = array();
        if(!empty($questionList)) {
            foreach($questionList as $q) {
                $questionCacheList[$q['question_model']][$q['id']] = array(
                    'id' => $q['id'],
                    'questions' => $q['questions'],
                    'select_options' => $q['select_options'],
                    'correct_answers' => $q['correct_answers'],
                    'answer_analysis' => $q['answer_analysis'],
                    'level' => $q['level']
                );
            }
        }

        return S(C('EXAM_OFFICIAL_QU_LIB_CACHE_PREFIX').$tid, $questionCacheList);
    } else {
        $questionList = M('write_exam_question')->where(array('question_library' => ':question_library', 'create_by' => ':create_by'))->bind(array(':question_library' => array(2, \PDO::PARAM_INT), ':create_by' => array($uid, \PDO::PARAM_INT)))->order('create_time ASC')->field('id, question_model, questions, select_options, correct_answers, answer_analysis, level')->select();

        $tempQuestionCacheList = array();
        if(!empty($questionList)) {
            foreach($questionList as $val) {
                $tempQuestionCacheList[$val['question_model']][$val['id']] = array(
                    'id' => $val['id'],
                    'questions' => $val['questions'],
                    'select_options' => $val['select_options'],
                    'correct_answers' => $val['correct_answers'],
                    'answer_analysis' => $val['answer_analysis'],
                    'level' => $val['level']
                );
            }
        }
        return S(C('EXAM_TEMP_QU_LIB_CACHE_PREFIX').$uid, $tempQuestionCacheList);
    }
}

/**
+----------------------------------------------------------
 * 重新生成问卷调查题库缓存数据
+----------------------------------------------------------
 * @param: intval	$tid	要生成的题库分类
+----------------------------------------------------------
 * @return array
+----------------------------------------------------------
 */
function createQuestionnairelQuestionCache($tid) {
    if($tid <= 0) {
        return false;
    }
    $questionList = M('questionnaire_question_to_types')->alias('t')
        ->join('LEFT JOIN __QUESTIONNAIREL_QUESTION_LIBRARY__ q ON q.id=t.qid')
        ->where(array('t.tid' => ':tid', 'q.status' => ':status'))
        ->bind(array(':tid' => array($tid, \PDO::PARAM_INT), ':status' => array('1', \PDO::PARAM_INT)))
        ->order('t.create_time ASC')
        ->field('q.id, q.question_model, q.questions, q.select_options, q.question_tips, q.option_num')
        ->select();
    $questionCacheList = array();
    if(!empty($questionList)) {
        foreach($questionList as $q) {
            $questionCacheList[$q['question_model']][$q['id']] = array(
                'id' => $q['id'],
                'questions' => $q['questions'],
                'select_options' => $q['select_options'],
                'question_tips' => $q['question_tips'],
                'option_num' => $q['option_num']
            );
        }
    }

    return S(C('QUESTIONNAIREL_OFFICIAL_LIB_CACHE_PREFIX').$tid, $questionCacheList);
}

/**
+----------------------------------------------------------
 * 获取数组中重复的数据
+----------------------------------------------------------
 * @param: array	$arr
+----------------------------------------------------------
 * @return array
+----------------------------------------------------------
 */
function array_repeat_values($arr) {
    $res = array_filter($arr, function($i) {
        global $arr;
        $res = array_keys($arr, $i);
        if(count($res) <= 1) {
            return false;
        }
        return true;
    });
    return $res;
}

/**
+----------------------------------------------------------
 * 获取当前签出日期
+----------------------------------------------------------
 * @return date
+----------------------------------------------------------
 */
function get_attendance_checkout_date($uid) {
    if(!$uid) {
        return false;
    }

    $checkoutDate = date('Y-m-d');
    $checkExist = M('attendance')->where(array('uid' => $uid, 'dates' => $checkoutDate))->count('id');
    if(empty($checkExist)) {
        $dateObj = new \DateTime('07:00');
        $checkOutYesLimit = $dateObj->getTimestamp();	//签出昨天考勤的限制时间
        if(time() < $checkOutYesLimit) {
            $checkoutDate = date('Y-m-d', strtotime('yesterday'));
        }
    }

    return $checkoutDate;
}

//将数据按照某值进行排序，并加入排序序号。不足以空数组补足到指定数量
function get_order_level_data($data, $num = 10, $numIndex = 'num') {
    $existNum = 0;
    $level = 0;
    if(!empty($data)) {
        $level++;
        $lastLevelValue = 0;
        foreach($data as $key=>&$val) {
            if($lastLevelValue > $val[$numIndex]) {
                $level = $key+1;
            }
            $lastLevelValue = $val[$numIndex];
            $val['level'] = $level >= 10 ? $level : '0'.$level;
        }
        $existNum = count($data);
    }
    if($num > $existNum) {
        for(; $existNum < $num; $existNum++) {
            $data[] = array();
        }
    }

    return $data;
}

//将数据按照某值进行降序排序，并加入排序序号。不足以空数组补足到指定数量
function get_order_level_data_asc($data, $num = 10, $numIndex = 'num') {
    $existNum = 0;
    $level = 0;
    $a = 0;
    if(!empty($data)) {
        $level++;
        $lastLevelValue = $data[0][$numIndex];
        foreach($data as $key=>&$val) {
            if($lastLevelValue < $val[$numIndex]) {
                $level = $key+1;
            }
            $lastLevelValue = $val[$numIndex];
            $val['level'] = $level >= 10 ? $level : '0'.$level;
        }
        $existNum = count($data);
    }
    if($num > $existNum) {
        for(; $existNum < $num; $existNum++) {
            $data[] = array();
        }
    }

    return $data;
}

/**
+----------------------------------------------------------
 * 对二维数组按照指定值排序
+----------------------------------------------------------
 * @param  integer    $id
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function multiSortArr($arrays, $sort_key, $sort_order = SORT_ASC, $sort_type = SORT_NUMERIC){
    $key_arrays = array();
    if(is_array($arrays)){
        foreach ($arrays as $array){
            if(is_array($array)){
                $key_arrays[] = $array[$sort_key];
            }else{
                return false;
            }
        }
    }else{
        return false;
    }
    array_multisort($key_arrays, $sort_order, $sort_type, $arrays);
    return $arrays;
}

/**
+----------------------------------------------------------
 * 根据部门id获取部门路径
+----------------------------------------------------------
 * @param  integer    $id
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function getPathById($id) {
    if(!$id) {
        return false;
    }
    $pathInfo = M('dept')->where(array('id' => $id))->getField('path');
    $allDeptName = M('dept')->where(array('id' => array('in', $pathInfo)))->getField('id, name', true);
    $paths = explode(',', $pathInfo);
    $dname = '';
    foreach($paths as $did){
        $dname .= $allDeptName[$did].'/';
    }
    $deptfullname = rtrim($dname, '/');
    return  $deptfullname;
}

function dateFormat($timestamp, $fmt = 'Y-m-d H:i:s') {
    return date($fmt, $timestamp);
}

function getBaseDecodeStatus($status) {
    $statusList = base64_decode($status);

    $str = '';
    if(mb_strlen($statusList) > 0) {
        $statusArr = explode(',', $statusList);
        foreach($statusArr as $val) {
            $str && $str .= ',';
            $str .= fetch_lang('ACTION_FIELD.work.status.value.'.$val);
        }
    }

    return $str;
}


/**
 * 导出到excel
 * @param array $data array 数据，根据列名顺序
 * @param array $fields array 列名数组
 * @param $excel_name string 文件名，不需要后缀
 * @param array $param  array 参数
 *        save: 保存在磁盘（true）或下载（false）, append: 是否追加数据, row: 行标记,
 *        title: 文件标题, subject: 文件分类, description: 文件描述, file_path: 保存磁盘路径,
 *        autoWidth: 是否自适应列宽（按列名宽度）, perSheet: 每张sheet的行数
 */
function exportExcel($data = array(), $fields = array(), $excel_name, $param = array('save' => true, 'append' => false, 'row' => 0, 'title' => '', 'subject' => '', 'description' => '', 'file_path' => './Public/Uploads/', 'autoWidth' => false, 'perSheet' => 20000))
{
    import('Home.Util.PHPExcel.PHPExcel', '', '.php');
    import('Home.Util.PHPExcel.PHPExcel.Writer.Excel5', '', '.php');
    import('Home.Util.PHPExcel.PHPExcel.IOFactory.php');

    !isset($param['save']) && $param['save'] = true;
    !isset($param['append']) && $param['append'] = false;
    !isset($param['row']) && $param['row'] = 0;
    !isset($param['title']) && $param['title'] = $excel_name;
    !isset($param['subject']) && $param['subject'] = $param['title'];
    !isset($param['description']) && $param['description'] = $param['title'];
    !isset($param['file_path']) && $param['file_path'] = './Public/Uploads/';
    !isset($param['perSheet']) && $param['perSheet'] = 20000;

    $filename = $excel_name . '.xls';

    //如果是以追加数据形式写入文件，且是首次写入，则需清除同文件名文件
    if($param['row'] == 0 && $param['save'] && file_exists($param['file_path'] . $filename))
        unlink($param['file_path'] . $filename);

    //缓存方式
    $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
    $cacheSettings = array();
    \PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

    //文件属性
    if ($param['append'] && file_exists($param['file_path'] . $filename)) {
        $objReader = \PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel = $objReader->load($param['file_path'] . $filename);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    } else {
        $objPHPExcel = new \PHPExcel();
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objPHPExcel->getProperties()->setCreator($this->userName);
        $objPHPExcel->getProperties()->setLastModifiedBy($this->userName);
        $objPHPExcel->getProperties()->setTitle($param['title']);
        $objPHPExcel->getProperties()->setSubject($param['subject']);
        $objPHPExcel->getProperties()->setDescription($param['description']);
    }

    //写入哪张sheet
    $sheet = floor($param['row']/$param['perSheet']);
    $rowIndex = $param['row'] % $param['perSheet'];
//        file_put_contents('2.txt', $sheet . ":" . $param['row'] .':' . round(memory_get_usage()/1024/1024,2) . "\n", FILE_APPEND);
    if($param['row'] >= $param['perSheet'] && $rowIndex == 0){
        $objPHPExcel->createSheet();
    }
    $objPHPExcel->setActiveSheetIndex($sheet);
    $objPHPExcel->getActiveSheet()->setTitle($param['title']);

    //标题 code start
    $fields = array_values($fields);
    if($rowIndex == 0){
        foreach ($fields as $k => $field){
            $columnIndex = $objPHPExcel->stringFromColumnIndex($k);
            $objPHPExcel->getActiveSheet()->setCellValue($columnIndex . '1', $field);
            if($param['autoWidth'])//自适应列宽
                $objPHPExcel->getActiveSheet()->getColumnDimension($columnIndex)->setWidth(strlen($field)*1.1);
        }
    }
    //标题 code end

//         $objPHPExcel->getActiveSheet()->freezePane("A1");	//冻结姓名列和表头

    //列宽 code start
//            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth($param['width']);
    //列宽 code end

    //默认样式 code start
    $defaulyStyleObj = $objPHPExcel->getDefaultStyle();
    $defaulyStyleObj->getFont()->setName('微软雅黑')->setSize(9);
    $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);
    $defaulyStyleObj->getAlignment()->applyFromArray(
        array(
            'horizontal'=> \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,	//水平居中
            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,		//垂直居中
            'wrap' => TRUE													//换行
        )
    );
    //默认样式 code end

    //表头样式 code start
    $last_columnIndex = $objPHPExcel->stringFromColumnIndex(count($fields) - 1);
    $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(20);
    $style1_2 = $objPHPExcel->getActiveSheet()->getStyle('A1:'.$last_columnIndex.'1');
    $style1_2->getFont()->setBold(true)->setSize(11);
    $style1_2->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A1:'.$last_columnIndex.'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('ffd5e4fb');
    //表头样式 code end

    //填充数据 code start
    $excelRow = $rowIndex + 2;
    foreach($data as $key => $row) {
        $row = array_values($row);
        foreach ($row as $k => $column){
            $columnIndex = $objPHPExcel->stringFromColumnIndex($k);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit($columnIndex. $excelRow, $column, \PHPExcel_Cell_DataType::TYPE_STRING);
        }
        $excelRow++;
    }
    //填充数据 code end

    $filename = iconv('utf-8', 'gb2312', $filename);
    if($param['save']){//如果要保存
        $objWriter->save($param['file_path'] . $filename);
    }else{//不保存，直接下载
        if($param['append'])//以追加形式写入的暂存文件需要删除
            unlink($param['file_path']. $filename);
        //页面输出下载
        header ( "Content-Type: application/force-download" );
        header ( "Content-Type: application/octet-stream" );
        header ( "Content-Type: application/download" );
        header ( "Content-Disposition: attachment; filename=" . $filename );
        $objWriter->save('php://output');
    }
    $objPHPExcel->Destroy();//清除缓存
}

/**
 * 导出到csv
 * @param $data
 * @param $filename
 * @param string $delimiter
 */
function exportCSV($data, $filename, $delimiter = ',')
{
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename="'.$filename.'.csv"');
    foreach ($data as $key => $val){
        foreach ($val as $vk => &$v){
            $v = iconv('utf-8', 'gb2312//IGNORE', $v);
        }
        $output = fopen('php://output','w') or die("Can't open php://output");
        fputcsv($output, $val, $delimiter);
        fclose($output) or die("Can't close php://output");
    }
}

/**
+----------------------------------------------------------
 * 获取下个月的第一天和最后一天
+----------------------------------------------------------
 * @return array()
+----------------------------------------------------------
 */
function getNextMonthStartEnd() {
    $arr = getdate(strtotime(date('Y-m')));
    $nextMonthYear = ($arr['mon'] == 12) ? $arr['year'] + 1 : $arr['year'];
    $nextMonthMon = ($arr['mon'] == 12) ? 1 : ($arr['mon'] + 1);
    $nextMonthMon = $nextMonthMon < 10 ? '0'.$nextMonthMon : $nextMonthMon;
    $nextMonthStart = $nextMonthYear.'-'.$nextMonthMon.'-01';
    $t = date('t', strtotime($nextMonthYear.'-'.$nextMonthMon));
    $nextMonthEnd = $nextMonthYear.'-'.$nextMonthMon.'-'.$t;
    return array('startDay' => $nextMonthStart, 'endDay' => $nextMonthEnd);
}

/**
+----------------------------------------------------------
 * 获取前一个月的第一天和最后一天
+----------------------------------------------------------
 * @return array()
+----------------------------------------------------------
 */
function getPreMonthRegion() {
    $arr = getdate(strtotime(date('Y-m')));

    $startYear = ($arr['mon'] == 1) ? $arr['year'] - 1 : $arr['year'];
    $startMonth = ($arr['mon'] == 1) ? 12 : $arr['mon'] - 1;
    $startMonth = $startMonth < 10 ? '0'.$startMonth : $startMonth;
    $t = date('t', strtotime($startYear.'-'.$startMonth));

    $startDay = $startYear.'-'.$startMonth.'-01';
    $endDay = $startYear.'-'.$startMonth.'-'.$t;
    return array('startDay' => $startDay, 'endDay' => $endDay);
}

/**
+----------------------------------------------------------
 * 获取最近三个月的第一天和最后一天（按照自然月）
+----------------------------------------------------------
 * @return array()
+----------------------------------------------------------
 */
function getQuarterRegion() {
    $arr = getdate(strtotime(date('Y-m')));

    $startYear = ($arr['mon'] < 4) ? $arr['year'] - 1 : $arr['year'];
    $startMonth = ($arr['mon'] < 4) ? $arr['mon'] + 9 : $arr['mon'] - 3;
    $startMonth = $startMonth < 10 ? '0'.$startMonth : $startMonth;
    $startDay = $startYear.'-'.$startMonth.'-01';

    $endYear = ($arr['mon'] == 1) ? $arr['year'] - 1 : $arr['year'];
    $endMonth = ($arr['mon'] == 1) ? 12 : $arr['mon'] - 1;
    $endMonth = $endMonth < 10 ? '0'.$endMonth : $endMonth;
    $t = date('t', strtotime($endYear.'-'.$endMonth));
    $endDay = $endYear.'-'.$endMonth.'-'.$t;
    return array('startDay' => $startDay, 'endDay' => $endDay);
}

/**
+----------------------------------------------------------
 * 获取PRS信息
+----------------------------------------------------------
 * @return boolean
+----------------------------------------------------------
 */
function getPRS($sn) {
    $result = array('error' => 0, 'msg' => '');
    if(!$sn){
        $result = array('error' => 1, 'msg' => 'PRS单号为空');
        return $result;
    }

    $postData = array('sn' => $sn);
    $reponse = curl('http://10.6.2.151:9000/demand', $postData);
    $reponseData = json_decode($reponse, true);

    if(isset($reponseData['code']) && in_array($reponseData['code'], array('2000', '4001'))){

        if($reponseData['code'] == '4001'){
            $result = array('error' => 1, 'msg' => 'PRS订单不存在');
            return $result;
        }

        $data = $reponseData['data'];
        if(!get_magic_quotes_gpc()){
            $data = _addslashes($data);
        }

        if(empty($data)){
            $result = array('error' => 1, 'msg' => '获取到PRS订单具体信息为空');
            return $result;
        }

        //数据组合插入到TASK的PRS订单表
        $prs_id = intval($data['id']);
        $prs_sn = $sn;
        $name = (isset($data['infoName']) && strlen($data['infoName']) > 0) ? trim($data['infoName']) : '';

        $product_type = intval($data['par']);
        if(!in_array($product_type, array(1,2))){
            $result = array('error' => 1, 'msg' => '获取到PRS订单定制产品类型['.$product_type.']不为1(DVR/NVR)或2(IPC)');
            return $result;
        }

        if(isset($data['demandLevel']) && strlen($data['demandLevel']) > 0){
            switch($data['demandLevel']){
                case '简单定制':
                    $level = 1;
                    break;
                case '中度定制':
                    $level = 2;
                    break;
                case '深度定制':
                    $level = 3;
                    break;
                default:
                    $result = array('error' => 1, 'msg' => '获取到PRS订单定制程度['.$data['demandLevel'].']不在指定范围内');
                    return $result;
            }
        }else{
            $level = 0;
        }

        $status = isset($data['msgVo']['state']) ? trim($data['msgVo']['state']) : '';
        if(strlen($status) <= 0 || mb_strlen($status, 'UTF-8') > 20){
            $result = array('error' => 1, 'msg' => '获取到PRS订单单号状态为空或者超过20个字符');
            return $result;
        }
        $customer = trim($data['infoClient']);
        if(mb_strlen($customer, 'UTF-8') > 100){
            $result = array('error' => 1, 'msg' => '获取到PRS订单客户名称['.$customer.']超过100个字符');
            return $result;
        }

        $contacts = trim($data['infoLinkman']);
        if(mb_strlen($contacts, 'UTF-8') > 100){
            $result = array('error' => 1, 'msg' => '获取到PRS订单客户联系人['.$contacts.']超过50个字符');
            return $result;
        }

        $contact_tel = trim($data['infoTel']);
        if(mb_strlen($contact_tel, 'UTF-8') > 30){
            $result = array('error' => 1, 'msg' => '获取到PRS订单客户联系方式['.$contact_tel.']超过30个字符');
            return $result;
        }

        $contact_address = trim($data['infoTel']);
        if(mb_strlen($contact_address, 'UTF-8') > 255){
            $result = array('error' => 1, 'msg' => '获取到PRS订单客户联系地址['.$contact_address.']超过255个字符');
            return $result;
        }

        $salesman = trim($data['infoBiller']);
        if(strlen($salesman) > 0){
            if(mb_strlen($salesman, 'UTF-8') > 50){
                $result = array('error' => 1, 'msg' => '获取到PRS订单业务员['.$salesman.']超过50个字符');
                return $result;
            }
            //查询业务员对应的Task用户ID
            $user_id = M('user')->where(array('user_name' => $salesman))->getField('user_id');
            $salesman_id = $user_id ? $user_id : 0;
        }else{
            $salesman_id = 0;
        }

        $assistant = trim($data['infoAssistant']);
        if(strlen($assistant) > 0){
            if(mb_strlen($assistant, 'UTF-8') > 50){
                $result = array('error' => 1, 'msg' => '获取到PRS订单业务助理['.$assistant.']超过50个字符');
                return $result;
            }
            //查询业务助理对应的Task用户ID
            $user_id = M('user')->where(array('user_name' => $assistant))->getField('user_id');
            $assistant_id = $user_id ? $user_id : 0;
        }else{
            $assistant_id = 0;
        }

        //prs产品信息
        $pruducts = isset($data['productVos']) && !empty($data['productVos']) ? $data['productVos'] : array();
        $pruducts = json_encode($pruducts);
        if(!get_magic_quotes_gpc()){
            $pruducts = _addslashes($pruducts);
        }

        //硬件类信息(DVR/NVR包括:主板； IPC包括：模组、机芯、镜头、POE支持、尾线)
        $hardware_info = array();
        if(strlen(trim($data['hardwareMainboard'])) > 0){		//主板
            $hardware_info['mainboard'] = trim($data['hardwareMainboard']);
            $hardware_info['mainboardDesc'] = trim($data['hardwareMainboardRemark']);
            $hardware_info['mainboardErpno'] = trim($data['hardwareMainboardErpno']);
        }

        if(strlen(trim($data['hardwareModule'])) > 0){			//模组
            $hardware_info['module'] = trim($data['hardwareModule']);
            $hardware_info['moduleDesc'] = trim($data['hardwareModuleRemark']);
        }

        if(strlen(trim($data['hardwareBlock'])) > 0){			//机芯
            $hardware_info['block'] = trim($data['hardwareBlock']);
            $hardware_info['blockDesc'] = trim($data['hardwareBlockRemark']);
            $hardware_info['blockErpno'] = trim($data['hardwareBlockErpno']);
        }

        if(strlen(trim($data['hardwareLens'])) > 0){			//镜头
            $hardware_info['lens'] = trim($data['hardwareLens']);
            $hardware_info['lensDesc'] = trim($data['hardwareLensRemark']);
            $hardware_info['lensErpno'] = trim($data['hardwareLensErpno']);
        }

        if(strlen(trim($data['hardwarePoes'])) > 0){			//poe支持
            $hardware_info['poes'] = trim($data['hardwarePoes']);
            $hardware_info['poesDesc'] = trim($data['hardwarePoesRemark']);
        }

        if(strlen(trim($data['hardwareCable'])) > 0){			//尾线
            $hardware_info['cable'] = trim($data['hardwareCable']);
            $hardware_info['cableDesc'] = trim($data['hardwareCableRemark']);
            $hardware_info['cableErpno'] = trim($data['hardwareCableErpno']);
        }

        if(strlen(trim($data['hardwareRemark'])) > 0){
            $hardware_info['remark'] = trim($data['hardwareRemark']);
        }
        $hardware_info = json_encode($hardware_info);
        if(!get_magic_quotes_gpc()){
            $hardware_info = _addslashes($hardware_info);
        }

        //附件类信息
        $attachment_info = array();
        if(strlen($data['annexPower']) > 0){					//电源(适配器)
            $attachment_info['power'] = trim($data['annexPower']);
            $attachment_info['powerDesc'] = trim($data['annexPowerRemark']);
        }

        if(strlen($data['annexPowerline']) > 0){				//电源线
            $attachment_info['powerline'] = trim($data['annexPowerline']);
            $attachment_info['powerlineDesc'] = trim($data['annexPowerlineRemark']);
        }

        $instruct = array();									//说明书
        if(strlen($data['annexDescription']) > 0){
            $instruct['instructions'] = trim($data['annexDescription']);
            $instruct['instructionsDesc'] = trim($data['annexDescriptionRemark']);
            $instruct['instructionsErpno'] = trim($data['annexDescDescriptionErpno']);
        }
        if(strlen($data['annexDescContent']) > 0){
            $instruct['instructionsContent'] = trim($data['annexDescContent']);
        }
        if(strlen($data['annexDescCount']) > 0){
            $instruct['instructionsNum'] = trim($data['annexDescCount']);
        }
        !empty($instruct) && $attachment_info['instruct'] = $instruct;

        $disk = array();										//光盘
        if(strlen($data['annexDisk']) > 0){
            $disk['disk'] = trim($data['annexDisk']);
            $disk['diskDesc'] = trim($data['annexDiskRemark']);
        }
        if(strlen($data['annexDiskPrint']) > 0){
            $disk['diskPrint'] = trim($data['annexDiskPrint']);
            $disk['diskPrintDesc'] = trim($data['annexDiskPrintRemark']);
            $disk['diskPrintErpno'] = trim($data['annexDiskPrintErpno']);
        }
        if(strlen($data['annexDiskContent']) > 0){
            $disk['diskContent'] = trim($data['annexDiskContent']);
            $disk['diskContentDesc'] = trim($data['annexDiskContentRemark']);
            $disk['diskContentErpno'] = trim($data['annexDiskContentErpno']);
        }
        if(strlen($data['annexDiskCount']) > 0){
            $disk['diskNum'] = trim($data['annexDiskCount']);
        }
        !empty($disk) && $attachment_info['disk'] = $disk;

        $remote = array();										//遥控器
        if(strlen($data['annexRemote']) > 0){
            $remote['remote'] = trim($data['annexRemote']);
            $remote['remoteDesc'] = trim($data['annexRemoteRemark']);
        }
        if(strlen($data['annexRemotePower']) > 0){
            $remote['remotePower'] = trim($data['annexRemotePower']);
            $remote['remotePowerDesc'] = trim($data['annexRemotePowerRemark']);
        }
        !empty($remote) && $attachment_info['remote'] = $remote;

        if(strlen($data['annexCertificate']) > 0){				//合格证
            $attachment_info['certificate'] = trim($data['annexCertificate']);
            $attachment_info['certificateDesc'] = trim($data['annexCertificateRemark']);
        }

        if(strlen($data['annexWarrantycard']) > 0){				//保修卡
            $attachment_info['warrantycard'] = trim($data['annexWarrantycard']);
            $attachment_info['warrantycardDesc'] = trim($data['annexWarrantycardRemark']);
        }

        $mouse = array();										//鼠标
        if(strlen($data['annexMouse']) > 0){
            $mouse['mouse'] = trim($data['annexMouse']);
            $mouse['mouseDesc'] = trim($data['annexMouseRemark']);
        }
        if(strlen($data['annexMousePrint']) > 0){
            $mouse['mousePrint'] = trim($data['annexMousePrint']);
            $mouse['mousePrintDesc'] = trim($data['annexMousePrintRemark']);
            $mouse['mousePrintErpno'] = trim($data['annexMousePrintErpno']);
        }
        !empty($mouse) && $attachment_info['mouse'] = $mouse;

        if(strlen(trim($data['annexRemark'])) > 0){				//备注
            $attachment_info['remark'] = trim($data['annexRemark']);
        }
        $attachment_info = json_encode($attachment_info);
        if(!get_magic_quotes_gpc()){
            $attachment_info = _addslashes($attachment_info);
        }

        //结构件类信息
        $structure_info = array();
        if(!empty($data['structBoxItems'])){
            $structure_info['wholePackage'] = $data['structBoxItems'];
        }

        $face = $logo = array();	//前面板（DVR/NVR）或机身（IPC）
        if(!empty($data['structFaceLogoPrintItems'])){
            // $logo['faceLogoPrint'] = trim($data['structFaceLogoPrint']);
            $logo['faceLogoPrintItems'] = $data['structFaceLogoPrintItems'];
        }

        if(strlen($data['structFaceLogoMetal']) > 0){
            $logo['faceLogoMetal'] = trim($data['structFaceLogoMetal']);
            $logo['faceLogoMetalDesc'] = trim($data['structFaceLogoMetalRemark']);
            $logo['faceLogoMetalErpno'] = trim($data['structFaceLogoMetalErpno']);
        }
        !empty($logo) && $face['logo'] = $logo;

        if($product_type == 1){			//DVR/NVR
            if(strlen($data['structFace']) > 0){
                $face['face'] = trim($data['structFace']);
                $face['faceDesc'] = trim($data['structFaceRemark']);
                $face['faceErpno'] = trim($data['structFaceErpno']);
            }

            if(strlen($data['structFaceBlock']) > 0){
                $face['faceBlock'] = trim($data['structFaceBlock']);
                $face['faceBlockDesc'] = trim($data['structFaceBlockRemark']);
            }
        }
        !empty($face) && $structure_info['face'] = $face;

        $case = array();
        if(strlen($data['structCaseTop']) > 0){
            $case['caseTop'] = trim($data['structCaseTop']);
            $case['caseTopDesc'] = trim($data['structCaseTopRemark']);
            $case['caseTopErpno'] = trim($data['structCaseTopErpno']);
        }

        if(strlen($data['structCaseBottom']) > 0){
            $case['caseBottom'] = trim($data['structCaseBottom']);
            $case['caseBottomDesc'] = trim($data['structCaseBottomRemark']);
            $case['caseBottomErpno'] = trim($data['structCaseBottomErpno']);
        }

        if(strlen($data['structCaseBack']) > 0){
            $case['caseBack'] = trim($data['structCaseBack']);
            $case['caseBackDesc'] = trim($data['structCaseBackRemark']);
            $case['caseBackErpno'] = trim($data['structCaseBackErpno']);
        }
        !empty($case) && $structure_info['case'] = $case;

        if(strlen($data['structInner']) > 0){
            $structure_info['inner'] = trim($data['structInner']);
            $structure_info['innerDesc'] = trim($data['structInnerRemark']);
            $structure_info['innerErpno'] = trim($data['structInnerErpno']);
        }

        if(strlen(trim($data['structRemark'])) > 0){				//备注
            $structure_info['remark'] = trim($data['structRemark']);
        }

        $structure_info = json_encode($structure_info);
        if(!get_magic_quotes_gpc()){
            $structure_info = _addslashes($structure_info);
        }

        //标签类信息
        $label_info = array();
        if(strlen($data['labelInner']) > 0){
            $label_info['inner'] = trim($data['labelInner']);
            $label_info['innerDesc'] = trim($data['labelInnerRemark']);
        }
        if(strlen($data['labelOuter']) > 0){
            $label_info['outer'] = trim($data['labelOuter']);
            $label_info['outerDesc'] = trim($data['labelOuterRemark']);
        }
        if(strlen($data['labelHeader']) > 0){
            $label_info['header'] = trim($data['labelHeader']);
            $label_info['headerDesc'] = trim($data['labelHeaderRemark']);
        }
        if(strlen($data['labelRemark']) > 0){
            $label_info['remark'] = trim($data['labelRemark']);
        }

        $label_info = json_encode($label_info);
        if(!get_magic_quotes_gpc()){
            $label_info = _addslashes($label_info);
        }

        //出货方式
        $sell_style = array();
        if(strlen($data['sellStyle']) > 0){					//出货方式
            $sell_style['sellStyle'] = trim($data['sellStyle']);
            $sell_style['sellStyleDesc'] = trim($data['sellStyleRemark']);
            $sell_style['sellStyleErpno'] = trim($data['sellStyleErpno']);
        }
        if(strlen($data['sellPackStyle']) > 0){				//包装方式
            $sell_style['sellPackStyle'] = trim($data['sellPackStyle']);
            $sell_style['sellPackStyleDesc'] = trim($data['sellPackStyleRemark']);
        }
        if(strlen($data['sellBox']) > 0){					//大外箱
            $sell_style['sellBox'] = trim($data['sellBox']);
            $sell_style['sellBoxDesc'] = trim($data['sellBoxRemark']);
            $sell_style['sellBoxErpno'] = trim($data['sellBoxErpno']);
        }
        if(strlen($data['sellRemark']) > 0){				//备注
            $sell_style['remark'] = trim($data['sellRemark']);
        }

        $sell_style = json_encode($sell_style);
        if(!get_magic_quotes_gpc()){
            $sell_style = _addslashes($sell_style);
        }

        //固件程序信息
        $program_info = array();
        $burn = array();
        if(strlen($data['programUpdate']) > 0){				//定制
            $burn['update'] = trim($data['programUpdate']);
            $burn['updateDesc'] = trim($data['programUpdateRemark']);
        }

        if(strlen($data['programUpdateSn']) > 0){			//需求编号
            $burn['updateSn'] = trim($data['programUpdateSn']);
        }

        if(strlen($data['programUpdateName']) > 0){			//程序链接
            $burn['updateName'] = trim($data['programUpdateName']);
        }
        !empty($burn) && $program_info['burn'] = $burn;

        $process = array();
        if(strlen($data['programMakeLogo']) > 0){				//开机LOGO
            $process['makeLogo'] = trim($data['programMakeLogo']);
            $process['makeLogoDesc'] = trim($data['programMakeLogoRemark']);
        }

        if(strlen($data['programMakeLanguage']) > 0){			//语言
            $process['makeLanguage'] = trim($data['programMakeLanguage']);
            $process['makeLanguageDesc'] = trim($data['programMakeLanguageRemark']);
        }

        if(strlen($data['programMakeVideo']) > 0){				//视频制式
            $process['makeVideo'] = trim($data['programMakeVideo']);
            $process['makeVideoDesc'] = trim($data['programMakeVideoRemark']);
        }

        if(strlen($data['programMakeChannel']) > 0){			//通道模式
            $process['makeChannel'] = trim($data['programMakeChannel']);
            $process['makeChannelDesc'] = trim($data['programMakeChannelRemark']);
        }

        if(strlen($data['programMakeMac']) > 0){				//MAC地址
            $process['makeMac'] = trim($data['programMakeMac']);
            $process['makeMacDesc'] = trim($data['programMakeMacRemark']);
        }
        !empty($process) && $program_info['process'] = $process;

        if(strlen($data['programRemark']) > 0){				//备注
            $program_info['remark'] = trim($data['programRemark']);
        }

        $program_info = json_encode($program_info);
        if(!get_magic_quotes_gpc()){
            $program_info = _addslashes($program_info);
        }

        //出货检验信息
        $oqc_info = array();
        if(strlen($data['programCheckStd']) > 0){
            $oqc_info['check'] = trim($data['programCheckStd']);
            // $oqc_info['checkDesc'] = trim($data['programCheckRemark']);
        }
        if(strlen($data['programCheckRemark']) > 0){				//备注
            $oqc_info['remark'] = trim($data['programCheckRemark']);
        }
        $oqc_info = json_encode($oqc_info);
        if(!get_magic_quotes_gpc()){
            $oqc_info = _addslashes($oqc_info);
        }

        //文件信息
        $file_info = array();
        if(strlen($data['pathRequest']) > 0){
            $file_info['pathRequest'] = trim($data['pathRequest']);
        }

        if(strlen($data['pathCraft']) > 0){
            $file_info['pathCraft'] = trim($data['pathCraft']);
        }

        if(strlen($data['pathChecking']) > 0){
            $file_info['pathChecking'] = trim($data['pathChecking']);
        }

        if(strlen($data['pathRemark']) > 0){
            $file_info['remark'] = trim($data['pathRemark']);
        }
        $file_info = json_encode($file_info);
        if(!get_magic_quotes_gpc()){
            $file_info = _addslashes($file_info);
        }

        //查看prs订单是否已经存在
        $prs_order_model = M('prs_order');
        $exist = $prs_order_model->where(array('prs_id' => $prs_id))->field('id, prs_sn, name, product_type, level, status, customer, contacts, contact_tel, contact_address, salesman, salesman_id, assistant, assistant_id, pruducts, hardware_info, attachment_info, structure_info, label_info, sell_style, program_info, oqc_info, file_info')->find();
        $prs_order_model->startTrans();
        if(empty($exist)){
            $addData = array(
                'prs_id'	=> $prs_id,
                'prs_sn'	=> $prs_sn,
                'name'	=> $name,
                'product_type'	=> $product_type,
                'level'	=> $level,
                'status'	=> $status,
                'customer'	=> $customer,
                'contacts'	=> $contacts,
                'contact_tel'	=> $contact_tel,
                'contact_address'	=> $contact_address,
                'salesman'	=> $salesman,
                'salesman_id'	=> $salesman_id,
                'assistant'	=> $assistant,
                'assistant_id'	=> $assistant_id,
                'pruducts'	=> $pruducts,
                'hardware_info'	=> $hardware_info,
                'attachment_info'	=> $attachment_info,
                'structure_info'	=> $structure_info,
                'label_info'	=> $label_info,
                'sell_style'	=> $sell_style,
                'program_info'	=> $program_info,
                'oqc_info'	=> $oqc_info,
                'file_info'	=> $file_info
            );

            $prs_order_id = $prs_order_model->add($addData);
            if(!$prs_order_id){
                $prs_order_model->rollback();
                $result = array('error' => 1, 'msg' => '获取到PRS订单信息同步到TASK失败');
                return $result;
            }

        }else{
            $updateData = array();
            $fields = array('prs_sn', 'name', 'product_type', 'level', 'status', 'customer', 'contacts', 'contact_tel', 'contact_address', 'salesman', 'salesman_id', 'assistant', 'assistant_id', 'pruducts', 'hardware_info', 'attachment_info', 'structure_info', 'label_info', 'sell_style', 'program_info', 'oqc_info', 'file_info');
            foreach($fields as $v) {
                if($exist[$v] != $$v) {
                    $updateData[$v] = $$v;
                }
            }

            if(!empty($updateData)){
                $res = $prs_order_model->where(array('id' => $exist['id']))->save($updateData);
                if($res === false){
                    $prs_order_model->rollback();
                    $result = array('error' => 1, 'msg' => '获取到PRS订单信息更新到TASK失败');
                    return $result;
                }
            }
            $prs_order_id = $exist['id'];
        }

        $prs_order_model->commit();
        $result = array('error' => 0, 'msg' => '', 'prs_order_id' => $prs_order_id);
        return $result;

    }elseif(!empty($reponseData['msg'])) {
        $result = array('error' => 1, 'msg' => '获取PRS订单信息失败['.$reponseData['code'].'&'.$reponseData['msg'].']');
        return $result;
    }else{
        $result = array('error' => 1, 'msg' => '获取PRS订单信息失败');
        return $result;
    }
}

/**
+----------------------------------------------------------
 * 获取物料的最低最高库存参考量
+----------------------------------------------------------
 * @param  int    $obj 	指定的物料
+----------------------------------------------------------
 * @param  array|int    $type 	物料类型  1是数组类型  2是非数组
+----------------------------------------------------------
 * @param  int        $mergeType 	物料合并类型  1相同编码, 2相同销售型号, 3相同产品线, 4相同事业部
+----------------------------------------------------------
 * @return array
+----------------------------------------------------------
 */
function getMaterielStockSuggestNum($obj, $type, $mergeType = 0){
    $today = date('Y-m-d');
    $last_three_month_first_day = date('Y-m-01', strtotime('-2 month'));
    $last_six_month_first_day = date('Y-m-01', strtotime('-5 month'));

    $this_month_first_day = date('Y-m-01');
    $this_month_month_times = strtotime($this_month_first_day);
    $last_month_last_day = date('Y-m-t', strtotime('-1 day', $this_month_month_times));//上个月最后一天

    $this_month_last_day = date('Y-m-t');
    $month = date('m') == 1 ? 12 : date('m')-1;
    $last_month = date('Y').'-'.($month > 9 ? $month : '0'.$month);

    $future_thirty_day = date('Y-m-d', strtotime('+30 day'));
    $future_sixty_day =  date('Y-m-d', strtotime('+60 day'));
    $future_fourty_five_day =  date('Y-m-d', strtotime('+45 day'));

    $materielStockSuggest = array();
    if($mergeType){
        $groupDS = 'real_materiel_id';
        $demandSql = M('materiel_forecast_demand_month_statistics2')->alias('d')
            ->join('LEFT JOIN __MATERIEL_FORECAST_DEMAND_MONTH_PLACE_STATISTICS__ p ON p.log_id = d.id')
            ->where(array('p.expect_deadline' => array(array('egt', $today), array('elt', $future_sixty_day)), 'd.real_materiel_id' => array('IN', $obj)))
            ->field('d.real_materiel_id as real_materiel_id, SUM(if((p.expect_deadline >= "'.$today.'" AND p.expect_deadline <= "'.$future_thirty_day.'") , p.demand_num, 0)) as future_30_num, SUM(if((p.expect_deadline >= "'.$today.'" AND p.expect_deadline <= "'.$future_fourty_five_day.'") , p.demand_num, 0)) as future_45_num, SUM(if((p.expect_deadline >= "'.$today.'" AND p.expect_deadline <= "'.$future_sixty_day.'") , p.demand_num, 0)) as future_60_num')
            ->group($groupDS)
            ->buildSql();

        $saleForecastSql = M('final_forecast_num2')->alias('ffn')
            ->where(array('expect_deadline' => array(array('egt', $today), array('elt', $future_sixty_day)), 'real_materiel_id' => array('IN', $obj)))
            ->field('real_materiel_id as real_materiel_id, SUM(if((expect_deadline >= "'.$today.'" AND expect_deadline <= "'.$future_thirty_day.'") , final_num, 0)) as future_30_num, SUM(if((expect_deadline >= "'.$today.'" AND expect_deadline <= "'.$future_fourty_five_day.'") , final_num, 0)) as future_45_num, SUM(final_num) as future_60_num')
            ->group($groupDS)
            ->buildSql();

        $outListSql = M('outbound_order')->alias('o')
            ->where(array('o.create_date' => array(array('egt', $last_six_month_first_day.' 00:00:00')), 'o.materiel_id'=> array('IN', $obj)))
            ->group($groupDS)
            ->field('o.materiel_id as real_materiel_id, SUM(o.num) last_six_month_num, ROUND(SUM(IF((o.create_date >= "' . ($last_three_month_first_day.' 00:00:00') . '" AND o.create_date <= "'.($this_month_last_day.' 23:59:59').'"), o.num, 0))) last_three_month_num, ROUND(SUM(IF((o.create_date >= "' . $last_month . '-01 00:00:00' . '" AND o.create_date <= "'.($last_month_last_day.' 23:59:59').'"), o.num, 0))) last_month_num, SUM(IF((o.create_date >="'.$last_three_month_first_day.' 00:00:00'.'" AND o.create_date <= "'.$last_month_last_day.' 23:59:59'.'"), o.num, 0)) as last_two_month_num, SUM(IF((o.create_date >="'.$this_month_first_day.' 00:00:00'.'" AND o.create_date <= "'.$today.' 23:59:59'.'"), num, 0)) as this_month_num')
            ->buildSql();

        $tempCond['r.id'] = array('IN', $obj);
        $tempCond['_complex'] = array(
            'd.real_materiel_id' => array('gt', '0'),
            's.real_materiel_id' => array('gt', '0'),
            'o.real_materiel_id' => array('gt', '0'),
            '_logic' => 'OR'
        );
        /*IF(d.future_30_num > 0, d.future_30_num, s.future_30_num) as future_30_need_num,
            IF(d.future_45_num > 0, d.future_45_num, s.future_45_num) as future_45_need_num,
            IF(d.future_60_num > 0, d.future_60_num, s.future_60_num) as future_60_need_num,
            o.this_month_num / date_format(NOW(), "%d") * DAY(LAST_DAY(NOW())) as this_month_total,this_month_num,
            IF(o.last_two_month_num > 0, o.last_two_month_num , 0) as last_two_month_num,
            ( o.this_month_num / date_format(NOW(), "%d") * DAY(LAST_DAY(NOW()))  + IF(o.last_two_month_num > 0, o.last_two_month_num , 0))/3 as last_avag_three_month_num,*/
        $sumField = ', SUM(
                CASE WHEN r.customized_type = 2  
                     THEN  IF(r.control_materiel_type = 4 , 0, IF(d.future_30_num > 0, d.future_30_num, IFNULL(s.future_30_num, 0)) * r.purchase_ratio  * r.turnover_ratio * r.produce_ratio * r.develop_ratio * r.strategy_ratio * r.sales_radio * r.quality_ratio * 0.2)
                     WHEN r.customized_type <> 2 
                     THEN  IF(r.control_materiel_type = 4, 0, greatest(ROUND(( ROUND(IFNULL(o.this_month_num, 0) / date_format(NOW(), "%d") * DAY(LAST_DAY(NOW())))  + IF(o.last_two_month_num > 0, o.last_two_month_num, 0))/3) , IF(d.future_30_num > 0, d.future_30_num, IFNULL(s.future_30_num, 0))) * r.purchase_ratio  * r.turnover_ratio * r.produce_ratio * r.develop_ratio * r.strategy_ratio * r.sales_radio * r.quality_ratio * 0.2) END
                ) as minstoresuggest,
              SUM(
                CASE WHEN r.customized_type = 2  
                     THEN   IF(d.future_60_num > 0, d.future_60_num, IFNULL(s.future_60_num, 0)) * r.purchase_ratio  * r.turnover_ratio * r.produce_ratio * r.develop_ratio * r.strategy_ratio * r.sales_radio * r.quality_ratio
                     WHEN r.customized_type <> 2 
                     THEN  greatest(ROUND(( ROUND(IFNULL(o.this_month_num, 0) / date_format(NOW(), "%d") * DAY(LAST_DAY(NOW())))  + IF(o.last_two_month_num > 0, o.last_two_month_num, 0))/3) , IF(d.future_60_num > 0, d.future_60_num, IFNULL(s.future_60_num, 0))) * r.purchase_ratio  * r.turnover_ratio * r.produce_ratio * r.develop_ratio * r.strategy_ratio * r.sales_radio * r.quality_ratio END
                ) as maxstoresuggest,
                SUM(
                CASE WHEN r.customized_type = 2  
                     THEN   IF(d.future_45_num > 0, d.future_45_num, IFNULL(s.future_45_num, 0)) * r.purchase_ratio  * r.turnover_ratio * r.produce_ratio * r.develop_ratio * r.strategy_ratio * r.sales_radio * r.quality_ratio * 0.6
                     WHEN r.customized_type <> 2 
                     THEN  greatest(ROUND(( ROUND(IFNULL(o.this_month_num, 0) / date_format(NOW(), "%d") * DAY(LAST_DAY(NOW())))  + IF(o.last_two_month_num > 0, o.last_two_month_num , 0))/3) , IF(d.future_45_num > 0, d.future_45_num, IFNULL(s.future_45_num, 0))) * r.purchase_ratio  * r.turnover_ratio * r.produce_ratio * r.develop_ratio * r.strategy_ratio * r.sales_radio * r.quality_ratio * 0.6 END
                ) as enoughstoresuggest';
        if($mergeType == 1){
            $group = 'same_code';
            $field = 'CONCAT(RIGHT(SUBSTRING_INDEX(r.code,".",1),1),".",SUBSTRING_INDEX(r.code,".",-4)) as same_code' . $sumField;
        }elseif($mergeType == 2){
            $group = 'same_code';
            $field = 'r.belong_mid as same_code' . $sumField;
        }elseif($mergeType == 3){
            $group = 'same_code';
            $field = 'spt.product_line as same_code' . $sumField;
        }elseif($mergeType == 4){
            $group = 'same_code';
            $field = 'tsl.division_id as same_code' . $sumField;
        }

        $demandSaleforeSql = M('real_materiel')->alias('r')
            ->join('LEFT JOIN __SALE_PRODUCT__ spt ON spt.id = r.belong_mid')
            ->join("LEFT JOIN $demandSql d ON d.real_materiel_id = r.id")
            ->join("LEFT JOIN $saleForecastSql s ON s.real_materiel_id = r.id")
            ->join("LEFT JOIN $outListSql o ON o.real_materiel_id = r.id");
        if($mergeType == 4){
            $tempSql = M('materiel_proline')->alias('p')->join('LEFT JOIN __MATERIEL_PROLINE_TO_DIVISION__ d ON d.proline_id = p.id')->join('LEFT JOIN __DIVISION__ dsn ON dsn.id = d.did')->where(array('dsn.company'=> array('neq', '')))->field('p.name as proline_name, IF(d.category=1 , ("1,2"), (d.category-1)) as cus, d.proline_id, dsn.name, dsn.id as division_id, dsn.company')->buildSql();
            $demandSaleforeSql->join("LEFT JOIN $tempSql tsl ON tsl.proline_id = spt.product_line AND tsl.cus LIKE CONCAT('%',r.customized_type,'%')");
        }
        $demandSaleforeList = $demandSaleforeSql
            ->where($tempCond)
            ->field($field)
            ->group($group)
            ->select();
//        $fieldDSO = 'r.id, r.belong_mid as same_code, r.customized_type, r.control_materiel_type, r.purchase_ratio, r.turnover_ratio, r.produce_ratio, r.develop_ratio, r.strategy_ratio, r.sales_radio, d.real_materiel_id drid, s.real_materiel_id srid, o.real_materiel_id orid, d.future_30_num d_future_30_num, d.future_45_num d_future_45_num, d.future_60_num d_future_60_num, s.future_30_num s_future_30_num, s.future_45_num s_future_45_num, s.future_60_num s_future_60_num, o.last_six_month_num, o.last_three_month_num, o.last_month_num, o.this_month_num';

        foreach ($demandSaleforeList as $val){
            $materielStockSuggest[$val['same_code']]['min_stock_suggest'] = round($val['minstoresuggest']);
            $materielStockSuggest[$val['same_code']]['enough_stock_suggest'] = round($val['enoughstoresuggest']);
            $materielStockSuggest[$val['same_code']]['max_stock_suggest'] = round($val['maxstoresuggest']);
        }
        return  $materielStockSuggest;
    }

    if($type == 1){
        $materiel_list = M('real_materiel')->where(array('id' => array('IN', $obj)))->getField('id, name, code ,customized_type, control_materiel_type, purchase_ratio, turnover_ratio, produce_ratio, develop_ratio, strategy_ratio, secret_ratio, sales_radio, quality_ratio', true);

        $out_lists = M('outbound_order')->alias('r')
            ->where(array('r.create_date' => array(array('egt', $last_six_month_first_day.' 00:00:00')), 'r.materiel_id' => array('IN', $obj)))
            ->group('r.materiel_id')
            ->getField('r.materiel_id, SUM(r.num) last_six_month_num, ROUND(SUM(IF((r.create_date >= "' . ($last_three_month_first_day.' 00:00:00') . '" AND r.create_date <= "'.($this_month_last_day.' 23:59:59').'"), r.num, 0))) last_three_month_num, ROUND(SUM(IF((r.create_date >= "' . $last_month . '-01 00:00:00' . '" AND r.create_date <= "'.($last_month_last_day.' 23:59:59').'"), r.num, 0))) last_month_num, SUM(IF((create_date >="'.$last_three_month_first_day.' 00:00:00'.'" AND create_date <= "'.$last_month_last_day.' 23:59:59'.'"), num, 0)) as last_two_month_num, SUM(IF((create_date >="'.$this_month_first_day.' 00:00:00'.'" AND create_date <= "'.$today.' 23:59:59'.'"), num, 0)) as this_month_num', true);

        //未来预测需求量
        $demandList = M('materiel_forecast_demand_month_statistics2')->alias('d')
            ->join('LEFT JOIN __MATERIEL_FORECAST_DEMAND_MONTH_PLACE_STATISTICS__ p ON p.log_id = d.id')
            ->where(array('p.expect_deadline' => array(array('egt', $today), array('elt', $future_sixty_day)), 'd.real_materiel_id' => array('IN', $obj)))
            ->field('d.real_materiel_id, SUM(if((p.expect_deadline >= "'.$today.'" AND p.expect_deadline <= "'.$future_thirty_day.'") , p.demand_num, 0)) as future_30_num, SUM(if((p.expect_deadline >= "'.$today.'" AND p.expect_deadline <= "'.$future_fourty_five_day.'") , p.demand_num, 0)) as future_45_num, SUM(if((p.expect_deadline >= "'.$today.'" AND p.expect_deadline <= "'.$future_sixty_day.'") , p.demand_num, 0)) as future_60_num')
            ->group('d.real_materiel_id')
            ->select();

        $demandLists = array();
        foreach ($demandList as $k=>$v){
            $demandLists[$v['real_materiel_id']] = $v;
        }

        $saleForecastList = M('final_forecast_num2')
            ->where(array('expect_deadline' => array(array('egt', $today), array('elt', $future_sixty_day)), 'real_materiel_id' => array('IN', $obj)))
            ->group('real_materiel_id')
            ->getField('real_materiel_id, SUM(if((expect_deadline >= "'.$today.'" AND expect_deadline <= "'.$future_thirty_day.'") , final_num, 0)) as future_30_num, SUM(if((expect_deadline >= "'.$today.'" AND expect_deadline <= "'.$future_fourty_five_day.'") , final_num, 0)) as future_45_num, SUM(final_num) as future_60_num', true);

        foreach ($obj as $mid){
            $future_30_need_num = empty($demandLists[$mid]['future_30_num']) ? (empty($saleForecastList[$mid]['future_30_num']) ? 0 : $saleForecastList[$mid]['future_30_num']) : $demandLists[$mid]['future_30_num'];
            $future_45_need_num = empty($demandLists[$mid]['future_45_num']) ? (empty($saleForecastList[$mid]['future_45_num']) ? 0 : $saleForecastList[$mid]['future_45_num']) : $demandLists[$mid]['future_45_num'];
            $future_60_need_num = empty($demandLists[$mid]['future_60_num']) ? (empty($saleForecastList[$mid]['future_60_num']) ? 0 : $saleForecastList[$mid]['future_60_num']) : $demandLists[$mid]['future_60_num'];

            if($materiel_list[$mid]['customized_type'] == 2){
                if($materiel_list[$mid]['control_materiel_type'] == 4){
                    $minStoreSuggest = 0;
                }else{
                    $minStoreSuggest = round($future_30_need_num * $materiel_list[$mid]['purchase_ratio'] * $materiel_list[$mid]['turnover_ratio'] * $materiel_list[$mid]['produce_ratio'] * $materiel_list[$mid]['develop_ratio'] * $materiel_list[$mid]['strategy_ratio'] * $materiel_list[$mid]['sales_radio'] * $materiel_list[$mid]['quality_ratio'] * 0.2);
                }
                $maxStoreSuggest = round($future_60_need_num * $materiel_list[$mid]['purchase_ratio'] * $materiel_list[$mid]['turnover_ratio'] * $materiel_list[$mid]['produce_ratio'] * $materiel_list[$mid]['develop_ratio'] * $materiel_list[$mid]['strategy_ratio']* $materiel_list[$mid]['sales_radio']* $materiel_list[$mid]['quality_ratio']);
                $enoughStoreSuggest = round($future_45_need_num * $materiel_list[$mid]['purchase_ratio'] * $materiel_list[$mid]['turnover_ratio'] * $materiel_list[$mid]['produce_ratio'] * $materiel_list[$mid]['develop_ratio'] * $materiel_list[$mid]['strategy_ratio'] * $materiel_list[$mid]['sales_radio'] * $materiel_list[$mid]['quality_ratio']* 0.6);
            }else{
                $this_month_total = round($out_lists[$mid]['this_month_num'] / date('j') * date('t'));
                $last_two_month_num = empty($out_lists[$mid]['last_two_month_num']) ? 0 : $out_lists[$mid]['last_two_month_num'];
                $this_month_num = empty($this_month_total) ? 0 : $this_month_total;
                $last_avag_three_month_num = round(($last_two_month_num + $this_month_num)/3);

                if($materiel_list[$mid]['control_materiel_type'] == 4){
                    $minStoreSuggest = 0;
                }else{
                    $minStoreSuggest = round(max($last_avag_three_month_num, $future_30_need_num) * $materiel_list[$mid]['purchase_ratio'] * $materiel_list[$mid]['turnover_ratio'] * $materiel_list[$mid]['produce_ratio'] * $materiel_list[$mid]['develop_ratio'] * $materiel_list[$mid]['strategy_ratio'] * $materiel_list[$mid]['sales_radio'] * $materiel_list[$mid]['quality_ratio']* 0.2);
                }
                $maxStoreSuggest = round(max($last_avag_three_month_num, $future_60_need_num) * $materiel_list[$mid]['purchase_ratio'] * $materiel_list[$mid]['turnover_ratio'] * $materiel_list[$mid]['produce_ratio'] * $materiel_list[$mid]['develop_ratio'] * $materiel_list[$mid]['strategy_ratio'] * $materiel_list[$mid]['sales_radio']* $materiel_list[$mid]['quality_ratio']);
                $enoughStoreSuggest = round(max($last_avag_three_month_num, $future_45_need_num) * $materiel_list[$mid]['purchase_ratio'] * $materiel_list[$mid]['turnover_ratio'] * $materiel_list[$mid]['produce_ratio'] * $materiel_list[$mid]['develop_ratio'] * $materiel_list[$mid]['strategy_ratio'] * $materiel_list[$mid]['sales_radio'] * $materiel_list[$mid]['quality_ratio']* 0.6);
            }

            $materielStockSuggest[$mid]['min_stock_suggest'] = $minStoreSuggest;
            $materielStockSuggest[$mid]['enough_stock_suggest'] = $enoughStoreSuggest;
            $materielStockSuggest[$mid]['max_stock_suggest'] = $maxStoreSuggest;
            $materielStockSuggest[$mid]['this_month_num'] = $out_lists[$mid]['this_month_num'];
            $materielStockSuggest[$mid]['last_month_num'] = $out_lists[$mid]['last_month_num'];
            $materielStockSuggest[$mid]['last_three_month_num'] = $out_lists[$mid]['last_three_month_num'];
            $materielStockSuggest[$mid]['last_six_month_num'] = $out_lists[$mid]['last_six_month_num'];

        }
    }else{
        $materiel_list = M('real_materiel')->where(array('id' => $obj))->field('id, name, code ,customized_type, control_materiel_type, purchase_ratio, turnover_ratio, produce_ratio, develop_ratio, strategy_ratio, secret_ratio, quality_ratio, sales_radio')->find();

        $out_lists = M('outbound_order')->alias('r')
            ->where(array('r.create_date' => array(array('egt', $last_six_month_first_day.' 00:00:00')), 'r.materiel_id' => $obj))
            ->getField('r.materiel_id, SUM(r.num) last_six_month_num, ROUND(SUM(IF((r.create_date >= "' . ($last_three_month_first_day.' 00:00:00') . '" AND r.create_date <= "'.($this_month_last_day.' 23:59:59').'"), r.num, 0))) last_three_month_num, ROUND(SUM(IF((r.create_date >= "' . $last_month . '-01 00:00:00' . '" AND r.create_date <= "'.($last_month_last_day.' 23:59:59').'"), r.num, 0))) last_month_num, SUM(IF((create_date >="'.$last_three_month_first_day.' 00:00:00'.'" AND create_date <= "'.$last_month_last_day.' 23:59:59'.'"), num, 0)) as last_two_month_num, SUM(IF((create_date >="'.$this_month_first_day.' 00:00:00'.'" AND create_date <= "'.$today.' 23:59:59'.'"), num, 0)) as this_month_num');

        //未来预测需求量
        $demandLists = M('materiel_forecast_demand_month_statistics2')->alias('d')
            ->join('LEFT JOIN __MATERIEL_FORECAST_DEMAND_MONTH_PLACE_STATISTICS__ p ON p.log_id = d.id')
            ->where(array('p.expect_deadline' => array(array('egt', $today), array('elt', $future_sixty_day)), 'd.real_materiel_id' => $obj))
            ->field('SUM(if((p.expect_deadline >= "'.$today.'" AND p.expect_deadline <= "'.$future_thirty_day.'") , p.demand_num, 0)) as future_30_num, SUM(if((p.expect_deadline >= "'.$today.'" AND p.expect_deadline <= "'.$future_fourty_five_day.'") , p.demand_num, 0)) as future_45_num, SUM(if((p.expect_deadline >= "'.$today.'" AND p.expect_deadline <= "'.$future_sixty_day.'") , p.demand_num, 0)) as future_60_num')
            ->find();

        $saleForecastList = M('final_forecast_num2')
            ->where(array('expect_deadline' => array(array('egt', $today), array('elt', $future_sixty_day)), 'real_materiel_id' => $obj))
            ->getField('real_materiel_id, SUM(if((expect_deadline >= "'.$today.'" AND expect_deadline <= "'.$future_thirty_day.'") , final_num, 0)) as future_30_num, SUM(if((expect_deadline >= "'.$today.'" AND expect_deadline <= "'.$future_fourty_five_day.'") , final_num, 0)) as future_45_num, SUM(final_num) as future_60_num');


        $future_30_need_num = empty($demandLists['future_30_num']) ? (empty($saleForecastList[$obj]['future_30_num']) ? 0 : $saleForecastList[$obj]['future_30_num']) : $demandLists['future_30_num'];
        $future_45_need_num = empty($demandLists['future_45_num']) ? (empty($saleForecastList[$obj]['future_45_num']) ? 0 : $saleForecastList[$obj]['future_45_num']) : $demandLists['future_45_num'];
        $future_60_need_num = empty($demandLists['future_60_num']) ? (empty($saleForecastList[$obj]['future_60_num']) ? 0 : $saleForecastList[$obj]['future_60_num']) : $demandLists['future_60_num'];

        if($materiel_list['customized_type'] == 2){
            if($materiel_list['control_materiel_type'] == 4){
                $minStoreSuggest = 0;
            }else{
                $minStoreSuggest = round($future_30_need_num * $materiel_list['purchase_ratio'] * $materiel_list['turnover_ratio'] * $materiel_list['produce_ratio'] * $materiel_list['develop_ratio'] * $materiel_list['strategy_ratio'] * $materiel_list['sales_radio'] * $materiel_list['quality_ratio'] * 0.2);
            }
            $maxStoreSuggest = round($future_60_need_num * $materiel_list['purchase_ratio'] * $materiel_list['turnover_ratio'] * $materiel_list['produce_ratio'] * $materiel_list['develop_ratio'] * $materiel_list['strategy_ratio']* $materiel_list['sales_radio']* $materiel_list['quality_ratio']);
            $enoughStoreSuggest = round($future_45_need_num * $materiel_list['purchase_ratio'] * $materiel_list['turnover_ratio'] * $materiel_list['produce_ratio'] * $materiel_list['develop_ratio'] * $materiel_list['strategy_ratio'] * $materiel_list['sales_radio'] * $materiel_list['quality_ratio']* 0.6);
        }else{
            $this_month_total = round($out_lists[$obj]['this_month_num'] / date('j') * date('t'));
            $last_two_month_num = empty($out_lists[$obj]['last_two_month_num']) ? 0 : $out_lists[$obj]['last_two_month_num'];
            $this_month_num = empty($this_month_total) ? 0 : $this_month_total;
            $last_avag_three_month_num = round(($last_two_month_num + $this_month_num)/3);

            if($materiel_list['control_materiel_type'] == 4){
                $minStoreSuggest = 0;
            }else{
                $minStoreSuggest = round(max($last_avag_three_month_num, $future_30_need_num) * $materiel_list['purchase_ratio'] * $materiel_list['turnover_ratio'] * $materiel_list['produce_ratio'] * $materiel_list['develop_ratio'] * $materiel_list['strategy_ratio'] * $materiel_list['sales_radio'] * $materiel_list['quality_ratio']* 0.2);

            }
            $maxStoreSuggest = round(max($last_avag_three_month_num, $future_60_need_num) * $materiel_list['purchase_ratio'] * $materiel_list['turnover_ratio'] * $materiel_list['produce_ratio'] * $materiel_list['develop_ratio'] * $materiel_list['strategy_ratio'] * $materiel_list['sales_radio']* $materiel_list['quality_ratio']);
            $enoughStoreSuggest = round(max($last_avag_three_month_num, $future_45_need_num) * $materiel_list['purchase_ratio'] * $materiel_list['turnover_ratio'] * $materiel_list['produce_ratio'] * $materiel_list['develop_ratio'] * $materiel_list['strategy_ratio'] * $materiel_list['sales_radio'] * $materiel_list['quality_ratio']* 0.6);
        }

        $materielStockSuggest['min_stock_suggest'] = $minStoreSuggest;
        $materielStockSuggest['enough_stock_suggest'] = $enoughStoreSuggest;
        $materielStockSuggest['max_stock_suggest'] = $maxStoreSuggest;
        $materielStockSuggest['this_month_num'] = $out_lists['this_month_num'];
        $materielStockSuggest['last_month_num'] = $out_lists['last_month_num'];
        $materielStockSuggest['last_three_month_num'] = $out_lists['last_three_month_num'];
        $materielStockSuggest['last_six_month_num'] = $out_lists['last_six_month_num'];
    }
    return  $materielStockSuggest;

}



/******CURL请求********/
function curl($url, $postFields = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    //https 请求
    if(strlen($url) > 5 && strtolower(substr($url,0,5)) == "https" ) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }

    if (is_array($postFields) && 0 < count($postFields)) {
        $postBodyString = "";
        $postMultipart = false;
        foreach ($postFields as $k => $v) {
            if("@" != substr($v, 0, 1)) {	//判断是不是文件上传
                $postBodyString .= "$k=" . urlencode($v) . "&";
            } else {	//文件上传用multipart/form-data，否则用www-form-urlencoded
                $postMultipart = true;
            }
        }
        unset($k, $v);
        curl_setopt($ch, CURLOPT_POST, true);
        if ($postMultipart) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        } else {
            $header = array("content-type: application/x-www-form-urlencoded; charset=UTF-8");
            curl_setopt($ch,CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString,0,-1));
        }
    }
    $reponse = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch), 0);
    } else {
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (200 !== $httpStatusCode) {
            throw new Exception($reponse, $httpStatusCode);
        }
    }
    curl_close($ch);
    return $reponse;
}

function _addslashes($string) {
    if(is_array($string)) {
        foreach($string as $key => $val) {
            $string[$key] = _addslashes($val);
        }
    } else {
        $string = addslashes($string);
    }
    return $string;
}

// 第一个参数：传入要转换的字符串
// 第二个参数：取0，半角转全角；取1，全角到半角
function SBC_DBC($str, $args2) {
    $DBC = Array(
        '０' , '１' , '２' , '３' , '４' ,
        '５' , '６' , '７' , '８' , '９' ,
        'Ａ' , 'Ｂ' , 'Ｃ' , 'Ｄ' , 'Ｅ' ,
        'Ｆ' , 'Ｇ' , 'Ｈ' , 'Ｉ' , 'Ｊ' ,
        'Ｋ' , 'Ｌ' , 'Ｍ' , 'Ｎ' , 'Ｏ' ,
        'Ｐ' , 'Ｑ' , 'Ｒ' , 'Ｓ' , 'Ｔ' ,
        'Ｕ' , 'Ｖ' , 'Ｗ' , 'Ｘ' , 'Ｙ' ,
        'Ｚ' , 'ａ' , 'ｂ' , 'ｃ' , 'ｄ' ,
        'ｅ' , 'ｆ' , 'ｇ' , 'ｈ' , 'ｉ' ,
        'ｊ' , 'ｋ' , 'ｌ' , 'ｍ' , 'ｎ' ,
        'ｏ' , 'ｐ' , 'ｑ' , 'ｒ' , 'ｓ' ,
        'ｔ' , 'ｕ' , 'ｖ' , 'ｗ' , 'ｘ' ,
        'ｙ' , 'ｚ' , '－' , '　' , '：' ,
        '．' , '，' , '／' , '％' , '＃' ,
        '！' , '＠' , '＆' , '（' , '）' ,
        '＜' , '＞' , '＂' , '＇' , '？' ,
        '［' , '］' , '｛' , '｝' , '＼' ,
        '｜' , '＋' , '＝' , '＿' , '＾' ,
        '￥' , '￣' , '｀', '、', '—'
    );

    $SBC = Array( // 半角
        '0', '1', '2', '3', '4',
        '5', '6', '7', '8', '9',
        'A', 'B', 'C', 'D', 'E',
        'F', 'G', 'H', 'I', 'J',
        'K', 'L', 'M', 'N', 'O',
        'P', 'Q', 'R', 'S', 'T',
        'U', 'V', 'W', 'X', 'Y',
        'Z', 'a', 'b', 'c', 'd',
        'e', 'f', 'g', 'h', 'i',
        'j', 'k', 'l', 'm', 'n',
        'o', 'p', 'q', 'r', 's',
        't', 'u', 'v', 'w', 'x',
        'y', 'z', '-', ' ', ':',
        '.', ',', '/', '%', '#',
        '!', '@', '&', '(', ')',
        '<', '>', '"', '\'','?',
        '[', ']', '{', '}', '\\',
        '|', '+', '=', '_', '^',
        '$', '~', '`', ',', '-'
    );

    if ($args2 == 0) {
        return str_replace($SBC, $DBC, $str);  // 半角到全角
    } else if ($args2 == 1) {
        return str_replace($DBC, $SBC, $str);  // 全角到半角
    } else {
        return false;
    }
}
