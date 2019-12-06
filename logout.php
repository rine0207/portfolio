<?php

require('link/function.php');

debug('-------------------------');
debug('ログアウト');
debug('-------------------------');
debugLogStart();

debug('ログアウトします');
//セッション削除
session_destroy();
debug('ログインページ遷移');
//ログインページへ
header("location:login.php");