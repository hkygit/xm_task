<?php
namespace Home\Service;

class PassportService
{
	/**
	 +----------------------------------------------------------
	 * 验证用户是否已登录
	 +----------------------------------------------------------
	 * 按照session -> cookie的顺序检查是否登陆
	 +----------------------------------------------------------
	 * @return boolean 登陆成功是返回true, 否则返回false
	 +----------------------------------------------------------
	 */
	public function isLogged() {
		// 验证本地系统登录
		if(isset($_SESSION[C('USER_AUTH_KEY')]['uid']) && (int)$_SESSION[C('USER_AUTH_KEY')]['uid'] > 0) {
			return true;
		} elseif($uid = $this->getCookieUid()) {
			return $this->loginUserByUid($uid);
		} else {
			return false;
		}
	}

	/**
	 +----------------------------------------------------------
	 * 根据标示符(login_account)和未加密的密码获取用户信息
	 +----------------------------------------------------------
	 * @param string         $login_account   登录账号
	 +----------------------------------------------------------
	 * @param string|boolean $password   未加密的密码
	 +----------------------------------------------------------
	 * @return array|boolean 成功获取用户数据时返回用户信息数组, 否则返回false
	 +----------------------------------------------------------
	 */
	public function getUserInfo($login_account, $password) {
		if(empty($login_account)) {
			return false;
		}

		$user = $condition = $subCondition = $bind = array();

		$user = M('user')->where(array('login_account' => ':login_account', 'status' => array('neq', '-1')))
				->field('user_id, login_account, user_name, password, real_name, role_id, dept, last, ip, salt, work_attendance, status')
				->union('SELECT user_id, login_account, user_name, password, real_name, role_id, dept, last, ip, salt, work_attendance, status FROM __USER__ WHERE user_name=:user_name AND status <> "-1" LIMIT 1', true)
				->bind(array(':login_account' => $login_account, ':user_name' => $login_account))
				->find();

		if(!$user || $user['status'] <= 0) {
			return false;
		} elseif(empty($user['password']) || $this->createPassword($password, $user['salt']) != $user['password']) {
			return false;
		} else {
			return $user;
		}
	}

	/**
	 +----------------------------------------------------------
	 * 验证用户密码
	 +----------------------------------------------------------
	 * @param string         $userName   用户名
	 +----------------------------------------------------------
	 * @param string $password   未加密的密码
	 +----------------------------------------------------------
	 * @return boolean
	 +----------------------------------------------------------
	 */
	public function checkUserPwd($user_id, $password) {
		if(empty($user_id)) {
			return false;
		}

		$user = array();

		$user = M('user')->where(array('user_id' => ':user_id', 'status' => array('egt', '1')))->bind(':user_id', $user_id, \PDO::PARAM_INT)->field('password, salt')->find();

		if(!$user) {
			return false;
		} elseif(empty($user['password']) || $this->createPassword($password, $user['salt']) != $user['password']) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 +----------------------------------------------------------
	 * 验证用户密码
	 +----------------------------------------------------------
	 * @param string         $pwd   未加密的密码
	 +----------------------------------------------------------
	 * @param string	$salt   加密拼接字符
	 +----------------------------------------------------------
	 * @return string 返回加密后的字符串
	 +----------------------------------------------------------
	 */
	public function createPassword($pwd, $salt = '') {
		if(empty($pwd)) {
			return false;
		}

		if(!empty($salt)) {
			$password = MD5(MD5($pwd). $salt);
		} else {
			$password = MD5(substr(MD5($pwd), 0, 12));
		}

		return $password;
	}

	/**
	 * 根据用户ID获取用户信息
	 * 
	 * @param intval   $id	用户编号
	 */
	public function getUserById($id) {
		$user = M('user')->where(array('user_id' => ':user_id'))->bind(array(':user_id' => array($id, \PDO::PARAM_INT)))->field('user_id, user_name, real_name, dept, role_id, sex, birthday, email, qq, mobile, phone, join_time, last, ip, status, user_name_spell, job_number, pid, job_ip, login_account, work_attendance, check_attendance, path, password, salt, part_in_work_time')->find();
		return empty($user) || $user['status'] == '-1' ? false : $user;
	}

	/**
	 * 注册用户的登陆状态 (即: 注册cookie + 注册session + 记录登陆信息)
	 * 
	 * @param array   $user          
	 * @param boolean $is_remeber_me 
	 */
	private function registerLogin(array $user, $is_remeber_me = false) {
		if(empty($user)) {
			return false;
		}

		$session = array(
			'uid' => $user['user_id'],
			'login_account' => $user['login_account'],
			'userName' => $user['user_name'],
			'realName' => $user['real_name'],
			'lastLoginTime' => $user['last'],
			'lastLoginIp' => $user['ip'],
			'roleId' => $user['role_id'],
			'dept' => $user['dept'],
			'work_attendance' => $user['work_attendance']
		);
		session(C('USER_AUTH_KEY'), $session);

		if (!$this->getCookieUid()) {
			$expire = $is_remeber_me ? 3600*24*365 : 0;
			cookie(C('USER_COOKIE_KEY'), base64_encode(C('USER_COOKIE_KEY').'.'.$user['user_id'].'.'.$user['password']), array('expire' => $expire));
		}

		$bind = array(
			':user_id' => array($user['user_id'], \PDO::PARAM_INT),
			':last' => date('Y-m-d H:i:s', time()),
			':ip' => get_client_ip()
		);

		M('user')->where(array('user_id' => ':user_id', 'status' => array('egt', '1')))->bind($bind)->data(array('last' => ':last', 'ip' => ':ip'))->save();

		M('action')->data(array(
			'object_type'	=> ':object_type',
			'object_id'		=> ':object_id',
			'actor_id'		=> ':actor_id',
			'actor'			=> ':actor',
			'action'		=> ':action',
			'date'			=> ':date',
			'comment'		=> ':comment',
			'extra'			=> ':extra',
			'real_actor_id'	=> ':real_actor_id',
			'real_actor'	=> ':real_actor'
		))->bind(array(
			':object_type'	=> '1',
			':object_id'	=> array($user['user_id'], \PDO::PARAM_INT),
			':actor_id'		=> array($user['user_id'], \PDO::PARAM_INT),
			':actor'		=> $user['user_name'],
			':action'		=> 'login',
			':date'			=> date('Y-m-d H:i:s', time()),
			':comment'		=> '',
			':extra'		=> get_client_ip(),
			':real_actor_id'=> array($user['user_id'], \PDO::PARAM_INT),
			':real_actor'	=> $user['user_name']
		))->add();

		return true;  
	}

	/**
	 * 获取cookie中记录的用户ID
	 */
	public function getCookieUid() {
		static $cookie_uid = null;
		if(isset($cookie_uid)) {
			return $cookie_uid;
		}

		$cookie = cookie(C('USER_COOKIE_KEY'));
		$cookie = explode('.', base64_decode($cookie));
		if($cookie[0] !== C('USER_COOKIE_KEY') || empty($cookie[1]) || empty($cookie[2])) {
			return false;
		};

		$user = M('user')->where(array('user_id' => ':user_id', 'status' => array('egt', '1')))->bind(':user_id', $cookie[1], \PDO::PARAM_INT)->field('user_id, password')->find();
		if(empty($user) || $user['password'] != $cookie[2]) {
			return false;
		}

		return $cookie[1];
	}

	//登陆平台
	public function loginUser($login_account, $password, $is_remember_me = false) {
		$user = $roleInfo = array();
		$user = $this->getUserInfo($login_account, $password);

		return $user ? $this->registerLogin($user, $is_remember_me) : false;
	}

	//用uid登陆平台
	private function loginUserByUid($uid, $is_remember_me = false) {
		$user = M('user')->where(array('user_id' => ':user_id', 'status' => array('egt', '1')))->bind(':user_id', $uid, \PDO::PARAM_INT)->field('user_id, login_account, user_name, password, real_name, role_id, dept, last, ip, salt, work_attendance')->find();

		return $user ? $this->registerLogin($user, $is_remember_me) : false;
	}

	//注销
	public function logoutUser() {
		if(!$this->isLogged()) {
			return false;
		}

		$user['user_id'] = $_SESSION[C('USER_AUTH_KEY')]['uid'];
		$user['user_name'] = $_SESSION[C('USER_AUTH_KEY')]['userName'];

		// 注销session
		unset($_SESSION[C('USER_AUTH_KEY')]);
		// 注销cookie
		cookie(C('USER_COOKIE_KEY'), NULL);

		M('action')->data(array(
			'object_type'	=> '1',
			'object_id'		=> $user['user_id'],
			'actor_id'		=> $user['user_id'],
			'actor'			=> $user['user_name'],
			'action'		=> 'logoutUser',
			'date'			=> date('Y-m-d H:i:s', time()),
			'comment'		=> '',
			'extra'			=> get_client_ip(),
			'real_actor_id'	=> $user['user_id'],
			'real_actor'	=> $user['user_name']
		))->add();
	}
}