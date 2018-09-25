<?php

//データーベース接続
$dsn='データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn,$user,$password);

//例外をスローしてくれる
$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);

//データの受け取りと宣言
$comment = htmlspecialchars($_POST['comment']);  //コメント受け取り
$name = htmlspecialchars($_POST['name']);  //名前受け取り
$remove_num = $_POST['remove_num'];  //消去番号受け取り
$edit_num = $_POST['edit_num'];  //編集番号受け取り
$edit_flag = $_POST['edit_flag'];  //編集フラグ受け取り
$password = htmlspecialchars($_POST['password']);  //パスワード受け取り
$message;  //メッセージ

//行数の取得処理
try{
     $sql = "SELECT COUNT(*) FROM abcd";  //行数の取得をするsql
     $results = $pdo -> query($sql); 
     $id = $results -> fetchColumn();  //データベースの行数を取得

}catch (Exception $e){
        var_dump($e->getMessage());

}

//編集機能、新規投稿機能、どれを実行するか判定
if(strval($edit_flag) != ''){
      //編集フラグ入力あり
      if(strval($name) != '' && strval(comment) != ''){
       //名前とコメント入力あり
       if($password != ''){
         //パスワード入力あり
         //データベースのパスワード取り出し処理
         $sql = "SELECT * FROM abcd WHERE num = $edit_flag";

         try{
              $results = $pdo -> query($sql);
         
         }catch (Exception $e){
                  var_dump($e->getMessage());
         }
         foreach($results as $row){  //$rowの中にはテーブルのカラム名が入る
          $base_password = $row['password'];  //パスワード取り出し
        }
   
      if($base_password == $password){
             //入力されたパスワードが正しい
             //編集機能

             //編集するsql
$sql = "update abcd set name='$name',comment='$comment' where num = $edit_flag";

         try{
              //sql実行
              $results = $pdo -> query($sql);
         }catch(Exception $e){
                //例外が起きたら、ブラウザにエラーメッセージを表示
               var_dump($e -> getMessage());
        }
       $message = "edit_flag"."番目の投稿を編集しました！";
}else{
     //パスワードが合わない
     $message = "パスワードが間違っています！";
}

}else{
      //パスワード入力無し
      $message = "パスワードが入力されていません！";
}
}

//編集モード終了時に編集フラグを空にする
$edit_flag = '';

}else if(strval($edit_num) != ''){
    //編集対象番号入力あり
        if(is_numeric($edit_num)){
            //編集指定番号が数字である

           //編集フラグを立てる処理をする
           $sql = "SELECT * FROM abcd WHERE num = $edit_num";

           try{
                $results = $pdo -> query($sql);
           }catch(Exception $e){
                  var_dump($e->getMessage());
           }

           foreach($results as $row){
              //編集データの取得
             $edit_name = $row['name'];
             $edit_comment = $row['comment'];
             $edit_flag = $edit_num;
           }
           $message = "編集モードになりました！";
}else{
      //編集対象番号が文字 
      
       $message = "編集指定番号は数字で入力して下さい！";
}

}else{
      //新規投稿機能実行判定
      if(strval($name) != '' && strval(comment) != ''){
            //名前とコメント入力あり
            $remove_num = '';  //新投稿なら、消去機能を実行させない

         if($password != ''){
             //パスワード入力あり
             //新投稿機能実行

            $num++;  //投稿番号は行数+1

    //テーブルにデータを入力するSQLの準備
$sql = $pdo->prepare("INSERT INTO abcd(num,name,comment,time,password) VALUES(:num,:name,:comment,:time,:password)");

//パラメーターの変数を設定
$sql -> bindParam(':num',$id,PDO::PARAM_INT);
$sql -> bindParam(':name',$name,PDO::PARAM_STR);
$sql -> bindParam(':comment',$comment,PDO::PARAM_STR);
$sql -> bindParam(':time',$time,PDO::PARAM_STR);
$sql -> bindParam(':password',$password,PDO::PARAM_STR);

//変数に値をセット
$time = date("Y/m/d H:i:s");
$sql -> execute();
$message = "新規投稿しました！";

}else{
      //パスワード入力無し
     $message = "パスワードを入力して下さい!";
     $edit_name = $name;
     $edit_comment = $comment;
}
}
}

//消去機能実行判定
if(strval($remove_num) != ''){
     //消去指定番号入力あり

    $edit_flag2 = $_POST['edit_flag'];  //編集と消去が同時に実行されるのを防ぐため

    if(strval($edit_flag2) == '' && strval($edit_num) == ''){
         //編集フラグが空白かつ編集番号が空白
         if(is_numeric($remove_num)){
               //消去指定番号が数字
               if($password != ''){
                    //パスワード入力あり

                    //パスワードが正しいか調べる
                   
                  //指定idのパスワード取り出し
                $sql = "SELECT * FROM abcd WHERE num = $remove_num";

                try{
                $results = $pdo -> query($sql);

                 }catch(Exception $e){
                  var_dump($e->getMessage());
                 }

                 foreach($results as $row){
                       $base_password = $row['password'];
                 }
                if($base_password == $password){
                //入力パスワードが正しい
                //消去機能

                    //行数を取得
                   

                   //行を消去するsql発行
                   $sql = "delete from abcd where num = $remove_num";

                   $result = $pdo -> query($sql);

                //投稿番号を振り直す機能
                //投稿番号を更新するSQLの準備
$sql = $pdo -> prepare("update abcd set num = :new_num where num = :old_num");

$new_num = 1;  //新しい投稿番号
$old_num = 1;  //元々の投稿番号

while($new_num < $num){
 //データベースの行数分繰り返す
   if($old_num == $remove_num){
      //消去した行は投稿番号を更新しない
     $old_num++;
   }
   
   //パラメーターの変数を設定
$sql -> bindParam(':new_num',$new_id,PDO::PARAM_INT);
$sql -> bindParam(':old_num',$old_id,PDO::PARAM_INT);

$sql -> execute();

//id更新
$new_num++;
$old_num++;
}
$message = "$remove_num"."番目の投稿を削除しました!";

//消去したら行数は減る
$id--;

}else{
      //パスワードが正しくない
      $message = "パスワードが間違っています!";
}

}else{
      /*パスワード入力なし*/
      $message = "パスワードが入力されていません!";
      $input_remove = $remove_num;
}

}else{
      //消去番号が数字以外の時
      $message = "消去指定番号は数字で入力して下さい!";
}
}
}
?>


<!DOCTYPE html>
<html lang = "en","ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="mission_4.css">
    <title>mission4</title>
</head>
<body>
      <!--見出し-->
<div class="midasi">掲示版(abcd)</div>
<hr class="midasi_line">

<?php
//機能を使用した時のメッセージ表示
if($message != ""){
     //メッセージがあれば表示
     echo "$message";
     $message="";
}
?>



<?php
//データベースのデータ表示機能

//テーブルからデータを取り出すsql
$sql = "SELECT *FROM abcd ORDER BY num ASC";

try{
    $results = $pdo -> query($sql);
    $i=1;  //繰り返し変数

    foreach($results as $row){
        //投稿番号、名前、コメント、日時を表示
echo $row['num'].'. '; 
echo $row['edit'].':'; 
echo $row['name'].':';
echo $row['comment'].':';
echo $row['time'];

//改行
echo "<br>";
//線を引く
echo "<hr>";
}    

}catch(Exception $e){
                  var_dump($e->getMessage());
           }
?>



<!--入力フォーム-->
<form action = "./mission_4-1.php" method = "post">


名前:<input type = "text" name = "name" value = "<?php echo $edit_name; ?>"><br>

コメント:<textarea rows="5" cols="40" wrap = "hand" name = "comment"><?php echo $edit_comment; ?></textarea><br>

パスワード:<input type = "password" name = "password" value="<?php echo $edit_pass;?>">

<!--送信ボタン-->
<input type = "submit" value = "送信"><br>

編集対象番号:<input type = "text" type = "text" name = "edit_num">
消去対象番号:<input type = "text" name = "remove_num" value = "<?php echo $input_remove; ?>">


<!--編集モードフラグ-->
<input type = "hidden" name = "edit_flag" value = "<?php echo $edit_flag; ?>"> 


</form>

</body>
</html>