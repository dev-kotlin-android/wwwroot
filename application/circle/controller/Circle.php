<?php
namespace app\circle\controller;

use app\common\controller\Base;
use app\common\controller\Upload;
use app\common\model\Friend;
use app\common\model\Member;

class Circle extends Base {
    /**
     * 发表朋友圈动态
     * @author Steed
     * @param string $title
     * @param string $content
     * @param int $type
     */
    public function release($title = '', $content = '', int $type = 0) {
        empty($type) && $this->result([], 1006, '缺少必要参数');
        $data = [
            'uid' => $this->member['uid'],
            'title' => $title,
            'content' => $content,
            'type' => $type,
            'timestamp' => $this->request->time()
        ];
        //上传文件
        if (!empty($this->request->file('img'))) {
            $validate = ['size' => 5242880, 'ext' => 'jpg,png,gif'];
            $savepath = ROOT_PATH . 'public' . DS . 'upload';
            $upload = new Upload($this->request->file('img'), $savepath, $validate);
            $data['img'] = $upload->upload();
            $data['img'] || $this->result([], 1012, $upload->getError());
            $data['img'] = json_encode($data['img']);
        }

        $circle_model = new \app\common\model\Circle();
        $circle = $circle_model->addCircle($data);
        $circle && $this->result($circle, 1000, '请求成功');
        $this->result([], 1010, '服务器错误，请重试');
    }

    /**
     * 获取朋友圈动态
     * @author Steed
     * @param int $uid
     */
    public function getCircle(int $uid = 0) {
        $data = empty($uid) ? $this->multiple() : $this->getSingle($uid);
        $this->result($data, 1000, '请求成功');
    }

    /**
     * 获取单个人的动态
     * @author Steed
     * @param $uid
     * @return array
     */
    private function getSingle($uid) {
        $where = ['uid' => $uid, 'is_delete' => 0];
        $circle_model = new \app\common\model\Circle();
        $data = $circle_model->getList($where);
        $member_model = new Member();
        $member = $member_model->getMember(['uid' => $uid]);
        $friend_model = new Friend();
        $friend = $friend_model->getFriend(['uid' => $this->member['uid'], 'friend_id' => $uid]);
        $member['notes'] = $friend['notes'];
        foreach ($data['list'] as $key => $value) {
            $data['list'][$key]['username'] = $member['username'];
            $data['list'][$key]['nickname'] = $member['nickname'];
            $data['list'][$key]['logo'] = $member['logo'];
            $data['list'][$key]['notes'] = $member['notes'];
        }
        return $data;
    }

    /**
     * 获取好友的动态
     * @author Steed
     * @return array
     */
    private function multiple() {
        //获取所有的好友
        $where = ['uid' => $this->member['uid'], 'is_delete' => 0];
        $friends = Friend::where($where)->column('friend_id, notes');
        //将自己加入好友中
        $friends[$this->member['uid']] = '';
        $friend_id = array_keys($friends);
        //获取动态数据
        $where = ['uid' => ['in', $friend_id]];
        $circle_model = new \app\common\model\Circle();
        $data = $circle_model->getList($where);
        $member = Member::where($where)->column('uid, username, nickname, logo');
        //构造用户数据
        foreach ($member as $key => $value) {
            $member[$key]['notes'] = $friends[$key];
        }
        //构造动态用户数据
        foreach ($data['list'] as $key => $value) {
            $data['list'][$key]['username'] = $member[$data['list'][$key]['uid']]['username'];
            $data['list'][$key]['nickname'] = $member[$data['list'][$key]['uid']]['nickname'];
            $data['list'][$key]['logo'] = $member[$data['list'][$key]['uid']]['logo'];
            $data['list'][$key]['notes'] = $member[$data['list'][$key]['uid']]['notes'];
        }
        return $data;
    }
}