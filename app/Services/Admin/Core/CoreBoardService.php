<?php

namespace App\Services\Admin\Core;

use App\Repositories\Repository\Admin\Customize\CustomizeBoardRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeSettingSiteRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CoreBoardService {

    protected $boardRepository;
    public function __construct(CustomizeBoardRepository $boardRepository,CustomizeSettingSiteRepository $siteRepository) {

        $this->boardRepository = $boardRepository;

        $data = $siteRepository->getSiteInfoByField('images');
        /**
         *이미지 사이즈 정보 가져오기
         *   상품상세 이미지  =>pdetail / base width: 640, height: 640},
         *   상품목록 이미지 => plist / base width: 640, height: 640},
         *   상품후기 목록 이미지 =>prlist / base width: 640, height: 640},
         *   게시판 목록 이미지 =>blist / base width: 640, height: 640},
         **/

        if(!empty($data)) {
            if($data->images) {
                $imageSizeInfo = json_decode($data->images);
                if(!empty($imageSizeInfo->blist)) {
                    $this->boardRepository->articleImgSize['width'] = $imageSizeInfo->blist->width;
                    $this->boardRepository->articleImgSize['height'] =$imageSizeInfo->blist->height;
                }
            }
        }
    }

    // 게시판저장
    public function insertBoard(Request $params) {
        $requestParams = $params->all();
        $fieldsets = makeFieldset($this->boardRepository->useFields,$requestParams);
        $rank = $this->boardRepository->getMaxRank();
        $fieldsets['brank'] = $rank + 1;
        $id = $this->boardRepository->insertBoard($fieldsets);
        if($id) {
            $status = 'success';
        } else $status = 'error';

        return ['status'=>$status,'data'=>$id];
    }

    // 수정
    public function updateBoard(Request $params) {

        $requestParams = $params->all();
        if(!$requestParams['id'])return ['status'=>'fail','data'=>''];
        if($requestParams['id']=='newBoard')return ['status'=>'fail','data'=>''];
        $fieldsets = makeFieldset($this->boardRepository->useFields,$requestParams);
        $id = $this->boardRepository->updateBoard($requestParams['id'],$fieldsets);
        if($id) {
            $data = $this->boardRepository->getBoardInfo($id);
            $status = 'success';
        } else {
            $data = '';
            $status = 'error';
        }

        return ['status'=>$status,'data'=>$data];
    }

    // 삭제
    public function deleteBoard(Request $params) {

        $requestParams = $params->all();
        if(!$requestParams['id'])return ['status'=>'fail','data'=>''];
        $id = $this->boardRepository->deleteBoard($requestParams['id']);
        if($id) {
            $list =  $this->boardRepository->getBoardList();
            $grank = 1;
            foreach($list as $val) {
                $targetFieldsets = ['brank'=>$grank];
                $this->boardRepository->updateBoard($val->id,$targetFieldsets);
                $grank++;
            }
            $status = 'success';
        } else $status = 'error';
        return ['status'=>$status,'data'=>$id];
    }
    // 순서 변경
    public function sequenceBoard(Request $params) {

        $requestParams = $params->all();
        if(!$requestParams['id'] || !$requestParams['cmd'])return ['status'=>'fail','data'=>''];

        $row =$this->boardRepository->getBoardInfo($requestParams['id']);
        $rowData['cmd'] = $requestParams['cmd'];
        $rowData['brank'] = $row->brank;
        $rankInfo = $this->boardRepository->sequenceBoardInfo($rowData);
        if($rankInfo['rank']) {
            $fieldsets = ['brank'=>$rankInfo['rank']];
            $this->boardRepository->updateBoard($row->id,$fieldsets);

            $targetFieldsets = ['brank'=>$row->brank];
            $this->boardRepository->updateBoard($rankInfo['targetId'],$targetFieldsets);
            $data = $this->boardRepository->getBoardList();
        } else {
            $data = 'stay';
        }
        return ['status'=>'success','data'=>$data];

    }

    // 목록
    public function getBoardList() {
        return $this->boardRepository->getBoardList();
    }

    // 게시글 목록
    public function getArticleList(Request $params) {
        $requestParams = $params->all();

        $list['boardList'] = $this->boardRepository->getBoardUseList();
        $list['articleList'] = $this->boardRepository->getArticleList($requestParams);
        return $list;
    }

    // 게시글 목록 (데이타만)
    public function getArticleDataList(Request $params) {
        $requestParams = $params->all();
        $list = $this->boardRepository->getArticleList($requestParams);
        return $list;
    }

    /// 게시글 쓰기
    public function getArticleRegist(Request $params) {

        $list['boardList'] = $this->boardRepository->getBoardUseList();
        $list['imgSize'] = $this->boardRepository->articleImgSize;

        if($params->input('id')) {
            $list['info'] = $this->boardRepository->getArticleInfo($params->input('id'));
        }
        return $list;
    }

    // 게시글 저장
    public function insertArticle(Request $params) {
        $requestParams = $params->all();
        $fieldsets = makeFieldset($this->boardRepository->articleUseFields,$requestParams);
        $user = Auth::user();
        $fieldsets['user_id'] = $user->id;
        if($params->file('img')) {
            $uploadParams['type'] = 'image';
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
        $requestParams = $params->all();
        $id = $params->input('id');
        $fieldsets = makeFieldset($this->boardRepository->articleUseFields,$requestParams);

        if($params->file('img')) {
            $uploadParams['type'] = 'image';
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
        $ids = $params->input('ids');
        $info = $this->boardRepository->deleteArticle($ids);
        if($info) {
                $status = 'success';
        } else $status = 'error';
        return ['status'=>$status,'data'=>$info];
    }

    // faq 게시글 목록
    public function getFaqList(Request $params) {
        $requestParams = $params->all();

        $list['boardInfo'] = $this->boardRepository->getFaqBoard();
        $list['articleList'] = $this->boardRepository->getFaqList($requestParams);
        return $list;
    }

    /// faq 게시글 쓰기
    public function getFaqRegist(Request $params) {

        $list['boardInfo'] = $this->boardRepository->getFaqBoard();

        if($params->input('id')) {
            $list['info'] = $this->boardRepository->getArticleInfo($params->input('id'));
        }
        return $list;
    }

    public function insertArticleTempImage(Request $params) {
        $params['type'] = 'image';

        $imgName = uploadFile($params,'image',$this->boardRepository->filePath,$params);
        $imgUrl = $this->boardRepository->imgUrl.$imgName;

        return ['status'=>'success','data'=>$imgUrl];
    }

}
