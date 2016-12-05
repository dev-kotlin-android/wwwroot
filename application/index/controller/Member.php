<?php
namespace app\index\controller;

class Member extends Base {
    public function _empty() {

    }

    public function getMember() {
        unset($this->member['password']);
        unset($this->member['salt']);
        unset($this->member['status']);
        $this->result($this->member, 1000, '获取用户信息成功');
    }

    public function updateMember() {
        $request = $this->request->param();
        (isset($request['gender']) && ($request['gender'] === '男' || $request['gender'] === '女')) && $data['gender'] = $request['gender'];
        isset($request['profession']) && $data['profession'] = $request['profession'];
        isset($request['nickname']) && $data['nickname'] = $request['nickname'];
        if(isset($request['password']) && !empty($request['password'])) {
            $data['salt'] = sha1(get_rand_char(20));
            $data['password'] = md5(sha1($request['password'] . $data['salt']));
        }
        if (!empty($this->request->file('logo'))) {
            $file = $this->request->file('logo');
            $info = $file->move(ROOT_PATH . 'public' . DS . 'upload');
            $info || $this->result([], 1012, $file->getError());
            $data['logo'] = $info->getSaveName();
        }
        $member_model = new \app\index\model\Member();
        $where = ['uid' => $this->member['uid']];
        false === $member_model->updateMember($data, $where) && $this->result([], 1010, '服务器错误，请重试');
        $member = $member_model->getMember($where);
        unset($member['password']);
        unset($member['salt']);
        unset($member['status']);
        $this->result($member, 1000, '修改用户信息成功');
    }
}