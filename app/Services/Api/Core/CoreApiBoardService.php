<?php

namespace App\Services\Api\Core;

use App\Repositories\Repository\Api\Customize\CustomizeApiBoardRepository;

use App\Services\Api\Core\CoreApiAuthHeader;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;

class CoreApiBoardService extends CoreApiAuthHeader {

    protected $boardRepository;

    public function __construct(Request $request,
                                CustomizeApiBoardRepository $boardRepository) {
        parent::__construct($request);

        $this->boardRepository = $boardRepository;
    }

    // 게시판 목록
    public function getArticleList(Request $request) {
        $data['imageset'] = $this->siteInfos['images'];
        $data['memberset'] = $this->siteInfos['member'];
        $data['boardInfo'] = $this->boardRepository->getBoardInfo($request->input('bid'));
        $data['articleList'] =  $this->boardRepository->getArticleList($request->all());

        return $data;
    }

    // 공지사항/faq
    public function getArticleListByBtype(Request $request) {
        $params = $request->all();
        $params['getType'] = 'btype';
        $data['boardInfo'] = $this->boardRepository->getBoardByType($params['btype']);
        $data['articleList'] =  $this->boardRepository->getArticleList($params);

        return $data;
    }
    public function getArticleInfo(Request $request) {
        $data['imageset'] = $this->siteInfos['images'];
        $data['memberset'] = $this->siteInfos['member'];
        $data['boardInfo'] = $this->boardRepository->getBoardInfo($request->input('bid'));
        if(!empty($request->input('id'))) {

            $articleInfo =  $this->boardRepository->getArticleInfo($request->input('id'));
            if($articleInfo->secret=='yes') {
                if($articleInfo->user_id) {
                    if(empty($this->isLoginInfo) || empty($this->isLoginInfo->id)) {
                        return ['status'=>'error','data'=>''];
                    } else if($this->isLoginInfo->id != $articleInfo->user_id) {
                        return ['status'=>'error','data'=>''];
                    }
                } else {
	                $sessArticleInfo = $request->session()->get('sessArticleInfo');
	                if ($sessArticleInfo['id'] != $articleInfo->id) {
		                return ['status' => 'error', 'data' => ''];
	                }
                }
            }
            if(!empty($request->input('type')) && $request->input('type')=='view') {
                $hitFlag = true;
                if(!empty($articleInfo->user_id)) {
                    if(!empty($this->isLoginInfo) && $this->isLoginInfo->id != $articleInfo->user_id) {
                        $hitFlag = false;
                    }
                } else {
                    $sessArticleInfo = $request->session()->get('sessArticleInfo');
                    if(!empty($sessArticleInfo['id']) && $sessArticleInfo['id'] != $articleInfo->id) {
                        $hitFlag = false;
                    }
                }
                if($hitFlag) {
                    $hit = ($articleInfo->hit)?$articleInfo->hit +1:1;
                    $this->boardRepository->updateArticle($articleInfo->id,['hit'=>$hit]);
                }
            }
            $data['articleInfo'] = $articleInfo;
        }
        return ['status'=>'success','data'=>$data];
    }

    // 게시글 저장
    public function insertArticle(Request $params) {

        $boardInfo = $this->boardRepository->getBoardInfo($params->input('bid'));
        if($boardInfo->wauth=='user') {
            if(empty($this->isLoginInfo) || empty($this->isLoginInfo->id)) {
                return ['status'=>'notLogin','data'=>''];
            }
        }
        $requestParams = $params->all();
        $fieldsets = makeFieldset($this->boardRepository->articleUseFields,$requestParams);
        if(!empty($requestParams['user_pass'])) {
            $fieldsets['user_pass'] = Crypt::encryptString($requestParams['user_pass']);
        }

        if(!empty($this->isLoginInfo) && !empty($this->isLoginInfo->id)) {
            $fieldsets['user_id'] = $this->isLoginInfo->id;
        }

        if($params->file('img')) {
            $uploadParams['type'] = ['image'];
            $uploadParams['resize'] = $this->boardRepository->articleImgSize;
            $imgName = uploadFile($params,'img',$this->boardRepository->filePath,$uploadParams);
            if(empty($imgName)) {
                return ['status'=>'message','data'=>'wrong_img'];
            }
            $fieldsets['img'] = $this->boardRepository->imgUrl.$imgName;
        }

         $info = $this->boardRepository->insertArticle($fieldsets);
         if($info) {
            $status = 'success';
         } else $status = 'error';
         return ['status'=>$status,'data'=>$info];

    }

    // 게시글 수정
    public function updateArticle(Request $params) {

         $authResult = $this->checkArticleAuth($params);
         if($authResult['status']!='success')return $authResult;

         $requestParams = $params->all();
         $id = $params->input('id');
         $fieldsets = makeFieldset($this->boardRepository->articleUseFields,$requestParams);

         if(!empty($requestParams['user_pass'])) {
             $fieldsets['user_pass'] = Crypt::encryptString($requestParams['user_pass']);
         }


        if($params->file('img')) {
            $uploadParams['type'] = ['image'];
            $uploadParams['resize'] = $this->boardRepository->articleImgSize;
            $imgName = uploadFile($params,'img',$this->boardRepository->filePath,$uploadParams);
            if(empty($imgName)) {
                return ['status'=>'message','data'=>'wrong_img'];
            }
            $fieldsets['img'] = $this->boardRepository->imgUrl.$imgName;
        }
         $info = $this->boardRepository->updateArticle($id,$fieldsets);
         if($info) {
            $status = 'success';
         } else $status = 'error';
         return ['status'=>$status,'data'=>$info];

    }
    // 게시글 삭제
    public function deleteArticle(Request $params) {
         $authResult = $this->checkArticleAuth($params);
         if($authResult['status']!='success')return $authResult;

        $id = $params->input('id');
        $info = $this->boardRepository->deleteArticle([$id]);
        if($info) {
                $status = 'success';
        } else $status = 'error';
        return ['status'=>$status,'data'=>$info];
    }
     // 게시글 비밀번호 체크
    public function checkArticleUserPass(Request $params) {
        $id = $params->input('id');
        $user_pass = $params->input('user_pass');

        $info =  $this->boardRepository->getArticleInfo($id);
        if($info) {

            if($info->user_pass && $user_pass == Crypt::decryptString($info->user_pass)) {
                $sessArticleInfo= ['id'=>$info->id];
                $params->session()->put('sessArticleInfo',$sessArticleInfo);
                $status = 'success';
            } else {
                $status = 'message';
            }

        } else $status = 'error';
        return ['status'=>$status,'data'=>''];
    }

    private function checkArticleAuth(Request $params) {

         $boardInfo = $this->boardRepository->getBoardInfo($params->input('bid'));
         $articleInfo =  $this->boardRepository->getArticleInfo($params->input('id'));
         if($articleInfo->user_id) {
            if(empty($this->isLoginInfo) || empty($this->isLoginInfo->id)) {
                return ['status'=>'error','data'=>''];
            } else if($this->isLoginInfo->id != $articleInfo->user_id) {
                return ['status'=>'error','data'=>''];
            }
         }
         if($boardInfo->wauth=='user') {
            if(empty($this->isLoginInfo) || empty($this->isLoginInfo->id)) {
                return ['status'=>'notLogin','data'=>''];
            }
            if($this->isLoginInfo->id != $articleInfo->user_id) {
                return ['status'=>'error','data'=>''];
            }
         } else {
	         if($this->isLoginInfo && $this->isLoginInfo->id && ($this->isLoginInfo->id == $articleInfo->user_id)) {

	         } else {

		         $sessArticleInfo = $params->session()->get('sessArticleInfo');
		         if ($sessArticleInfo['id'] != $articleInfo->id) {
			         return ['status' => 'error', 'data' => ''];
		         }
	         }
         }
         return ['status'=>'success','data'=>''];
    }

    public function insertArticleTempImage(Request $params) {
        $params['type'] = 'image';

        $imgName = uploadFile($params,'image',$this->boardRepository->filePath,$params);
        $imgUrl = $this->boardRepository->imgUrl.$imgName;

        return ['status'=>'success','data'=>$imgUrl];
    }
}
