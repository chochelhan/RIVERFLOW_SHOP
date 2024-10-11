<?php

use Illuminate\Http\JsonResponse;
use Intervention\Image\Facades\Image;
use function response;

function restResponse($data) {

   return response()->json(['status'=>$data['status'],'data'=>$data['data']]);
}

function apiResponse($data,$newToken) {

   return response()->json(['status'=>$data['status'],'data'=>$data['data'],'newToken'=>$newToken]);
}

function makeFieldset($useFields,$params) {
    $fieldsets = [];
    foreach($useFields as $key) {
        if(isset($params[$key])) {
            if(!empty($params[$key]) || is_numeric($params[$key])) {
                $fieldsets[$key] = $params[$key];
            }
        }
    }
    return $fieldsets;
}
function uploadFile($request,$imgStringName,$filePath,$params) {

   // $extension = $file->extension();
    $fileDeny = false;
    switch($params['type']) {
        case 'image':
            /*
            $validation = $request->validate([
                'image' => 'file|mimes:jpeg, jpg, bmp, png, gif',
            ]);
            */
        break;
    }
    $imgName = '';
    if($request->file($imgStringName)){
        $imgName = $request->file($imgStringName)->hashName();
        $path = $request->file($imgStringName)->storeAs($filePath,$imgName);
        if(!empty($params['resize'])) {
            $imgResize = Image::make(storage_path('app/'.$filePath.'/'.$imgName))
                    ->resize($params['resize']['width'],$params['resize']['height'])->save(storage_path('app/'.$filePath.'/'.$imgName));
        }

    }
    return $imgName;
}

function searchCategory($orgCategory,$category) {
    $searchCategorys = explode(',',$orgCategory);
    $searchFlag = false;
    foreach($searchCategorys as $cate) {
        if($cate==$category) {
            $searchFlag = true;
            break;
        }
    }
    return $searchFlag;
}

/*
*$params [url,post_data,headers]
*
*/
function postCurl($params) {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $params['url']);
    curl_setopt($ch, CURLOPT_POST, true);
    if(!empty($params['post_data'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params['post_data']);
    }
    if(!empty($params['headers'])) {
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$params['headers']);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($status_code == 200) {
        return ['status'=>'success','data'=>$response];
    } else {
        return ['status'=>'fail','data'=>$response];
    }

}

/*
*$params [url,headers]
*
*/
function getCurl($params) {


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $params['url']);
    if(!empty($params['headers'])) {
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$params['headers']);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($status_code == 200) {
        return ['status'=>'success','data'=>$response];
    } else {
        return ['status'=>'fail','data'=>$response];
    }

}


function setDeliveryTracker($deliveryData,$fileName) {
    $fp = fopen($fileName, "r") or die("파일을 열 수 없습니다！");
    $license = fgets($fp);
    fclose($fp);

    $post_data['host'] = $_SERVER['HTTP_HOST'];
    $post_data['deliveryData'] = json_encode($deliveryData);
    $serviceparams['post_data'] = $post_data;
    $serviceparams['url'] = 'https://service.riverflow.co.kr/tracker/service/setTracker';
    $serviceparams['headers'] = ['Authorization: Bearer '.$license];
    $result = postCurl($serviceparams);
    if($result['status'] == 'success') {
        $dataStrings = explode('{',$result['data']);
        if(!empty($dataStrings[0])) {
            $jsonString = str_replace($dataStrings[0],'',$result['data']);
        } else {
            $jsonString = $result['data'];
        }
        $resultData = json_decode($jsonString);
        return $resultData;
        if($resultData->status == 'success') {
            return $resultData->data;
        }
    }
}

function isMobile() {
    $mAgent = ["iPhone","iPod","Android","Blackberry","Opera Mini", "Windows ce", "Nokia", "sony"];
    $chkMobile = false;
    for($i=0; $i<sizeof($mAgent); $i++){
        if(stripos( $_SERVER['HTTP_USER_AGENT'], $mAgent[$i] )){
            $chkMobile = true;
            break;
        }
    }
    // 모바일앱을 경우 ma
    if($chkMobile)return 'mw';
    else return 'pc';
}