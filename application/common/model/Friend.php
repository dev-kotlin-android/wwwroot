<?php
namespace app\common\model;

use think\Model;

class Friend extends Model {
    protected $pk = 'id';

    public function getList($map = []) {
        $total = $this::where($map)->count(1);
        $page = new \page($total);
        $list = $this::all(function($query) use($map, $page) {
            $query->where($map)->limit($page->firstRow, $page->listRows);
        });
        return pageData($page->totalPage, $total, $list);
    }

    public function getFriend($map = []) {
        return $this::get(function($query) use($map) {
            $query->where($map);
        });
    }

    public function addFriend($data = []) {
        return $this::create($data);
    }

    public function updateFriend($data = [], $map = []) {
        return $this::update($data, $map);
    }
}