<?php
namespace app\member\controller;

use app\common\controller\Base;
use app\common\controller\Upload;
use app\common\model\Friend;

class Member extends Base {
    public function _empty() {

    }

    /**
     * 获取用户信息
     * @author Steed
     * @param int $uid
     */
    public function getMember(int $uid = 0) {
        $member_model = new \app\common\model\Member();
        $where = ['uid' => $uid];
        $member = empty($uid) ? $member = $this->member : $member_model->getMember($where);
        unset($member['password']);
        unset($member['salt']);
        unset($member['status']);
        unset($member['token']);
        $member['logo'] = imgUrl($member['logo']);
        //是否是好友
        $where = ['uid' => $this->member['uid'], 'friend_id' => $uid, 'is_delete' => 0];
        $friend_model = new Friend();
        $friend = $friend_model->getFriend($where);
        $member['is_friend'] = 0;   //是否是好友
        $member['notes'] = '';      //备注
        if (!empty($friend)) {
            $member['is_friend'] = 1;
            $member['notes'] = $friend['notes'];
        }
        $this->result($member, 1000, '获取用户信息成功');
    }

    /**
     * 修改用户信息
     * @author Steed
     */
    public function updateMember() {
        $request = $this->request->param();
        (isset($request['gender']) && ($request['gender'] === '男' || $request['gender'] === '女')) && $data['gender'] = $request['gender'];
        isset($request['profession']) && $data['profession'] = $request['profession'];
        isset($request['nickname']) && $data['nickname'] = $request['nickname'];
        if(isset($request['password']) && !empty($request['password'])) {
            $data['salt'] = sha1(get_rand_char(20));
            $data['password'] = md5(sha1($request['password'] . $data['salt']));
        }
        //上传头像
        if (!empty($this->request->file('logo'))) {
            $validate = ['size' => 5242880, 'ext' => 'jpg,png,gif'];
            $savepath = ROOT_PATH . 'public' . DS . 'upload';
            $upload = new Upload($this->request->file('logo'), $savepath, $validate);
            $data['logo'] = $upload->upload();
            $data['logo'] || $this->result([], 1012, $upload->getError());
        }
        $member_model = new \app\common\model\Member();
        $where = ['uid' => $this->member['uid']];
        false === $member_model->updateMember($data, $where) && $this->result([], 1010, '服务器错误，请重试');
        $member = $member_model->getMember($where);
        unset($member['password']);
        unset($member['salt']);
        unset($member['status']);
        unset($member['token']);
        $member['logo'] = imgUrl($member['logo']);
        $this->result($member, 1000, '修改用户信息成功');
    }
}