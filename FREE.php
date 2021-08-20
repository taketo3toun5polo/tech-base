<?php
session_start();
    $dsn = 'mysql:dbname=*********';//データベースに接続する
    $user = '*********';
    $password = '***********';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
?>
<html lang = "ja">
<head>
    <meta charset = "UTF-8">
    <title>Mission_5-1</title>
</head>
<body>　　<!--投稿・削除・編集フォーム-->
<form action = "" method = "post">

<?php
    $f=0;//←指標　通常は「$f=0」、編集番号があるときは例外、後記述 「$f=1」
    if(!empty($_POST["edit"])){
        if($_POST["edit"]!=""){//変動対象番号がある場合
            $id = $_POST["edit"];
            $pass = $_POST["edit_password"];
            $sql = 'SELECT*FROM techbase';//SELECT・・・入力したデータレコード（行）を抽出し、表示する
            $stmt = $pdo->query($sql);//変動値がないため、queryを実行
            $results = $stmt->fetchAll();//キーを連番に、値をカラム毎の配列で取得する
            foreach ($results as $row){
                if($row['id']==$id &&$row['password']==$pass){
                    $_SESSION["num"] = $row['id'];
                    $edit_name = $row['name'];
                    $edit_comment = $row['comment'];
                    $new_password = $row['password'];
                    $f=1;//疑問ポイント1⃣
                }
                            
            }
        }
    }
?>  
    <!--例外（＝編集番号がある場合）の投稿フォーム-->
    <?php if($f==1 && !empty($_POST["edit"])): ?> 
        <p>名前：<input type = "text" name = "edit_name" value = "<?php echo $edit_name; ?>"></p>
        <p>コメント：<input type = "text" name = "edit_comment" value = "<?php echo $edit_comment; ?>"></p>
        <p>パスワード：<input type = "text" name = "new_password" value="<?php echo $new_password; ?>"></p>
        <input type = "submit" name = "edit_submit" value = "送信"><br>
    <?php endif; ?>    
    <!--通常の投稿フォーム-->
    <?php  if($f==0): ?>
    <p>名前：<input type = "text" name = "name"></p>
        <p>コメント：<input type = "text" name = "comment"></p>
        <p>パスワード：<input type = "text" name = "password"></p>
        <input type = "submit" name = "submit" value = "送信"><br>
    <?php endif; ?>
    <!--通常の削除・編集フォーム-->
        <p>削除番号：<input type = "number" name = "delete" ><br>
          パスワード：<input type = "text" name = "delete_password">
        <input type = "submit" name = "delete_buttun" value = "削除"></p>
        <p>編集番号：<input type = "number" name = "edit" ><br>
        パスワード：<input type = "text" name = "edit_password">
        <input type = "submit" name = "edit_buttun" value = "編集"></p>
    </form>

<?php
//投稿動作
if(!empty($_POST["name"]) && !empty($_POST["comment"])  && !empty($_POST["password"])){
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));//1....DBに接続
    $sql="CREATE TABLE IF NOT EXISTS techbase"//2....テーブルを作成
    ."("
    ."id INT AUTO_INCREMENT PRIMARY KEY,"
    ."name char(32),"
    ."comment TEXT,"
    ."password char(10),"
    ."day char(200)"
    .");";
    $stmt = $pdo->query($sql);//3.....変動値がないため、queryでSQLセットし、SQLを実行する.「DBから「tech_base」というテーブルへ操作したよ」という意味

    $sql = $pdo->prepare("INSERT INTO techbase (name, comment, password, day) VALUES (:name, :comment, :password, :day)");//1...実行したいSQL文をセット
    $sql->bindParam(':name',$name, PDO::PARAM_STR);//2...SQLに対してパラメータをセットする(CASE1⃣->:nameというパラメータ)（以下同文）
    $sql->bindParam(':comment',$comment, PDO::PARAM_STR);
    $sql->bindParam(':password',$password, PDO::PARAM_STR);
    $sql->bindParam(':day', $day, PDO::PARAM_STR);
    $name = $_POST["name"];//パラメータに投稿フォームの値を代入する（以下同文）
    $comment = $_POST["comment"];
    $password = $_POST["password"];
    $day = date("Y-m-d H-i-s");
    $sql -> execute();//3...SQLを実行
}
//削除動作
if(!empty($_POST["delete"]) && !empty($_POST["delete_password"])){
    if($_POST["delete"] != "" && $_POST["delete_password"] != ""){
        $id = $_POST["delete"];
        $password=$_POST["delete_password"];
        $sql = 'delete from techbase where id=:id && password=:password';
        $sql = $pdo->prepare($sql);
        $sql->bindParam(':id', $id, PDO::PARAM_INT);
        $sql->bindParam(':password', $password, PDO::PARAM_STR);
        $sql->execute();
    }
}
//編集動作
if(!empty($_POST["edit_name"]) && !empty($_POST["edit_comment"])){
    if($_POST["edit_name"] != "" && $_POST["edit_comment"]){//編集時に、名前とコメントと編集用パスワードがある場合
        $id = $_SESSION["num"];//編集番号があるときにその番号に合致した値を初期値として表示させる動作を行った際に、SESSION["num"]として保存した、編集対象番号
        $name = $_POST["edit_name"];
        $comment = $_POST["edit_comment"];
        $password = $_POST["new_password"];
        $day = date("Y-m-d D-i-s");
        $sql = 'UPDATE techbase SET name=:name,comment=:comment,password=:password,day=:day WHERE id=:id'; //DBから「tech_base」というテーブルにアクセスし、WHERE旬を満たす値を更新（＝更新データ）と入れ替えるよ」という意味
        $stmt = $pdo->prepare($sql);//1...実行したいSQL文をセット
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);//2...SQLに対してパラメータをセット（以下同文）
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':day', $day, PDO::PARAM_STR);
        $stmt->execute();//3...SQLを実行
    }
}echo "<hr>";//DBに保存されている投稿内容をブラウザに表示させる動作（ブラウザにechoさせる動作）
$sql = 'SELECT * FROM techbase';//SELECT・・・入力したデータレコード（行）を抽出し、表示する
$stmt = $pdo->query($sql);//変動値がないため、queryを実行
$results = $stmt->fetchAll();//キーを連番に、値をカラム毎の配列で取得する
foreach ($results as $row){
    echo $row['id'].' ';
    echo $row['name'].' ';
    echo $row['comment'].' ';
    echo $row['day'].'<br>'; 
    echo "<hr>";
}
?>
</body>
</html>