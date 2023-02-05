<?php
    require ('dbconnection.php');

session_start();

if(isset($_COOKIE['email'])){
    if($_COOKIE['email'] != ''){
        $_POST['email'] = $_COOKIE['email'];
        $_POST['password'] = $_COOKIE['password'];
        $_POST['save'] = 'on';
    }
}

if(!empty($_POST)){
    if($_POST['email'] != '' && $_POST['password'] != ''){
        $login = $db->prepare('SELECT * FROM members WHERE email=? AND password=?');
        $login->execute(array(
            $_POST['email'],
            sha1($_POST['password']),
        ));
        $member = $login->fetch();

        if($member){
            //ログイン成功
            $_SESSION['id'] = $member['id'];
            $_SESSION['time'] = time();

            // ログイン情報記録
            if($_POST['save'] == 'on'){
                setcookie('email', $_POST['email'], time()+60*60*24*14);
                setcookie('password', $_POST['password'], time()+60*60*24*14);
            }

            header('Location: index.php'); exit();
        } else {
            $error['login'] = 'failed';
        }
    } else {
        $error['login'] = 'blank';
    }
}
?>

<div id="lead">
    <p>メールアドレスとパスを記入して</p>
    <p>入会はこちら</p>
    <p>&raquo;<a href="join/">入会</a></p>
</div>
<form action="" method="post">
    <dl>
        <dt>メールアドレス</dt>
        <dd>
            <input type="text" name="email" size="35" value="<?php if(isset($_POST['email'])): echo htmlspecialchars($_POST['email'], ENT_QUOTES); endif ?>">
        </dd>
        <?php if (isset($error['login'])): ?>
            <?php if ($error['login'] == 'blank'): ?>
                <p class="error">※ メールアドレスとパスを入力してください</p>
            <?php endif ?>
        <?php endif ?>
        <dt>パス</dt>
        <dd>
            <input type="password" name="password" size="35" value="<?php if(isset($_POST['password'])): echo htmlspecialchars($_POST['password'], ENT_QUOTES); endif ?>">
        </dd>
        <?php if (isset($error['login'])): ?>
            <?php if ($error['login'] == 'failed'): ?>
                <p class="error">※ 失敗しました</p>
            <?php endif ?>
        <?php endif ?>
        <dt>ログイン情報の記録</dt>
        <dd>
            <input id="save" type="checkbox" name="save" value="on">
            <label for="save">次回から自動的にログイン</label>
        </dd>
    </dl>
    <div>
        <input type="submit" value="ログインする">
    </div>
</form>
