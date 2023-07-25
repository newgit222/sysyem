<?php
/**
 * Created by PhpStorm.
 * User: zhangxiaohei
 * Date: 2020/2/7
 * Time: 22:19
 */

namespace app\index\controller;


use app\common\library\enum\CodeEnum;
use app\common\logic\Log;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use think\Db;
use think\Exception;
use think\Loader;

class DaifuOrder extends Base
{


    /**
     * @return mixed
     *  代付订单列表
     */
    public function index()
    {
        $where = ['uid' => is_login()];
        //组合搜索
//print_r($this->request->get('d/a'));die;

        !empty($this->request->get('channel')) && $where['channel']
            = ['eq', $this->request->get('channel')];

        //时间搜索  时间戳搜素
        $date = $this->request->param('d/a');

        $start = empty($date['start']) ? date('Y-m-d 00:00:00', time()) : $date['start'];
        $end = empty($date['end']) ? date('Y-m-d 23:59:59', time()) : $date['end'];
        $where['update_time'] = ['between', [strtotime($start), strtotime($end)]];
        //状态
        if (!empty($this->request->get('status')) || $this->request->get('status') === '0') {
            $where['status'] = $this->request->get('status');
        }

        if (!empty($date['amount1']) && !empty($date['amount2'])){
            $where['amount'] = ['between',[$date['amount1'],$date['amount2']]];
        }

//        print_r($where);die;

        if (!empty($this->request->get('trade_no'))){
            unset($where['update_time']);
            $where['out_trade_no'] = ['like', '%' . $this->request->get('trade_no') . '%'];
        }

        if (!empty($this->request->get('bank_owner'))) {
            unset($where['update_time']);
            $where['bank_owner'] = $this->request->get('bank_owner');
        }


        if (!empty($this->request->get('bank_number'))) {
            unset($where['update_time']);
            $where['bank_number'] = $this->request->get('bank_number');
        }
//        print_r($where);


//        var_dump($where);die();
        $orderLists = $this->logicDaifuOrders->getOrderList($where, true, 'create_time desc', 10);
        //查询当前符合条件的订单的的总金额  编辑封闭 新增放开 原则
        $cals = $this->logicDaifuOrders->calOrdersData($where);

        $admin_id = Db::name('user')->where('uid',is_login())->value('admin_id');
        $daifu_success_uplode = Db::name('config')->where(['name'=>'daifu_success_uplode','admin_id'=>$admin_id])->value('value');
        if (!empty($daifu_success_uplode) && $daifu_success_uplode == 2){
            $this->assign('daifu_chars',$daifu_success_uplode);
        }else{
            $this->assign('daifu_chars',1);
        }
        $this->assign('list', $orderLists);
        $this->assign('cal', $cals);
        $this->assign('code', []);//$this->logicDaifuOrders->getCodeList([]));
        $this->assign('start', $start);
        $this->assign('end', $end);
        return $this->fetch();
    }


    /**
     * 申请代付
     */
    public function apply()
    {
        //用户信息
        $userInfo = $this->logicUser->getUserInfo(['uid' => session('user_info.uid')]);

        //google验证其二维码
        require_once EXTEND_PATH . 'PHPGangsta/GoogleAuthenticator.php';
        $ga = new \PHPGangsta_GoogleAuthenticator();
        $where = ['uid' => is_login()];
        if ($this->request->isPost()) {
            if ($userInfo['is_need_google_verify'] && $userInfo['google_secret_key']) {
                //google身份验证
                $code = input('b.google_code');
                $secret = session('google_secret');
                $checkResult = $ga->verifyCode($secret, $code, 1);
                if ($checkResult == false) {
                    $this->result(0, 'google身份验证失败 ！！！');
                }
            }
            //校验令牌
            $token = input('__token__');
            if(session('__token__')!= $token){
                $this->result(0,'请不要重复发起代付,请刷新页面重试 ！！！');
            }
            session('__token__', null);
            $userDfIp = require('./data/conf/userDfip.php');
            //校验是否允许发起代付从前端
            if ($userInfo->is_can_df_from_index != 1 || !in_array(is_login(),$userDfIp['userid'])) {
             //   $this->result(0, '您不允许在前端发起代付申请 ！！！');
            }

            if ($this->request->post('b/a')['uid'] == is_login()) {
                $params['uid'] = $this->request->post('b/a')['uid'];
                $params['body'] = $this->request->post('body');
                $params['scene'] = $this->request->post('scene');

                $this->result($this->logicDaifuOrders->easyCreateOrder($params, $userInfo));
            } else {
                $this->result(0, '非法操作，请重试！');
            }
        }
        //详情
        $this->common($where);
        //收款账户

        $this->assign('user', $userInfo);
        //银行
        $this->assign('banker', $this->logicBanker->getBankerList());

        if ($userInfo['is_need_google_verify'] && $userInfo['google_secret_key']){
            session('google_secret', $userInfo['google_secret_key']);
//            $this->assign('google_qr', $ga->getQRCodeGoogleUrl($userInfo['account'], $userInfo['google_secret_key']));
        }

        return $this->fetch();
    }

    /**
     * @user luomu
     * @return
     * @time
     * 批量提交代付
     */
//    public function batch_apply_model(){
//
//        /*$spreadsheet = new Spreadsheet();
//        $sheet       = $spreadsheet->getActiveSheet();
//        $sheet->setCellValueByColumnAndRow(1, 1, '业务')->getColumnDimension('A')->setWidth(15);
//        $sheet->setCellValueByColumnAndRow(2, 1, '外勤')->getColumnDimension('B')->setWidth(15);
//        $sheet->setCellValueByColumnAndRow(3, 1, '接单时间')->getColumnDimension('C')->setWidth(15);
//        $sheet->setCellValueByColumnAndRow(4, 1, '开始办理时间')->getColumnDimension('D')->setWidth(15);
//        $sheet->setCellValueByColumnAndRow(5, 1, '状态修改时间')->getColumnDimension('E')->setWidth(15);
//
//
//        $file = 'batch_apply_model.xlsx';
//        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//        header('Content-Disposition: attachment;filename="' . $file . '"');
//        header('Cache-Control: max-age=0');//禁止缓存
//
//        $writer = new Xls($spreadsheet);
//        ob_start(); //打开缓冲区
//        $writer->save('php://output');
//        ob_get_contents();
//        ob_end_clean();*/
//
//
//        $spreadsheet = new Spreadsheet();
//
//        $sheet = $spreadsheet->getActiveSheet();
//
//
//        $sheet->setCellValue('A1', '代付银行');
//        $sheet->setCellValue('B1', '银行卡号');
//        $sheet->setCellValue('C1', '真实姓名');
//        $sheet->setCellValue('D1', '代付金额');
//
//        // Write an .xlsx file
//        $date = date('d-m-y-'.substr((string)microtime(), 1, 8));
//        $date = str_replace(".", "", $date);
//        $file = "export_".$date.".xls";
//
//        header('Content-Type: application/vnd.ms-excel');
//
//        header('Content-Disposition: attachment;filename="' . $file . '"');
//        header('Cache-Control: max-age=0');
//
//        $objWriter = IOFactory::createWriter($spreadsheet, 'Xls');
//        $objWriter->save('php://output');
//        exit;
//
//
//
//        /*$strTable = '<table width="500" border="1">';
//        $strTable .= '<tr>';
//        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">代付银行</td>';
//        $strTable .= '<td style="text-align:center;font-size:12px;" width="100">银行卡号</td>';
//        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">真实姓名</td>';
//        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">代付金额</td>';
//        $strTable .= '</tr>';
//        for ($i=0;$i<200;$i++){
//            $strTable .= '<tr>';
//            $strTable .= '<td style="text-align:center;font-size:12px;width:120px;"></td>';
//            $strTable .= '<td style="text-align:center;font-size:12px;" width="100"></td>';
//            $strTable .= '<td style="text-align:center;font-size:12px;" width="*"></td>';
//            $strTable .= '<td style="text-align:center;font-size:12px;" width="*"></td>';
//            $strTable .= '</tr>';
//        }
//        $strTable .= '</table>';
//        downloadExcel($strTable, 'batch_apply_model');*/
//    }


    /**
     * Common
     *
     * @param array $where
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function common($where = [])
    {
        //资产信息
        $this->assign('info', $this->logicBalance->getBalanceInfo($where));
        //银行
        $this->assign('banker', $this->logicBanker->getBankerList());

    }


    /**
     * 导出订单
     */
    public function exportOrder()
    {
        $where = ['uid' => is_login()];
        //组合搜索
        !empty($this->request->get('trade_no')) && $where['out_trade_no']
            = ['like', '%' . $this->request->get('trade_no') . '%'];

        !empty($this->request->get('channel')) && $where['channel']
            = ['eq', $this->request->get('channel')];

        //时间搜索  时间戳搜素
        $date = $this->request->param('d/a');

        $start = empty($date['start']) ? date('Y-m-d', time()) : $date['start'];
        $end = empty($date['end']) ? date('Y-m-d', time() + 3600 * 24) : $date['end'];
        $where['create_time'] = ['between', [strtotime($start), strtotime($end)]];
        //状态
        if (!empty($this->request->get('status')) || $this->request->get('status') === '0') {
            $where['status'] = $this->request->get('status');
        }

        if (!empty($this->request->get('bank_owner'))) {
//            unset($where['create_time']);
            $where['bank_owner'] = $this->request->get('bank_owner');
        }


        if (!empty($this->request->get('bank_number'))) {
//            unset($where['create_time']);
            $where['bank_number'] = $this->request->get('bank_number');
        }
        //导出默认为选择项所有
        $orderList = $this->logicDaifuOrders->getOrderList($where, true, 'create_time desc', false);

        //组装header 响应html为execl 感觉比PHPExcel类更快
        $orderStatus = ['订单关闭', '等待支付', '支付完成', '异常订单'];
        $orderCzStatus = ['','冲正'];
        $strTable = '<table width="500" border="1">';
        $strTable .= '<tr>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">ID标识</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="100">订单号</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="100">金额</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="100">手续费</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">姓名</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">收款银行</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">卡号</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">创建时间</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">更新时间</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">状态</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">冲正状态</td>';
        $strTable .= '</tr>';
        if (is_array($orderList)) {
            foreach ($orderList as $k => $val) {
                $strTable .= '<tr>';
                $strTable .= '<td style="text-align:center;font-size:12px;" >&nbsp;' . $val['id'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;mso-number-format:\'@\';" x:str="' . $val['out_trade_no'] . '">' . $val['out_trade_no'] . ' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . sprintf("%.2f",$val['amount']) . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . sprintf("%.2f",$val['single_service_charge']) . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['bank_owner'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['bank_name'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;mso-number-format:\'@\';" x:str="' . $val['bank_number'] . '">'.$val['bank_number'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['create_time'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['update_time'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $orderStatus[$val['status']] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $orderCzStatus[$val['chongzhen']] . '</td>';
                $strTable .= '</tr>';
                unset($orderList[$k]);
            }
        }
        $strTable .= '</table>';
        downloadExcel($strTable, 'daifuorder');
    }

//    public function uploadfile(){
//
//        $filePath = $this->request->param('file_path');
//
//        $filePath =   openssl_decrypt(base64_decode($filePath), "AES-128-CBC", '8e70f72bc3f53b12', true, '99b538370c7729c7');
//
//            //实例化reader
//            $ext = pathinfo($filePath, PATHINFO_EXTENSION);
//
//            if (!in_array($ext, ['csv', 'xls', 'xlsx'])) {
//                $this->error('未知的数据格式');
//            }
//            if ($ext === 'csv') {
//                $file = fopen($filePath, 'r');
//                $filePath = tempnam(sys_get_temp_dir(), 'import_csv');
//                $fp = fopen($filePath, 'w');
//                $n = 0;
//                while ($line = fgets($file)) {
//                    $line = rtrim($line, "\n\r\0");
//                    $encoding = mb_detect_encoding($line, ['utf-8', 'gbk', 'latin1', 'big5']);
//                    if ($encoding !== 'utf-8') {
//                        $line = mb_convert_encoding($line, 'utf-8', $encoding);
//                    }
//                    if ($n == 0 || preg_match('/^".*"$/', $line)) {
//                        fwrite($fp, $line . "\n");
//                    } else {
//                        fwrite($fp, '"' . str_replace(['"', ','], ['""', '","'], $line) . "\"\n");
//                    }
//                    $n++;
//                }
//                fclose($file) || fclose($fp);
//                Loader::import('PhpOffice.PhpSpreadsheet.Reader.Csv');
//                $reader = new Csv();
//            } elseif ($ext === 'xls') {
//                Loader::import('PhpOffice.PhpSpreadsheet.Reader.Xls');
//                $reader = new Xls();
//            } else {
//                Loader::import('PhpOffice.PhpSpreadsheet.Reader.Xlsx');
//                $reader = new Xlsx();
//            }
//
//            //加载文件
//            $insert = [];
//            try {
//                if (!$PHPExcel = $reader->load($filePath)) {
//                    $this->error(__('Unknown data format'));
//                }
//
//                $currentSheet = $PHPExcel->getSheet(0);  //读取文件中的第一个工作表
//                $allColumn = $currentSheet->getHighestDataColumn(); //取得最大的列号
//                $allRow = $currentSheet->getHighestRow(); //取得一共有多少行
//                $maxColumnNumber = Coordinate::columnIndexFromString($allColumn);
//
//                $fields = [];
//                for ($currentRow = 1; $currentRow <= 1; $currentRow++) {
//                    for ($currentColumn = 1; $currentColumn <= $maxColumnNumber; $currentColumn++) {
//                        $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
//                        $fields[] = $val;
//                    }
//                }
//
//                for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
//                    $values = [];
//                    for ($currentColumn = 1; $currentColumn <= $maxColumnNumber; $currentColumn++) {
//                        $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
//                        $values[] = is_null($val) ? '' : $val;
//                    }
//                    $row = [];
//                    $temp = array_combine($fields, $values);
//                    $fieldArr = array(
//                        '代付银行' => 'bank_code',
//                        '代付金额' => 'amount',
//                        '银行卡号' => 'bank_number',
//                        '真实姓名' => 'bank_owner',
//                    );
//                    foreach ($temp as $k => $v) {
//                        if (isset($fieldArr[$k]) && $k !== '') {
//                            $row[$fieldArr[$k]] = $v;
//                        }
//                    }
//                    $row['body'] = '批量代付申请';
//                    if ($row) {
//                        $insert[] = $row;
//                    }
//                }
//            } catch (Exception $exception) {
//                $this->error($exception->getMessage());
//            }
//            if (!$insert) {
//                $this->error('没有上传可用数据');
//            }
//
//        //用户信息
//        $userInfo = $this->logicUser->getUserInfo(['uid' => session('user_info.uid')]);
//
//        //google验证其二维码
//        require_once EXTEND_PATH . 'PHPGangsta/GoogleAuthenticator.php';
//        $ga = new \PHPGangsta_GoogleAuthenticator();
//        $where = ['uid' => is_login()];
//        if ($this->request->isPost()) {
//            if ($userInfo['is_need_google_verify'] && $userInfo['google_secret_key']) {
//                //google身份验证
//                $code = input('google_code');
//                $secret = session('google_secret');
//                $checkResult = $ga->verifyCode($secret, $code, 1);
//                if ($checkResult == false) {
//                    $this->result(0, 'google身份验证失败 ！！！');
//                }
//            }
//            //校验令牌
//            $token = input('__token__');
//            if(session('__token__')!= $token){
//                $this->result(0,'请不要重复发起代付,请刷新页面重试 ！！！');
//            }
//            session('__token__', null);
//            $userDfIp = require('./data/conf/userDfip.php');
//            //校验是否允许发起代付从前端
//            if ($userInfo->is_can_df_from_index != 1 || !in_array(is_login(), $userDfIp['userid'])) {
//              //  $this->result(0, '您不允许在前端发起代付申请 ！！！');
//            }
//
//            if ($this->request->post('uid') == is_login()) {
//                Db::startTrans();
//                try {
//                    foreach ($insert as $item){
//                        $ret = $this->logicDaifuOrders->manualCreateOrder($item, $userInfo);
//                        if ($ret['code'] == CodeEnum::ERROR){
//                            throw new Exception($ret['msg']);
//                        }
//                    }
//                    Db::commit();
//                    $this->success('操作成功');
//                }catch (Exception $e){
//                    Db::rollback();
//                    \think\Log::error('批量代付error:' . $e->getMessage());
//                    $this->error($e->getMessage());
//                }
//            } else {
//                $this->result(0, '非法操作，请重试！');
//            }
//        }
//    }
}
