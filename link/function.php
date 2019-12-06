<?php

ini_set('display_errors',1);
ini_set('log_errors','on');
ini_set('error_log','php.log');

    //デバッグ
$debug_flg = true;
    //ログ関数
    function debug($moji){
        global $debug_flg;
        if(!empty($debug_flg)){
            error_log('デバッグ：'.$moji);
        }
    }

    //セッション準備・セッション有効期限を延ばす
    //セッションファイルの置き場を変更する
    session_save_path("/var/tmp/");
    //ガーベージコレクションが削除するセッションの有効期限を設定（30日以上経っているものに対してだけ１００分の１の確率で削除）
    ini_set('session.gc_maxlifetime', 60*60*24*30);
    //クッキーの有効期限を伸ばす
    ini_set('session.cookie_lifetime', 60*60*24*30);

    //セッションスタート
    session_start();
    //なりすましのセキュリティ対策
    session_regenerate_id();

    //ログ吐き出し関数
    function debugLogStart(){
        debug('画面表示処理開始');
        debug('セッションID：'.session_id());
        debug('変数の中身を出力：'.print_r($_SESSION,true));
        debug('現在日時：'.time());
        if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
            debug('ログイン期限：'.($_SESSION['login_date'] + $_SESSION['login_limit']));
        }
    }

    //定数設定
    define('MSG01','入力必須です');
    define('MSG02','Email形式で入力してください');
    define('MSG03','パスワードの再入力があっていません');
    define('MSG04','半角英数字のみご利用いただけます');
    define('MSG05','6文字以上で入力してください');
    define('MSG06','256文字以内で入力してください');
    define('MSG07','エラーが発生しました');
    define('MSG08','そのemailはすでに登録しています');
    define('MSG09','MAILまたはパスワードが違います');
    define('MSG10','漢字で入力してください');
    define('MSG11','カタカナで入力してください');
    define('MSG12','半角数字で入力してください');
    define('MSG13','形式が間違っているか<br>'.'ハイフンが含まれています');
    define('MSG14','郵便番号の形式が違います');
    define('MSG15','古いパスワードが違います');
    define('MSG16','古いパスワードと同じです');
    define('MSG17','文字で入力してください');
    define('MSG18','正しくありません');
    define('MSG19','有効期限が切れています');
    define('SUC01','パスワードを変更しました');
    define('SUC02','プロフィールを変更しました');
    define('SUC03','メールを送信しました');
    define('SUC04','投稿しました');
    define('SUC05','ユーザー登録完了しました');
    define('SUC06','ログイン完了しました');
    define('SUC07','退会しました');

    $err_msg = array();
        

//-------------------バリデーション関数-------------------------------
        //未入力バリデーション
        function Minyuuryoku($moji,$key){
            if(empty($moji)){
                global $err_msg;
                $err_msg[$key] = MSG01;
            }
        }
        
        //漢字正規バリデーション
        function KanjiKeisiki($moji,$key){
            mb_regex_encoding("UTF-8");
            if(!preg_match('/^[一-龠]+$/u',$moji)){
                global $err_msg;
                $err_msg[$key] = MSG10;
            }
        }

        //かな正規バリデーション
        function KatakanaKeisiki($moji,$key){
            mb_regex_encoding("UTF-8");
            if(!preg_match('/^[ァ-ヶー]+$/u',$moji)){
                global $err_msg;
                $err_msg[$key] = MSG11;
            }
        }

        //年齢半角チェック
        function Hankakuage($moji,$key){
            if(!preg_match("/^[0-9\-]+$/", $moji)){
                global $err_msg;
                $err_msg[$key] = MSG12;
            }
        }
        

        //tel正規バリデーション
        function Telkeisiki($moji,$key){
            if(!preg_match( "/^0\d{9,10}$/",$moji)){
                global $err_msg;
                $err_msg[$key] = MSG13;
            }
        }

        //zip形式
        function Zipkeisiki($moji,$key){
            if(!preg_match("/^\d{7}$/", $moji)){
                global $err_msg;
                $err_msg[$key] = MSG14;
            }
        }

        //email正規表現バリデーション
        function Keisiki($moji,$key){
            if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$moji)){
                global $err_msg;
                $err_msg[$key] = MSG02;
            }
        }
        //email重複チェック
        function Jufuku($email){
            global $err_msg;
        //例外処理
            try{
                $dbh = dbConnect();
                $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
                $data = array(':email' =>$email);
                //クエリ実行
                $stmt = queryPost($dbh,$sql,$data);
                //結果の取得
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if(!empty($result['count(*)'])){
                    debug('$resultの中身：'.print_r($result,true));
                    $err_msg['email'] = MSG08;
                }
            }catch(Exception $e){
            error_log('エラー発生：' . $e->getMessage());
                $err_msg['common'] = MSG07;
            }
        }
        
        //最大文字数バリデーション
        function Maxlen($moji,$key,$max=255){
            if(mb_strlen($moji) > $max){
                global $err_msg;
                $err_msg[$key] = MSG06;
            }
        }
        
        //最小文字数バリデーション
        function Minlen($moji,$key,$min=6){
            if(mb_strlen($moji) < $min){
                global $err_msg;
                $err_msg[$key] = MSG05;
            }
        }
        //同値バリデーション
        function Match($moji,$moji2,$key){
            if($moji !== $moji2){
                global $err_msg;
                $err_msg[$key] = MSG03;
            }
        }
        



        //半角バリデーション
        function Hankaku($moji,$key){
            if(!preg_match("/^[a-zA-Z0-9]+$/", $moji)){
                global $err_msg;
                $err_msg[$key] = MSG04;
            }
        }

        //固定長チェック
        function validLength($moji,$key,$len=8){
            if(mb_strlen($moji) !== $len){
                global $err_msg;
                $err_msg[$key] = $len . MSG17;
            }
        }


        //パスワード変更
        function validPass($moji,$key){
            //半角チェック
            Hankaku($moji,$key);
            //最大文字数
            Maxlen($moji,$key);
            //最小文字数
            Minlen($moji,$key);
        }
        
        function Geterrmsg($key){
            global $err_msg;
            if(!empty($err_msg[$key])){
                return $err_msg[$key];
            }
        }
        



        //DB接続
        function dbConnect(){
            //DBへの接続準備
            $dsn = 'mysql:dbname=portfolio;host=localhost;charset=utf8';
            $user = 'root';
            $password = 'root';
            $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
        );
            //オブジェクト生成
            $dbh = new PDO($dsn,$user,$password,$options);
            return $dbh;
        }
        
        //SQL実行
        function queryPost($dbh,$sql,$data){
            $stmt = $dbh->prepare($sql);
            if(!$stmt->execute($data)){
                debug('クエリ失敗');
                debug('SQLエラー：'.print_r($stmt->errorInfo(),true));
                $err_msg['common'] = MSG07;
                return 0;
            }
            debug('クエリ成功');
            return $stmt;
        }

        //プロフィール編集画面で使う処理
        function getUser($u_id){
            debug('ユーザー情報取得');
            
            try{
                $dbh = dbConnect();
                $sql = 'SELECT * FROM users WHERE id = :u_id AND delete_flg = 0';
                $data = array(':u_id'=>$u_id);
                
                $stmt = queryPost($dbh,$sql,$data);
                
                if($stmt){
                    return $stmt->fetch(PDO::FETCH_ASSOC);
                }else{
                    return false;
                }
            }catch (Exception $e){
                error_log('エラー発生：'.getMessage());
            }
        }

        //情報idを取得して取ってくる
        function getProduct($u_id,$m_id){
            debug('情報を取得');
            debug('ユーザーid:'.$u_id);
            debug('情報id:'.$m_id);
            
            try{
                $dbh = dbConnect();
                $sql = 'SELECT * FROM main WHERE user_id = :u_id AND id = :m_id AND delete_flg = 0';
                $data = array(':u_id'=>$u_id,':m_id' => $m_id);
                $stmt = queryPost($dbh,$sql,$data);
                
                if($stmt){
                    return $stmt->fetch(PDO::FETCH_ASSOC);
                }else{
                    return false;
                }
            }catch (Exception $e){
                error_log('エラー発生：'.$e->getMessage());
            }
        }


        function getMyProduct($u_id){
            debug('日記情報を取得');
            debug('ユーザーID：'.$u_id);
                try{
                    $dbh = dbConnect();
                    $sql = 'SELECT * FROM main WHERE user_id = :u_id AND delete_flg=0';
                    $data = array(':u_id'=>$u_id);
                    $stmt = queryPost($dbh,$sql,$data);
                    
                    if($stmt){
                        return $stmt->fetchAll();
                    }else{
                        return false;
                    }
                }catch(Exception $e){
                    error_log('エラー発生：'.$e->getMessage());
                }
        }

        //カテゴリー
        function getCategory(){
            debug('カテゴリー情報を取得します');
            try{
                $dbh = dbConnect();
                $sql = 'SELECT * FROM category';
                $data = array();
                $stmt = queryPost($dbh,$sql,$data);
                
                if($stmt){
                    return $stmt->fetchAll();
                }else{
                    return false;
                }
            }catch (Exception $e){
                error_log('エラー発生：'.$e->getMessage());
            }
        }

        function sanitize($moji){
            return htmlspecialchars($moji,ENT_QUOTES);
        }

        //入力保持
        function getFormData($moji,$flg=false){
            if($flg){
                $method = $_GET;
            }else{
                $method = $_POST;
            }
            global $dbFormData;
            //ユーザーデータがある場合
            if(!empty($dbFormData)){
                //フォームのエラーがある場合
                if(!empty($err_msg[$moji])){
                    //POSTにデータが有る場合
                    if(isset($method[$moji])){//郵便番号などのフォームで数字や数値の０が入っている場合があるためemptyではなくisset
                        return sanitize($method[$moji]);
                        debug('$methodの中身：'.print_r($method,true));
                    }else{
                        //POSTにデータがない場合DBの情報を表示
                        return sanitize($dbFormData[$moji]);
                    }
                }else{
                    //POSTにデータが有り、DBの情報が違う場合
                    if(isset($method[$moji]) && $method[$moji] !== $dbFormData[$moji]){
                        return sanitize($method[$moji]);
                    }else{ //そもそも変更していない
                        return sanitize($dbFormData[$moji]);
                        debug('$dbFormDataの中身：'.print_r($dbFormData,true));
                    }
                }
            }else{
                if(isset($method[$moji])){
                    return sanitize($method[$moji]);
                    debug('$methodの中身：'.print_r($method,true));
                }
            }
        }
        
        //sessionを一回だけ取得する
        function getSessionFlash($key){
            if(!empty($_SESSION[$key])){
                $data = $_SESSION[$key];
                $_SESSION[$key] = '';
                return $data;
            }
        }

        //認証キー生成 パスワード再発行
        function makeRandKey($length = 8){
            $chars = 'abcdefghijklmnopqrstuvwxyz';
            $str = '';
            for($i = 0; $i < $length; ++$i){
                $str .= $chars[mt_rand(0,25)];
            }
            return $str;
        }

        //画像アップロードバリデーション
        function uploadImg($file,$key){
            debug('画像アップロード処理開始');
            debug('ファイル情報：'.print_r($file,true));
            if(isset($file['error']) && is_int($file['error'])){
                try{
                    switch($file['error']){
                        case UPLOAD_ERR_OK:
                            break;
                        case UPLOAD_ERR_NO_FILE: //ファイル未選択の場合
                            throw new runtimeexception('ファイルが選択されていません');
                        case UPLOAD_ERR_INI_SIZE: //php.ini定義の最大サイズが超過した場合
                        case UPLOAD_ERR_FORM_SIZE: //フォーム定義の最大サイズが超過した場合
                            throw new runtimeexception('ファイルサイズが大きすぎます');
                        default: //例外
                            throw new runtimeexception('その他のエラーが発生しました');
                    }
                    $type = @exif_imagetype($file['tmp_name']);
                    if(!in_array($type, [IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG],true)){
                        throw new runtimeexception('画像形式が未対応です');
                    }
                    
                    $path = 'images/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
                    
                    if(!move_uploaded_file($file['tmp_name'],$path)){ //ファイルを移動する
                        throw new runtimeexception('ファイル保存時にエラーが発生しました');
                    }
                    //保存したファイルパスのパーミッション（権限）を変更する
                    chmod($path,0644);
                    
                    debug('ファイルは正常にアップロードされました');
                    debug('ファイルパス：'.$path);
                    return $path;
                }catch (runtimeexception $e){
                    debug($e->getMessage());
                    global $err_msg;
                    $err_msg[$key] = $e->getMessage();
                }
            }
        }

        //画像表示用関数
        function showimg($path){
            if(empty($path)){
                return 'img/sample-img.png';
            }else{
                return $path;
            }
        }

        //GETパラメータ与える
        //$del_key : GETパラメータのキー
        function appendGetParam($arr_del_key=array()){
            if(!empty($_GET)){
                $str = '?';
                foreach($_GET as $key => $val){
                    if(!in_array($key,$arr_del_key,true)){
                        $str .=$key.'='.$val.'&';
                    }
                }
                $str = mb_substr($str,0,-1,"UTF-8");
                return $str;
            }
        }
?>