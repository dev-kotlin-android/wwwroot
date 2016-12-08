<?php
namespace app\common\model;

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
        return $this::create($data);
    }

    public function updateMember($data = [], $map = []) {
        return $this::isUpdate(true)->save($data, $map);
    }
}