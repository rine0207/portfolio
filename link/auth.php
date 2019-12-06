<?php

//ログイン認証・自動ログアウト

//ログインしている場合
if(!empty($_SESSION['login_date'])){
    debug('ログイン済みユーザーです');
    
    //現在日時が最終ログイン日時+有効期限を超えていた場合
    if(($_SESSION['login_date']+$_SESSION['login_limit']) < time()){
        debug('ログイン有効期限オーバーです');
        
        //セッションを削除(ログアウトする)
        session_destroy();
        //ログイン
        header("Location:login.php");
    }else{
        debug('ログイン有効期限内');
        //最終ログイン日時更新
        $_SESSION['login_date'] = time();
        
        //無限ループ防止
        if(basename($_SERVER['PHP_SELF']) === 'login.php'){
            
        debug('マイページ遷移');
        header("Location:main.php"); //マイページ遷移
    }
    }
}else{
    debug('未ログインユーザー');
    if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
        header("Location:login.php"); //ログインページ遷移
    }
}

?>