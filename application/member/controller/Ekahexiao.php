<?php

namespace app\member\controller;

use app\common\library\enum\CodeEnum;
use app\common\logic\GoogleAuth;
use app\common\logic\MsMoneyType;
use app\common\model\Ms;
use app\ms\Logic\SecurityLogic;
use think\Cookie;
use think\Db;
class Ekahexiao extends Base
{
    public function echaka(){
        if ($this->request->isPost()){
            $insertData = $this->request->param();
            $result = $this->validate(
                $insertData,
                [
                    'merchantId' => 'require',
                    'status' => 'require',
                    'pay_code' => 'require',
                    'signkey' => 'require',
                    '__token__' => 'require|token'
                ]);
            if (true !== $result) {
                $this->error($result);
            }

            unset($insertData['__token__']);
                $is = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>1])->find();
                if (empty($is)){

                    $insertData['ms_id'] = $this->agent_id;
                    $insertData['type'] = 1;
                    $res = Db::name('jdek_sale')->insert($insertData);
                    if ($res){
                        return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '修改成功'];
                    }else{
                        return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '修改失败'];
                    }
                }else{
                    $res = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>1])->update($insertData);
                    if ($res === false){
                        return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '修改失败'];
                    }else{
                        return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '修改成功'];
                    }
                }
        }
        $data = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>1])->find();
        $this->assign('data',$data);
        return $this->fetch();
    }


    public function puxijishou(){
        if ($this->request->isPost()){
            $insertData = $this->request->param();
            $result = $this->validate(
                $insertData,
                [
                    '__token__' => 'require|token'
                ]);
            if (true !== $result) {
                $this->error($result);
            }

            unset($insertData['__token__']);
            $is = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>2])->find();
            if (empty($is)){
                $insertData['ms_id'] = $this->agent_id;
                $insertData['type'] = 2;
                $res = Db::name('jdek_sale')->insert($insertData);
                if ($res){
                    return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '修改成功'];
                }else{
                    return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '修改失败'];
                }
            }else{
                $res = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>2])->update($insertData);
                if ($res === false){
                    return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '修改失败'];
                }else{
                    return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '修改成功'];
                }
            }
        }
        $data = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>2])->find();
        $this->assign('data',$data);
        return $this->fetch();
    }


    public function puxijishouhuiyuan(){
        if ($this->request->isPost()){
            $insertData = $this->request->param();
            $result = $this->validate(
                $insertData,
                [
                    '__token__' => 'require|token'
                ]);
            if (true !== $result) {
                $this->error($result);
            }

            unset($insertData['__token__']);
            $is = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>5])->find();
            if (empty($is)){
                $insertData['ms_id'] = $this->agent_id;
                $insertData['type'] =5;
                $res = Db::name('jdek_sale')->insert($insertData);
                if ($res){
                    return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '修改成功'];
                }else{
                    return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '修改失败'];
                }
            }else{
                $res = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>5])->update($insertData);
                if ($res === false){
                    return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '修改失败'];
                }else{
                    return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '修改成功'];
                }
            }
        }
        $data = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>5])->find();
        $this->assign('data',$data);
        return $this->fetch('puxijishouhuiyuan');
    }


    public function kuke(){
        if ($this->request->isPost()){
            $insertData = $this->request->param();
            $result = $this->validate(
                $insertData,
                [
                    '__token__' => 'require|token'
                ]);
            if (true !== $result) {
                $this->error($result);
            }

            unset($insertData['__token__']);
            $is = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>3])->find();
            if (empty($is)){
                $insertData['ms_id'] = $this->agent_id;
                $insertData['type'] = 3;
                $res = Db::name('jdek_sale')->insert($insertData);
                if ($res){
                    return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '修改成功'];
                }else{
                    return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '修改失败'];
                }
            }else{
                $res = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>3])->update($insertData);
                if ($res === false){
                    return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '修改失败'];
                }else{
                    return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '修改成功'];
                }
            }
        }
        $data = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>3])->find();
        $this->assign('data',$data);
        return $this->fetch();
    }


    public function echaka_hyyf(){
        if ($this->request->isPost()){
            $insertData = $this->request->param();
            $result = $this->validate(
                $insertData,
                [
                    '__token__' => 'require|token'
                ]);
            if (true !== $result) {
                $this->error($result);
            }

            unset($insertData['__token__']);
            $is = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>4])->find();
            if (empty($is)){
                $insertData['ms_id'] = $this->agent_id;
                $insertData['type'] = 4;
                $res = Db::name('jdek_sale')->insert($insertData);
                if ($res){
                    return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '修改成功'];
                }else{
                    return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '修改失败'];
                }
            }else{
                $res = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>4])->update($insertData);
                if ($res === false){
                    return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '修改失败'];
                }else{
                    return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '修改成功'];
                }
            }
        }
        $data = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>4])->find();
        $this->assign('data',$data);
        return $this->fetch('echaka_hyyf');
    }



    public function estjunwang(){
        if ($this->request->isPost()){
            $insertData = $this->request->param();
            $result = $this->validate(
                $insertData,
                [
                    '__token__' => 'require|token'
                ]);
            if (true !== $result) {
                $this->error($result);
            }

            unset($insertData['__token__']);
            $is = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>6])->find();
            if (empty($is)){
                $insertData['ms_id'] = $this->agent_id;
                $insertData['type'] = 6;
                $res = Db::name('jdek_sale')->insert($insertData);
                if ($res){
                    return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '修改成功'];
                }else{
                    return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '修改失败'];
                }
            }else{
                $res = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>6])->update($insertData);
                if ($res === false){
                    return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '修改失败'];
                }else{
                    return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '修改成功'];
                }
            }
        }
        $data = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>6])->find();
        $this->assign('data',$data);
        return $this->fetch('estjunwang');
    }


    public function taobao188(){
        if ($this->request->isPost()){
            $insertData = $this->request->param();
            $result = $this->validate(
                $insertData,
                [
                    '__token__' => 'require|token'
                ]);
            if (true !== $result) {
                $this->error($result);
            }

            unset($insertData['__token__']);
            $is = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>7])->find();
            if (empty($is)){
                $insertData['ms_id'] = $this->agent_id;
                $insertData['type'] = 7;
                $res = Db::name('jdek_sale')->insert($insertData);
                if ($res){
                    return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '修改成功'];
                }else{
                    return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '修改失败'];
                }
            }else{
                $res = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>7])->update($insertData);
                if ($res === false){
                    return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '修改失败'];
                }else{
                    return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '修改成功'];
                }
            }
        }
        $data = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>7])->find();
        $this->assign('data',$data);
        return $this->fetch('taobao188');
    }


    public function damoxiaoka(){
        if ($this->request->isPost()){
            $insertData = $this->request->param();
            $result = $this->validate(
                $insertData,
                [
                    '__token__' => 'require|token'
                ]);
            if (true !== $result) {
                $this->error($result);
            }

            unset($insertData['__token__']);
            $is = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>8])->find();
            if (empty($is)){
                $insertData['ms_id'] = $this->agent_id;
                $insertData['type'] = 8;
                $res = Db::name('jdek_sale')->insert($insertData);
                if ($res){
                    return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '修改成功'];
                }else{
                    return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '修改失败'];
                }
            }else{
                $res = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>8])->update($insertData);
                if ($res === false){
                    return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '修改失败'];
                }else{
                    return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '修改成功'];
                }
            }
        }
        $data = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>8])->find();
        $this->assign('data',$data);
        return $this->fetch('damoxiaoka');
    }



    public function shoukacloud(){
        if ($this->request->isPost()){
            $insertData = $this->request->param();
            $result = $this->validate(
                $insertData,
                [
                    '__token__' => 'require|token'
                ]);
            if (true !== $result) {
                $this->error($result);
            }

            unset($insertData['__token__']);
            $is = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>9])->find();
            if (empty($is)){
                $insertData['ms_id'] = $this->agent_id;
                $insertData['type'] = 9;
                $res = Db::name('jdek_sale')->insert($insertData);
                if ($res){
                    return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '修改成功'];
                }else{
                    return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '修改失败'];
                }
            }else{
                $res = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>9])->update($insertData);
                if ($res === false){
                    return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '修改失败'];
                }else{
                    return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '修改成功'];
                }
            }
        }
        $data = Db::name('jdek_sale')->where(['ms_id'=>$this->agent_id,'type'=>9])->find();
        $this->assign('data',$data);
        return $this->fetch('shoukacloud');
    }

}