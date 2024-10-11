<!doctype html>
<html lang="">
    <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Riverflow shop install</title>
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Nanum+Gothic&display=swap" rel="stylesheet">
   <script
     src="https://code.jquery.com/jquery-3.6.0.min.js"
     integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
     crossorigin="anonymous"></script>

   <script>
    function validate() {
        if(!$('#dbHost').val()) {
            alert('데이타베이스 HOST를 입력하세요');
            $('#dbHost').focus();
            return false;
        }
        if(!$('#dbPort').val()) {
            alert('데이타베이스 PORT를 입력하세요');
            $('#dbPort').focus();
            return false;
        }
        if(!$('#dbId').val()) {
            alert('데이타베이스 아이디를 입력하세요');
            $('#dbId').focus();
            return false;
        }
        if(!$('#dbPw').val()) {
            alert('데이타베이스 비밀번호를 입력하세요');
            $('#dbPw').focus();
            return false;
        }
        if(!$('#dbName').val()) {
            alert('데이타베이스 이름을 입력하세요');
            $('#dbName').focus();
            return false;
        }
        if(!$('#adminId').val()) {
            alert('관리자 아이디를 입력하세요');
            $('#adminId').focus();
            return false;
        }
        if(!$('#adminPw').val()) {
            alert('관리자 비밀번호를 입력하세요');
            $('#adminPw').focus();
            return false;
        }
        if(!$('#reAdminPw').val()) {
            alert('관리자 비밀번호확인을 입력하세요');
            $('#reAdminPw').focus();
            return false;
        }
        if($('#reAdminPw').val() !=$('#adminPw').val()) {
            alert('관리자 비밀번호와 비밀번호확인이 일치하지 않습니다');
            $('#reAdminPw').focus();
            return false;
        }
        $('#screen').show();
        $.ajax({
            url: "/install/installAction",
            data: $('#frm').serialize(),
            method: "POST",   // HTTP 요청 메소드(GET, POST 등)
            dataType: "json",
            success:function(resp) {
                if(resp.status=='success') {
                   insertAdmin();
                } else {
                    $('#screen').hide();
                    alert('설치에 실패하였습니다');
                }
            },
            error:function(request,status,error){
                $('#screen').hide();
                console.log("code:"+request.status+" message:"+request.responseText+"error:"+error);
            }

        })
        return false;

    }
    function insertAdmin() {

        $.ajax({
                url: "/install/insertAdmin",
                data: {adminId:$('#adminId').val(),adminPw:$('#adminPw').val(),'_token':$('#token').val()},
                method: "POST",   // HTTP 요청 메소드(GET, POST 등)
                dataType: "json",
                success:function(resp) {
                    if(resp.status=='success') {
                        location.href = '/';
                    } else {
                        $('#screen').hide();
                        alert('설치에 실패하였습니다');
                    }
                },
                error:function(request,status,error){
                    $('#screen').hide();
                    console.log("code:"+request.status+" message:"+request.responseText+"error:"+error);
                }

        })

    }
    </script>
    <style>
        body {
            font-family: 'Nanum Gothic', sans-serif !important;
            background:#fafafa;

        }
        div,td,th,span {
            font-family: 'Nanum Gothic', sans-serif !important;
            font-size:14px;
        }
        .container {
            width:600px;
            height:600px;
            margin:50px auto;
            color:#000;
        }
        th {
            text-align:left;
            height:50px;
            width:240px;
            border-bottom:solid 1px #ccc;
        }
        td {
            padding:0;
            border-bottom:solid 1px #ccc;
        }
        input {
            width:358px;
            background:#fff;
            border:solid 1px #ccc;
            height:30px;
        }
        .title {
            font-size:20pt;
            font-weight:bold;
            text-align:center;
        }
        .sub-title {
            font-size:14pt;
            font-weight:bold;
            padding:40px 0 15px 0;
            margin-bottom:0;
            border-bottom:solid 2px #333;
        }
        .button-row {
            width:300px;
            margin:auto;
            padding:80px 0;
        }
        .btn {
            width:300px;
            height:50px;
            border-radius:10px;
            background:#000;
            color:#fff;
            border:none;
            cursor:pointer;
            font-size:13pt;
            font-weight:bold;

        }
        #screen {
            background:rgba(0,0,0,.5);
            width:100%;
            height:100%;
            position:fixed;
            left:0;
            top:0;
            z-index:100;
             display:none;
        }
        #screen img {
            position:absolute;
            top:50%;
            left:50%;
            margin-top:-10px;
            margin-left:-10px;


        }
    </style>
    </head>
    <body>
        <div id='screen'>
            <img src="/loaderImg/Ajax-loader.gif">
        </div>
        <div class="container">
        <div class="title">Riverflow shop 설치</div>
        <form id="frm" onsubmit="return validate()">
        <input type='hidden' name='_token' id='token' value='{{$token}}'>
        <div class="sub-title">데이타 베이스 정보 입력</div>
        <table style="width:100%;" cellpadding=0 cellspacing=0>
        <tbody>
        <tr>
                <th>
                    데이타베이스 HOST
                </th>
                <td>
                    <input type="text" name="dbHost" id="dbHost" value="localhost">
                </td>
            </tr>
            <tr>
                <th>
                    데이타베이스 PORT
                </th>
                <td>
                    <input type="text" name="dbPort" id="dbPort" value="3306">
                </td>
            </tr>

            <tr>
                <th>
                    데이타베이스 아이디
                </th>
                <td>
                    <input type="text" name="dbId" id="dbId">
                </td>
            </tr>
            <tr>
                <th>
                   데이타베이스 비밀번호
                </th>
                <td>
                    <input type="password" name="dbPw" id="dbPw">
                </td>
            </tr>
            <tr>
                <th>
                   데이타베이스 이름
                </th>
                <td>
                    <input type="text" name="dbName" id="dbName">
                </td>
            </tr>
        </tbody>
        </table>
        <div class="sub-title">관리자 정보 입력</div>
        <table style="width:100%;" cellpadding=0 cellspacing=0>
        <tbody>
            <tr>
                <th>
                    관리자 아이디
                </th>
                <td>
                    <input type="text" name="adminId" id="adminId">
                </td>
            </tr>
            <tr>
                <th>
                   관리자 비밀번호
                </th>
                <td>
                    <input type="password" name="adminPw" id="adminPw">
                </td>
            </tr>
            <tr>
                <th>
                   관리자 비밀번호 확인
                </th>
                <td>
                    <input type="password" name="reAdminPw" id="reAdminPw">
                </td>
            </tr>
        </tbody>
        </table>
        <div class="sub-title">사이트 정보</div>
        <table  style="width:100%;" cellpadding=0 cellspacing=0>
        <tbody>
            <tr>
                <th>
                    도메인명
                </th>
                <td>
                    {{$protocol}}://{{$host}}
                </td>
            </tr>
            <tr>
                <th>
                   SSL 보안서버 사용여부
                </th>
                <td>
                    {{$sslUse}}
                </td>
            </tr>
            <tr>
                <th>
                   서버 IP
                </th>
                <td>
                    {{$addr}}
                </td>
            </tr>
        </tbody>
        </table>
        <div class="button-row">
            <button type="submit" class="btn">Riverflow shop 설치</button>
        </div>
        </form>
        </div>
    </body>
</html>