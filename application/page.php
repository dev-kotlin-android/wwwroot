<?php
class page {
    public $totalRows;//总条数

    public $listRows = 20;//每页显示的条数

    public $firstRow = 0;//开始条数

    public $nowPage = 1;//当前页数

    public $totalPage = 0;//总页数

    public function __construct($totalRows, $listRows = ''){
        $this->totalRows = $totalRows;
        $this->listRows = empty($listRows) ? \think\Request::instance()->param('rows', 20, 'intval') : $listRows;
        $this->totalPage = ceil($this->totalRows / $this->listRows);
        $this->nowPage = \think\Request::instance()->param('page', 1, 'intval');
        $this->firstRow = ($this->nowPage - 1) * $this->listRows;
    }
}
