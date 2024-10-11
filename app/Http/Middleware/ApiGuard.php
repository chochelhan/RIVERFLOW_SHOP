<?php

namespace App\Http\Middleware;
use Closure;

class ApiGuard
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
      public function handle($request, Closure $next) {

            $token = csrf_token();
            $siteInfos = \App\Models\Customize\CustomizeSettingSite::first();
            $siteEnvData = ($siteInfos->siteEnv)?json_decode($siteInfos->siteEnv):'';
            if($siteEnvData && !empty($siteEnvData->siteEnv)) {
                $this->siteEnv = $siteEnvData->siteEnv;
            }
            $csrfTokenError = false;
			if($this->siteEnv=='production') {
                if ($request->hasHeader('X-CSRF-TOKEN')) {
                    if($token != $request->header('X-CSRF-TOKEN')) {
                        $csrfTokenError = true;
                    }
                } else {
                    $csrfTokenError = true;
                }
            }
			if($csrfTokenError) {
                return response('tokenError',300);
            }
            return $next($request);
      }

}
