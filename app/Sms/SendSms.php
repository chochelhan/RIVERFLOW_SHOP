<?php

namespace App\Sms;

use Illuminate\Support\Facades\DB;

class SendSms
{


    public function send(array $params)
    {

        if(!$params['to'] || !$params['type']) {
            return 'emptyField';
        }
        $pcs = $params['to'];
        $gid = $params['type'];

        $tblName = config('tables.smsEmailSetting');
        $baseInfo = DB::table($tblName)->where('gtype','sms')->where('gid','base')->first();
        if($baseInfo && $baseInfo->content) {
            $info = json_decode($baseInfo->content);
            $sendParams['smsId']= $info->smsId;
            $sendParams['authkey']= $info->authkey;
            $sendParams['sendPcs']= $info->sendPcs;
            $sendParams['pcs']= $pcs;

            if($sendParams['smsId'] && $sendParams['authkey'] && $sendParams['sendPcs']) {
                $row = DB::table($tblName)->where('gtype','sms')->where('gid',$gid)->first();
                if($row && $row->content && $row->guse=='yes') {
                    $contentInfo = json_decode($row->content);
                    $messageInfo = $this->setMessage(strip_tags($contentInfo->content),$params);
                    $sendParams['message'] = $messageInfo['message'];
                    $sendParams['subject'] = $messageInfo['subject'];

                    return $this->smsAction($sendParams);
                } else {
                     return 'emptyField';
                }
            } else {
                 return 'emptyField';
            }
        } else {
             return 'emptyField';
        }
    }
    private function setMessage(string $message, array $params) {

        $userName = (empty($params['userName']))?'':$params['userName'];
        $userId = (empty($params['userId']))?'':$params['userId'];

        switch($params['type']) {
            case 'joinAuth':
                $subject = '인증번호';
                if(!empty($params['authNumber']))$message = str_replace('{{authNumber}}',$params['authNumber'], $message);
            break;
            case 'join':
                $subject = '회원가입';
                if(!empty($params['regDate']))$message = str_replace('{{regDate}}',$params['regDate'], $message);
                if($userName)$message = str_replace('{{userName}}',$userName, $message);
                if($userId)$message = str_replace('{{userId}}',$userId, $message);
            break;
            case 'notpay':
                $subject = '입금안내문자';
                if(!empty($params['orderDate']))$message = str_replace('{{orderDate}}',$params['orderDate'], $message);
                if($userName)$message = str_replace('{{userName}}',$userName, $message);
                if($userId)$message = str_replace('{{userId}}',$userId, $message);
                if(!empty($params['orderInfo']))$message = str_replace('{{orderInfo}}',$params['orderInfo'], $message);
                if(!empty($params['paymentInfo']))$message = str_replace('{{paymentInfo}}',$params['paymentInfo'], $message);
                if(!empty($params['bankExpire']))$message = str_replace('{{bankExpire}}',$params['bankExpire'], $message);
                if(!empty($params['accountInfo']))$message = str_replace('{{accountInfo}}',$params['accountInfo'], $message);
            break;
            case 'income':
                $subject = '입금완료문자';
                if(!empty($params['orderDate']))$message = str_replace('{{orderDate}}',$params['orderDate'], $message);
                if($userName)$message = str_replace('{{userName}}',$userName, $message);
                if(!empty($params['orderInfo']))$message = str_replace('{{orderInfo}}',$params['orderInfo'], $message);
                if(!empty($params['paymentInfo']))$message = str_replace('{{paymentInfo}}',$params['paymentInfo'], $message);
            break;
            case 'delivery':
                $subject = '상품발송문자';
                if(!empty($params['orderDate']))$message = str_replace('{{orderDate}}',$params['orderDate'], $message);
                if($userName)$message = str_replace('{{userName}}',$userName, $message);
                if(!empty($params['orderInfo']))$message = str_replace('{{orderInfo}}',$params['orderInfo'], $message);
                if(!empty($params['paymentInfo']))$message = str_replace('{{paymentInfo}}',$params['paymentInfo'], $message);
            break;
            case 'CC':
                $subject = '구매취소완료문자';
                if($userName)$message = str_replace('{{userName}}',$userName, $message);
                if(!empty($params['cancleProduct']))$message = str_replace('{{cancleProduct}}',$params['cancleProduct'], $message);
                if(!empty($params['refundPrice']))$message = str_replace('{{refundPrice}}',$params['refundPrice'], $message);

            break;
            case 'RC':
                $subject = '반품완료문자';
                if($userName)$message = str_replace('{{userName}}',$userName, $message);
                if(!empty($params['returnProduct']))$message = str_replace('{{returnProduct}}',$params['returnProduct'], $message);
                if(!empty($params['refundPrice']))$message = str_replace('{{refundPrice}}',$params['refundPrice'], $message);

            break;
            case 'EC':
                $subject = '상품교환완료문자';
                if($userName)$message = str_replace('{{userName}}',$userName, $message);
                if(!empty($params['exchangeProduct']))$message = str_replace('{{exchangeProduct}}',$params['exchangeProduct'], $message);

            break;
        }
        return ['message'=>$message,'subject'=>$subject];
    }
    private function smsAction(array $sendParams) {

        /******************** 인증정보 ********************/
         $sms_url = "https://sslsms.cafe24.com/sms_sender.php"; // HTTPS 전송요청 URL
         // $sms_url = "http://sslsms.cafe24.com/sms_sender.php"; // 전송요청 URL

         $sendPcs = trim(str_replace('-','',$sendParams['sendPcs']));
         $sendPcs1 = substr($sendPcs,0,3);
         $sendPcs2 = substr($sendPcs,3,4);
         $sendPcs3 = substr($sendPcs,7,4);


         $sms['user_id'] = base64_encode($sendParams['smsId']); //SMS 아이디.
         $sms['secure'] = base64_encode($sendParams['authkey']) ;//인증키
         $sms['msg'] = base64_encode(stripslashes($sendParams['message']));
         $msgLength =  mb_strwidth($sms['msg'],'UTF-8');
         if($msgLength > 90) {
            $sms['smsType'] = base64_encode('L');
            $sms['subject'] =  base64_encode($sendParams['subject']);
         }
         $sms['rphone'] = base64_encode($sendParams['pcs']);
         $sms['sphone1'] = base64_encode($sendPcs1);
         $sms['sphone2'] = base64_encode($sendPcs2);
         $sms['sphone3'] = base64_encode($sendPcs3);
         //$sms['rdate'] = base64_encode($_POST['rdate']);
         //$sms['rtime'] = base64_encode($_POST['rtime']);
         $sms['mode'] = base64_encode("1"); // base64 사용시 반드시 모드값을 1로 주셔야 합니다.
         //$sms['returnurl'] = base64_encode($_POST['returnurl']);
         //$sms['testflag'] = base64_encode('Y');
         //$sms['destination'] = strtr(base64_encode($_POST['destination']), '+/=', '-,');
         //$returnurl = $_POST['returnurl'];
         //$sms['repeatFlag'] = base64_encode($_POST['repeatFlag']);
         //$sms['repeatNum'] = base64_encode($_POST['repeatNum']);
         //$sms['repeatTime'] = base64_encode($_POST['repeatTime']);
         //$sms['smsType'] = base64_encode($_POST['smsType']); // LMS일경우 L
         //$nointeractive = $_POST['nointeractive']; //사용할 경우 : 1, 성공시 대화상자(alert)를 생략

         $host_info = explode("/", $sms_url);
         $host = $host_info[2];
         $path = $host_info[3];//."/".$host_info[4];
         srand((double)microtime()*1000000);
         $boundary = "---------------------".substr(md5(rand(0,32000)),0,10);
         //print_r($sms);
             // 헤더 생성
         $header = "POST /".$path ." HTTP/1.0\r\n";
         $header .= "Host: ".$host."\r\n";
         $header .= "Content-type: multipart/form-data, boundary=".$boundary."\r\n";

        $data = '';
         // 본문 생성
         foreach($sms AS $index => $value){
             $data .="--$boundary\r\n";
             $data .= "Content-Disposition: form-data; name=\"".$index."\"\r\n";
             $data .= "\r\n".$value."\r\n";
             $data .="--$boundary\r\n";
         }
         $header .= "Content-length: " . strlen($data) . "\r\n\r\n";

         $fp = fsockopen($host, 80);

         if ($fp) {
             fputs($fp, $header.$data);
             $rsp = '';
             while(!feof($fp)) {
                 $rsp .= fgets($fp,8192);
             }
             fclose($fp);
             $msg = explode("\r\n\r\n",trim($rsp));
             $rMsg = explode(",", $msg[1]);
             $Result= $rMsg[0]; //발송결과
             $Count= $rMsg[1]; //잔여건수

             //발송결과 알림
             if($Result=="success") {
                 $alert = 'success';
                 //$alert .= " 잔여건수는 ".$Count."건 입니다.";
             }
             else if($Result=="reserved") {
                 $alert = "성공적으로 예약되었습니다.";
                 $alert .= " 잔여건수는 ".$Count."건 입니다.";
             }
             else if($Result=="3205") {
                 $alert = "잘못된 번호형식입니다.";
             }

             else if($Result=="0044") {
                 $alert = "스팸문자는발송되지 않습니다.";
             }

             else {
                 $alert = "[Error]".$Result;
             }
         }
         else {
             $alert = "Connection Failed";
         }
        return $alert;


    }
}
