<?php

ini_set('display_errors',1);
ini_set('log_errors','on');
ini_set('errors_log','php.log');


if(!empty($file)){
    //バリ
    
    //画像の保存
    $upload_path = 'images/'.$file['name'];
    $rst = move_uploaded_file($file['tmp_name'],$upload_path); //移動元と移動先のファイルパスを指定
    
    //結果によって表示するメッセージを変数に入れる
    if($rst){
        $msg = '画像をアップしました。アップした画像ファイル名：'.$file['name'];
        $img_path = $upload_path; //表示する画像パスの変数へ画像パスを入れる
    }else{
        $msg = '画像はアップできませんでした。エラー内容：'.$file['error'];
    }
}else{
    $msg = '画像を選択してください';
}
?>