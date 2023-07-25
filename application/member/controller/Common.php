<?php
/**
 * Created by PhpStorm.
 * User: zhangxiaohei
 * Date: 2019/12/5
 * Time: 23:04
 */

namespace app\member\controller;


use app\admin\model\AreaModel;
use app\common\library\enum\CodeEnum;
use app\index\logic\SysToolLogic;
use app\index\model\ConfigModel;
use think\Db;

class Common extends Base
{


    public function upload()
    {
        if($this->request->isPost()) {
            $this->result($this->logicFile->fileUpload('file',$this->request->param('path_name', '') ?? 'logo'));
        }
    }


    public function upload_img()
    {
        if($this->request->isPost()) {
            $this->result($this->logicFile->picUpload('file',$this->request->param('path_name', '') ?? 'logo'));
        }
    }


    /**
     * 获取下级
     */
/*    public function getArea(){
        $id = $this->request->param('id','0');
        $AreaModel = new AreaModel();
        $result = $AreaModel->getArea($id);
        $this->success('请求成功',null,$result);
    }
*/


    /**
     * 上传
     */
    public function uploads()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = $this->request->file('file');
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->validate(['size'=>1048576,'ext'=>'jpg,png,jpeg','image' => 'require|image'])->move(ROOT_PATH . 'public' . DS . 'uploads'. DS .'payImg');
            if($info){
                $path=str_replace('\\','/', '/public' . DS . 'uploads'. DS .'payImg/'.$info->getSaveName());
                return json(['code'=>0,'msg'=>'上传成功','data'=>['src'=>$path]]);
            }else{
                // 上传失败获取错误信息
//                echo $file->getError();
                return json(['code'=>1,'msg'=>$file->getError()]);
            }
        }
    }

}
