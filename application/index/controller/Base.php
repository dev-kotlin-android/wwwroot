<?php
namespace app\index\controller;

use app\index\model\Member;
use think\Config;
use think\Controller;

class Base extends Controller {
    //是否检测登录
    protected $is_login = true;
    //当前用户
    protected $member;

    public function __construct() {
        parent::__construct();
        $this->request->isPost() || $this->result([], 1001, '非法请求');
        //验证token
        $this->is_login && $this->checkToken();
        $this->checkSign();
    }

    /**
     * 验证token
     * @author Steed
     */
    protected function checkToken() {
        $header = $this->request->header();
        (!isset($header['uid']) || empty($header['uid'])) && $this->result([], 1002, '用户错误');
        (!isset($header['token']) || empty($header['token'])) && $this->result([], 1003, 'token错误');
        $where = ['uid' => $header['uid']];
        $member_model = new Member();
        $member = $member_model->getMember($where);
        empty($member) && $this->result([], 1002, '用户错误');
        $header['token'] !== $member['token'] && $this->result([], 1003, 'token错误');
        $member['logo'] = '/public/upload/' . $member['logo'];
        $this->member = $member;
    }

    /**
     * 验证签名
     * @author Steed
     */
    protected function checkSign() {
        $header = $this->request->header();
        (!isset($header['sign']) || empty($header['sign'])) && $this->result([], 1004, '签名错误');
        (!isset($header['time']) || empty($header['time'])) && $this->result([], 1005, '时间错误');
        $data = ['time' => $header['time']];
        $data['key'] = Config::get('sign_key');
        ksort($data);
        $sign = sha1(http_build_query($data));
        $sign !== $header['sign'] && $this->result([], 1004, '签名错误');
    }
}