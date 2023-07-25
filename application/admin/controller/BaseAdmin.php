<?php
/**
 *  +----------------------------------------------------------------------
 *  | 狂神系统系统 [ WE CAN DO IT JUST THINK ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2018 http://www.iredcap.cn All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Licensed ( https://www.apache.org/licenses/LICENSE-2.0 )
 *  +----------------------------------------------------------------------
 *  | Author: Brian Waring <BrianWaring98@gmail.com>
 *  +----------------------------------------------------------------------
 */

namespace app\admin\controller;

use app\common\controller\Common;
use app\common\library\enum\CodeEnum;
use think\Db;
use think\Request;

class BaseAdmin extends Basis
{
    // 授权过的菜单列表
    protected $authMenuList     =   [];

    // 授权过的菜单url列表
    protected $authMenuUrlList  =   [];

    // 授权过的菜单树
    protected $authMenuTree     =   [];

    protected $allow_url_list   =   [
        'index',
        'login'
    ];

    // 菜单视图
    protected $menuView         =   '';

    protected $menuView1         =   '';

    public function __construct(Request $request = null)
    {
//        clear_admin_session();
        parent::__construct($request);

        // 初始化后台模块信息
        $this->initAdminInfo();







    }

    /**
     * 初始化后台模块信息
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    final private function initAdminInfo()
    {

        // 验证登录
        !is_admin_login() && $this->redirect('login/index');

        // 获取授权菜单列表
        $this->authMenuList = $this->logicAuthGroupAccess->getAuthMenuList(is_admin_login());

        // 获得权限菜单URL列表
        $this->authMenuUrlList = $this->logicAuthGroupAccess->getAuthMenuUrlList($this->authMenuList);

        // 检查菜单权限
        list($jump_type, $message) = $this->logicBaseAdmin->authCheck(
            strtolower($this->request->controller()."/".$this->request->action()),
            $this->authMenuUrlList,
            $this->allow_url_list
        );

        // 权限验证不通过则跳转提示
        CodeEnum::SUCCESS == $jump_type ?:$this->result($jump_type, $message);

        // 初始化基础数据
       $this->initBaseInfo();

    }


    /**
     * 初始化基础数据
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    final private function initBaseInfo()
    {

        // 获取过滤后的菜单树
        $this->authMenuTree = $this->logicBaseAdmin->getMenuTree($this->authMenuList, $this->authMenuUrlList);

        $daifu_on =  getAdminPayCodeSys('daifu_on', '256', is_admin_login());

        if ($daifu_on == '2'){
            foreach ($this->authMenuTree as $key => $menu){
                if ($menu['name'] == '代付管理'){
                    unset($this->authMenuTree[$key]);
                }
            }
        }

        $this->menuView1 = $this->logicMenu->menuToViewV1($this->authMenuTree);


        $admin_info =  $this->modelAdmin->find(session('admin_info')['id']);
        if ($admin_info['status'] != 1){
            clear_admin_login_session();
            $this->redirect('login/index');
        }

        // 菜单转换为视图
//        if (session('admin_info')['username'] == '演示'){
//            // 菜单转换为视图
//            $this->menuView = $this->logicMenu->menuToView($this->authMenuTree);
//        }else{
//            $this->menuView = $this->logicMenu->menuToView($this->authMenuTree, 'child',  $admin_info['google_secret_key'] ? '':'/admin/admin/blndgoogle.html');
//        }
//       security_code
        if (!$admin_info['security_code']){
            $this->menuView = $this->logicMenu->menuToView($this->authMenuTree, 'child',  $admin_info['security_code'] ? '':'/admin/admin/security_code.html');
        }else{
            $this->menuView = $this->logicMenu->menuToView($this->authMenuTree, 'child',  $admin_info['google_secret_key'] ? '':'/admin/admin/blndgoogle.html');
        }

        /*$domain =  $_SERVER['HTTP_HOST'];
        $title = Db::name('admin')->where('email',$domain)->value('nickname');
        if ($title){
            $this->assign('title',$title);
        }else{
            $title = '三方';
            $this->assign('title',$title);
        }*/
        $this->assign('title',$admin_info['nickname']);

        // 获取默认标题
        $this->title = $this->logicMenu->getDefaultTitle();

        // 设置页面标题
        $this->assign('site_title', $this->title);

        // 菜单视图
        $this->assign('menu_view', $this->menuView);
        $this->assign('menu_view1', $this->menuView1);

        // 登录会员信息
        $this->assign('admin_info', session('admin_info'));
    }
}
