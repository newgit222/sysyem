<?php

namespace app\admin\controller;
use app\common\library\enum\CodeEnum;
use think\Cache;
use think\Config;
use think\Db;
use think\Exception;
use think\Log;
use think\Request;

class Manage extends BaseAdmin
{
    public function weblist(){
        return $this->fetch();
    }


    public function getWebList(){
        $where = [];

        !empty($this->request->param('web_url')) && $where['web_url']
            = ['like', '%'.$this->request->param('web_url').'%'];


        $data = Db::name('web_site')
                ->where($where)
                ->paginate($this->request->param('limit', 10));


        $this->result($data || !empty($data) ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg'=> '',
                'count'=>$data->total(),
                'data'=>$data->items()
            ] : [
                'code' => CodeEnum::ERROR,
                'msg'=> '暂无数据',
                'count'=>$data->total(),
                'data'=>$data->items()
            ]
        );
    }


    public function webAdd(){
        if ($this->request->isPost()){
            $data = $this->request->post();
//            print_r($data);die;
            $res = Db::name('web_site')->insert($data);
            return [ 'code' => CodeEnum::SUCCESS,'msg' =>   '添加成功'];
        }
        return $this->fetch('web_add');
    }


    public function userList(Request $request){
        return $this->fetch('userList');
    }


    public function getUserList(Request $request){

        $where = [];
//        print_r($request->param());die;
        //时间搜索  时间戳搜素
        $where['a.addtime'] = $this->parseRequestDate();

        $this->request->param('username') && $where['a.username']
            = $this->request->param('username');

        $this->request->param('jl_class') && $where['a.jl_class']
            = $this->request->param('jl_class');


        $data = $this->modelAdminBill
            ->alias('a')
            ->where($where)
            ->order('addtime desc')
            ->paginate($this->request->param('limit'));

        $this->result($data->total() ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg'=> '',
                'count'=>$data->total(),
                'data'=>$data->items()
            ] : [
                'code' => CodeEnum::ERROR,
                'msg'=> '暂无数据',
                'count'=>$data->total(),
                'data'=>$data->items()
            ]
        );



    }


    public function changebalance(Request $request){

        if ($this->request->isPost()){
            $datas = $this->request->post();

            $result = $this->validate(
                [
                    '__token__' => $this->request->post('__token__'),
                ],
                [
                    '__token__' => 'require|token'
                ]);

            if (true !== $result) {
                $this->error($result);
            }

            $result = $this->validate(
                [
                    'web_url' => $this->request->post('web_url'),
                    'username' => $this->request->post('username'),
                ],
                [
                    'web_url' => 'require|url',
                    'username' => 'require'
                ]);

            if (true !== $result) {
                $this->error($result);
            }

            $data['username'] = $datas['username'];

            $parsed_url = parse_url($datas['web_url']);
            $apiurl = $parsed_url['host'];
            $weburl = 'http://'.$apiurl.'/manage/admin/adminInfo';

            $adminInfo = json_decode($this->curl($weburl,$data),true);
            if (!$adminInfo){
                $this->error('请求超时，请检查网址是否正确');
            }
            if ($adminInfo['code'] != 200){
                $this->error($adminInfo['msg']);
            }
            $adminInfo = $adminInfo['data'];
            $datas['id'] = $adminInfo['id'];
//            print_r($adminInfo);die;
            $changebalance = 'http://'.$apiurl.'/manage/admin/changeBalance';
            $res = json_decode($this->curl($changebalance,$datas),true);
            if ($res['code'] == 200){
                $this->logicAdminBill->addBill($adminInfo['id'], 2, $datas['op_type'], $datas['money'], $datas['opInfo'],$adminInfo['username'],$datas['web_url'],$adminInfo['balance']);
                $this->success($res['msg']);
            }
            $this->error($res['msg']);
        }
    }



    public function clearLoginLimit(Request $request){
        if ($this->request->isPost()){
            $datas = $this->request->post();

            $result = $this->validate(
                [
                    '__token__' => $this->request->post('__token__'),
                ],
                [
                    '__token__' => 'require|token'
                ]);

            if (true !== $result) {
                $this->error($result);
            }

            $result = $this->validate(
                [
                    'web_url' => $this->request->post('web_url'),
                    'username' => $this->request->post('username'),
                ],
                [
                    'web_url' => 'require|url',
                    'username' => 'require'
                ]);

            if (true !== $result) {
                $this->error($result);
            }

            $data['username'] = $datas['username'];

            $parsed_url = parse_url($datas['web_url']);
            $apiurl = $parsed_url['host'];
            $weburl = 'http://'.$apiurl.'/manage/admin/adminInfo';
            $adminInfo = json_decode($this->curl($weburl,$data),true);

            if (!$adminInfo){
                $this->error('请求超时，请检查网址是否正确');
            }
            if ($adminInfo['code'] != 200){
                $this->error($adminInfo['msg']);
            }
            $adminInfo = $adminInfo['data'];
            $datas['admin_id'] = $adminInfo['id'];
//            print_r($adminInfo);die;
            $weburl = 'http://'.$apiurl.'/manage/admin/clearLoginLimit';
            $res = json_decode($this->curl($weburl,$datas),true);
            if ($res['code'] == 1){
                return json(['code' => 1,  'msg' => '清除成功' ]);
            }
            return json(['code' => 0,  'msg' => '清除失败' ]);
        }



    }


    public function record(Request $request){
        $this->assign('id',$request->param('id'));
        return $this->fetch();
    }

    public function getBillList(Request $request)
    {
        $where = [];
//        print_r($request->param());die;
//        $where['a.pt_id'] = $request->param('id');
        //时间搜索  时间戳搜素
        $where['a.addtime'] = $this->parseRequestDate();

        $this->request->param('username') && $where['a.username']
            = $this->request->param('username');

        !empty($this->request->param('web_url')) && $where['a.web_url']
            = ['like', '%'.$this->request->param('web_url').'%'];

        $this->request->param('jl_class') && $where['a.jl_class']
            = $this->request->param('jl_class');


        $data = $this->modelAdminBill
            ->alias('a')
            ->where($where)
            ->order('addtime desc')
            ->paginate($this->request->param('limit'));

        $this->result($data->total() ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg'=> '',
                'count'=>$data->total(),
                'data'=>$data->items()
            ] : [
                'code' => CodeEnum::ERROR,
                'msg'=> '暂无数据',
                'count'=>$data->total(),
                'data'=>$data->items()
            ]
        );
    }


    private function curl($url,array $data)
    {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

}