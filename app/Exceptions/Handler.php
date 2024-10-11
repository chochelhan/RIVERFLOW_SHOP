<?php

namespace App\Exceptions;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (NotFoundHttpException $e, $request) {

            $siteInfos = \App\Models\Customize\CustomizeSettingSite::first();
            $siteEnv = 'developer';
            if(!empty($siteInfos)) {
                $siteEnvData = ($siteInfos->siteEnv)?json_decode($siteInfos->siteEnv):'';
                if($siteEnvData && !empty($siteEnvData->siteEnv)) {
                    $siteEnv = $siteEnvData->siteEnv;
                }
            }
            $protocol = 'http';
            if(!empty($_SERVER['HTTPS'])) {
                $protocol = ($_SERVER['HTTPS']=='on')?'https':'http';

            }
            if($siteEnv == 'production' && $protocol=='http') {
                return redirect('https://'.$_SERVER['HTTP_HOST'].'/');
            } else {
                $request->session()->regenerate();
                $token = csrf_token();

                $metaTitle = '';
                $metaKeyword = '';
                $metaContent = '';
                if(!empty($siteInfos) && !empty($siteInfos->company)) {
                    $siteMetaInfo = json_decode($siteInfos->company);
                    if($siteMetaInfo) {
                        $metaTitle = $siteMetaInfo->siteName;
                        $metaKeyword = (!empty($siteMetaInfo->metaKeyword))?$siteMetaInfo->metaKeyword:'';
                        $metaContent = (!empty($siteMetaInfo->metaContent))?$siteMetaInfo->metaContent:'';
                    }

                }
                $data = ['csrf_token'=>$token,
                        'metaTitle'=>$metaTitle,
                        'metaKeyword'=>$metaKeyword,
                        'metaContent'=>$metaContent];

                return response()->view('index',$data, 404);
            }
             
        });
        /*
         $this->reportable(function (Throwable $e) {
            print_r($e);
            exit;
        });
        */
    }
}
