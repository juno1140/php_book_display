<?php

session_start();

if (!empty($_POST)) {
    if ($_POST['name'] == '') {
        $error['name'] = 'blank';
    }
    if ($_POST['email'] == '') {
        $error['email'] = 'blank';
    }
    if (strlen($_POST['password']) < 4) {
        $error['password'] = 'length';
    }
    if ($_POST['password'] == '') {
        $error['password'] = 'blank';
    }
    $fileName = $_FILES['image']['name'];

    if (!empty($fileName)) {
        $ext = substr($fileName, -3);
        if ($ext != 'jpg' && $ext != 'gif') {
            $error['image'] = 'type';
        }
    }

    if (empty($error)) {
        // 画像アップロード
        $image = date('YmdHis') . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], '../member_picture/' . $image);

        $_SESSION['join'] = $_POST;
        $_SESSION['join']['image'] = $image;
        header('Location: check.php');
        exit();
    }

}

if(isset($_REQUEST['action'])){
    if($_REQUEST['action'] === 'rewrite'){
        $_POST = $_SESSION['join'];
        $error['rewrite'] = true;
    }
}

?>


<p>次のフォームに必要事項をご記入ください</p>
<form action="" method="post" enctype="multipart/form-data">
    <dl>
        <dt>ニックネーム<span class="required">必須</span></dt>
        <dd><input type="text" name="name" size="35" maxlength="255"
                   value="<?php if(isset($_POST['name'])): echo htmlspecialchars($_POST['name'], ENT_QUOTES); endif ?>">
        </dd>
        <?php if (isset($error['name'])): ?>
            <?php if ($error['name'] == 'blank'): ?>
                <p class="error">※ ニックネームを入力してください</p>
            <?php endif ?>
        <?php endif ?>
        <dt>メールアドレス<span class="required">必須</span></dt>
        <dd><input type="email" name="email" size="35" maxlength="255"
                   value="<?php if(isset($_POST['email'])): echo htmlspecialchars($_POST['email'], ENT_QUOTES); endif ?>">
        </dd>
        <?php if (isset($error['email'])): ?>
            <?php if ($error['email'] == 'blank'): ?>
                <p class="error">※ メールアドレスを入力してください</p>
            <?php endif ?>
        <?php endif ?>
        <dt>パスワード<span class="required">必須</span></dt>
        <dd><input type="password" name="password" size="10" maxlength="20"
                   value="<?php if(isset($_POST['password'])): echo htmlspecialchars($_POST['password'], ENT_QUOTES); endif ?>">
        </dd>
        <?php if (isset($error['password'])): ?>
            <?php if ($error['password'] == 'blank'): ?>
                <p class="error">※ passwordを入力してください</p>
            <?php endif ?>
            <?php if ($error['password'] == 'length'): ?>
                <p class="error">※ passwordは4文字以上で入力してください</p>
            <?php endif ?>
        <?php endif ?>
        <dt>写真など</dt>
        <dd>
            <input type="file" name="image" size="35">
            <?php if (isset($error['image'])): ?>
                <?php if ($error['image'] == 'type'): ?>
                    <p class="error">※ 写真などは「.gif」または「.jpg」の画像をしていしてください</p>
                <?php endif ?>
            <?php endif ?>
            <?php if (!empty($error)): ?>
                <p class="error">※ 再度画像を設定してください</p>
            <?php endif ?>
        </dd>

        <div>
            <input type="submit" value="入力内容を確認する">
        </div>
    </dl>
</form>
