<?php
namespace app\index\model;

use think\Model;

class Member extends Model {
    protected $pk = 'uid';

    public function getMember($map = []) {
        $map['status'] = 0;
        $member = $this::get(function($query) use($map) {
            $query->where($map);
        });
        return $member;
    }

    public function addMember($data = []) {
        $result = $this::isUpdate(false)->save($data);
        if ($result) return $this->data['uid'];
        return $result;
    }

    public function updateMember($data = [], $map = []) {
        return $this::isUpdate(true)->save($data, $map);
    }
}