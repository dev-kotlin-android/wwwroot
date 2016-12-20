<?php
namespace app\index\controller;

use app\common\controller\Base;
use app\common\model\Member;

class Index extends Base {
    protected $is_login = false;

    public function _empty() {

    }

    public function register($username = '', $password = '') {
        (empty($username) || empty($password)) && $this->result([], 1006, '参数缺失');
        $member_model = new Member();
        $where = ['username' => $username];
        $member_model->getMember($where) && $this->result([], 1007, '用户名已存在');
        $data = ['username' => $username];
        $data['reg_time'] = $this->request->time();
        //生成密码
        $data['salt'] = sha1(get_rand_char(20));
        $data['password'] = md5(sha1($password . $data['salt']));
        //生成token
        $token = $this->request->time() . $data['salt'];
        $data['token'] = sha1(md5($token));
        $member = $member_model->addMember($data);
        $member && $this->result(['uid' => $member['uid'], 'token' => $member['token']], 1000, '注册成功');
        $this->result([], 1010, '服务器错误，请重试');
    }

    public function login($username = '', $password = '') {
        (empty($username) || empty($password)) && $this->result([], 1006, '参数缺失');
        $member_model = new Member();
        $where = ['username' => $username];
        $member = $member_model->getMember($where);
        $member || $this->result([], 1008, '用户名不存在');
        intval($member['status']) === 1 && $this->result([], 1009, '用户已被禁用');
        md5(sha1($password . $member['salt'])) !== $member['password'] && $this->result([], 1011, '密码错误');
        //更新token
        $token = sha1($this->request->time() . get_rand_char(20));
        false === $member_model->updateMember(['token' => $token], ['uid' => $member['uid']]) && $this->result([], 1010, '服务器错误，请重试');
        $member['token'] = $token;
        $member['logo'] = imgUrl($member['logo']);
        unset($member['password']);
        unset($member['salt']);
        unset($member['status']);
        $this->result($member, 1000, '登录成功');
    }
}
