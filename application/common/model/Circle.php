<?php
namespace app\common\model;

use think\Model;

class Circle extends Model {
    protected $pk = 'id';

    public function getList($map = []) {
        $map['is_delete'] = 0;
        $total = $this::where($map)->count(1);
        $page = new \page($total);
        $list = $this::all(function($query) use($map, $page) {
            $query->where($map)->order('timestamp desc')->limit($page->firstRow, $page->listRows);
        });
        return pageData($page->totalPage, $total, $list);
    }

    public function addCircle($data = []) {
        return $this::create($data);
    }
}