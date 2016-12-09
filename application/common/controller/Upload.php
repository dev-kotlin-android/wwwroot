<?php
namespace app\common\controller;

class Upload extends Base {
    private $files;     //当前需要上传的文件对象

    private $file;      //当前上传对象

    private $savepath;  //需要保存的路径

    private $validate;  //验证规则

    private $error;     //错误描述

    public function __construct($files = null, $savepath = '', $validate = []) {
        parent::__construct();
        $this->files = $files;
        $this->savepath = $savepath;
        $this->validate = $validate;
    }

    public function upload() {
        return is_array($this->files) ? $this->multiple() : $this->single();
    }

    /**
     * 多文件上传
     * @author Steed
     */
    public function multiple() {
        $data = [];
        foreach ($this->files as $this->file) {
            $info = $this->file->validate($this->validate)->move($this->savepath);
            if (!$info) {
                $this->error = $this->file->getError();
                return false;
            }
            $data[] = $info->getSaveName();
        }
        return $data;
    }

    /**
     * 单文件上传
     * @author Steed
     */
    public function single() {
        $info = $this->files->validate($this->validate)->move($this->savepath);
        if (!$info) {
            $this->error = $this->files->getError();
            return false;
        }
        return $info->getSaveName();
    }

    public function getError() {
        return $this->error;
    }
}