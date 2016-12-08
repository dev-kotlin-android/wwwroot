<?php
namespace app\friends\controller;

use app\common\controller\Base;
use app\common\model\Member;

class Friend extends Base {

    /**
     * 添加好友
     * @author Steed
     * @param int $uid
     * @param string $notes
     */
    public function add(int $uid = 0, $notes = '') {
        empty($uid) && $this->result([], 1006, '缺少参数~');
        $uid == $this->member['uid'] && $this->result([], 1013, '不能添加自己为好友~');
        $member_model = new Member();
        $member_model->getMember(['uid' => $uid]) || $this->result([], 1008, '用户不存在');
        $data = [
            'uid' => $this->member['uid'],
            'friend_id' => $uid,
            'notes' => $notes,
            'is_delete' => 0,
            'timestamp' => $this->request->time()
        ];
        $friend_model = new \app\common\model\Friend();
        $where = ['uid' => $this->member['uid'], 'friend_id' => $uid];
        $friend = $friend_model->getFriend($where);
        $result = [];
        empty($friend) && $result = $friend_model->addFriend($data);
        intval($friend['is_delete'] === 0) && $this->result([], 1014, '你们已经是好友了~');
        $result = $friend_model->updateFriend($data, $where);
        $result || $this->result([], 1010, '服务器错误，请重试~');
        $this->result([], 1000, '请求成功');
    }

    /**
     * 获取好友列表
     * @author Steed
     */
    public function getFriends() {
        $friend_model = new \app\common\model\Friend();
        $where = ['uid' => $this->member['uid'], 'is_delete' => 0];
        $data = $friend_model->getList($where);
        empty($data['list']) && $this->result([], 1000, '请求成功');
        $uid = array_column($data['list'], 'friend_id');
        $where = ['uid' => ['in', $uid]];
        $fields = 'uid, username, nickname, logo, gender, profession';
        $member = Member::where($where)->column($fields);
        foreach ($data['list'] as $key => $value) {
            $data['list'][$key]['username'] = $member[$value['friend_id']]['username'];
            $data['list'][$key]['nickname'] = $member[$value['friend_id']]['nickname'];
            $data['list'][$key]['logo'] = imgUrl($member[$value['friend_id']]['logo']);
            $data['list'][$key]['gender'] = $member[$value['friend_id']]['gender'];
            $data['list'][$key]['profession'] = $member[$value['friend_id']]['profession'];
        }
        $this->result($data, 1000, '请求成功');
    }

    /**
     * 删除好友
     * @author Steed
     * @param int $uid
     */
    public function delFriend(int $uid = 0) {
        empty($uid) && $this->result([], 1006, '缺少参数');
        $friend_model = new \app\common\model\Friend();
        $where = ['uid' => $this->member['uid'], 'friend_id' => $uid, 'is_delete' => 0];
        $friend_model->getFriend($where) || $this->result([], 1015, '不存在此好友');
        $friend_model->updateFriend(['is_delete' => 1], $where) && $this->result([], 1000, '请求成功');
        $this->result([], 1010, '服务器错误，请重试');
    }
}