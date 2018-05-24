<?php
/**
 * Created by PhpStorm.
 * User: XMuser
 * Date: 2018-05-24
 * Time: 11:01
 */

namespace Home\Controller;


class PassportController extends BaseController
{
    public function index() {
        if(service('Passport')->isLogged()) {
            redirect(U('Index/index'));
        }

        $back_act = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : U('index/index');

        $this->assign('back_act', $back_act);
        $this->display();
    }

    public function verify() {
        $config =    array(
            'fontSize'	=>	30,
            'length'	=>	4,
            'seKey'		=>	'this_is_a_xm_key'
        );

        $Verify = new \Think\Verify($config);
        $Verify->entry();
    }

    public function login() {
        $result = array('code' => '10001', 'msg' => '', 'errorTips' => '');

        $action = M("action");

        // if ($this->autoCheckToken(I('post.'))) {	//验证表单令牌
        $login_account = I('post.loginName');
        $password = I('post.loginPass');
        $remember = I('post.remember', 0, 'intval');

        $return = service('Passport')->loginUser($login_account, $password, $remember);
        if(!$return) {
            $result = array('code' => '10003', 'msg' => '用户名或密码错误', 'errorTips' => 'loginNameTips');
            die(json_encode($result));
        }

        // $this->destoryToken(I('post.'));
        // } else {
        // $result = array('code' => '10002', 'msg' => '网络繁忙，请刷新后再试一次~');
        // }

        die(json_encode($result));
    }

    public function logout() {
        service('Passport')->logoutUser();

        redirect(U('Passport/index'));
    }
}