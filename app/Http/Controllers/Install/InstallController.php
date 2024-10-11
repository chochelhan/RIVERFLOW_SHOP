<?php

namespace App\Http\Controllers\Install;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InstallController extends Controller
{

    private $installToken = 'sderqiwer134534fncajuiwemit45872367';

    public function install()
    {


        $post_data['host'] = $_SERVER['HTTP_HOST'];
        $post_data['addr'] = $_SERVER['SERVER_ADDR'];
        $params['host'] = $_SERVER['HTTP_HOST'];
        $params['addr'] = $_SERVER['SERVER_ADDR'];
        $protocol = 'http';
        if (!empty($_SERVER['HTTPS'])) {
            $protocol = ($_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
        }

        $params['protocol'] = $protocol;
        $params['sslUse'] = ($protocol == 'http') ? '사용안함' : '사용';
        $params['token'] = csrf_token();

        return view('install.install', $params);


    }

    public function installAction(Request $request)
    {

        if (!$request->has(['dbHost', 'dbPort', 'dbId', 'dbPw', 'dbName', 'adminId', 'adminPw'])) {
            return restResponse(['status' => 'fail', 'data' => '']);
        }
        $path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
        if (!file_exists($path . '/license/db.json')) {

            $dirpath = $path . '/license';

            $dbPrams['dbHost'] = $request->input('dbHost');
            $dbPrams['dbPort'] = $request->input('dbPort');
            $dbPrams['dbName'] = $request->input('dbName');
            $dbPrams['dbUserName'] = $request->input('dbId');
            $dbPrams['dbPassword'] = $request->input('dbPw');

            $dbfileName = $dirpath . '/db.json';
            if (file_exists($dbfileName)) {
                unlink($dbfileName);
            }

            $dbfile = fopen($dbfileName, "a") or die("Unable to open file!");
            $txt = json_encode($dbPrams);
            fwrite($dbfile, $txt);
            fclose($dbfile);

            $installSchema = $path . '/installDb/riverflowshop_install.sql';
            exec("mysql -u" . $dbPrams['dbUserName'] . " -p" . $dbPrams['dbPassword'] . " " . $dbPrams['dbName'] . " < " . $installSchema);


            $fileName = $dirpath . '/end.txt';
            if (file_exists($fileName)) {
                unlink($fileName);
            }
            return restResponse(['status' => 'success', 'data' => '']);
        }
    }

    public function insertAdmin(Request $request)
    {

        if (!$request->has(['adminId', 'adminPw'])) {
            return restResponse(['status' => 'fail', 'data' => '']);
        }
        $path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
        if (file_exists($path . '/license/db.json') || !file_exists($path . '/license/end.txt')) {
            $memberTable = config('tables.users');
            $adminPass = Hash::make($request->input('adminPw'));
            $memberParams = ['uid' => $request->input('adminId'),
                'name' => '관리자',
                'email' => 'admin@sample.com',
                'email_verified_at' => now(),
                'admin' => 'yes',
                'password' => $adminPass, // password
                'remember_token' => Str::random(10)];
            DB::table($memberTable)->insert($memberParams);

            $fileName = $path . '/license/end.txt';
            $file = fopen($fileName, "a") or die("Unable to open file!");
            $txt = 'install end';
            fwrite($file, $txt);
            fclose($file);

            return restResponse(['status' => 'success', 'data' => '']);

        } else {
            return restResponse(['status' => 'fail', 'data' => '']);
        }


    }
}
