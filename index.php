<?php
session_start();
require('dbconnection.php');


if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
    // ログインしている
    $_SESSION['time'] = time();

    $members = $db->prepare('SELECT * FROM members WHERE id=?');
    $members->execute(array($_SESSION['id']));
    $member = $members->fetch();
} else {
    header('Location: login.php');
    exit();
}


//投稿登録
if (!empty($_POST)) {
    if ($_POST['message'] != '') {
        $reply_post_id = $_POST['reply_post_id'] != '' ? $_POST['reply_post_id'] : null;
        var_dump($reply_post_id);
        $message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, reply_post_id=?, created=NOW()');
        $message->execute(array(
            $member['id'],
            $_POST['message'],
            $reply_post_id,
        ));

        header('Location: index.php');
        exit();
    }
}

//投稿取得
$posts = $db->query('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC');

if (isset($_REQUEST['res'])) {

    $response = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=? ORDER BY p.created DESC');
    $response->execute(array($_REQUEST['res']));
    $table = $response->fetch();
    $message = '@' . $table['name'] . ' ' . $table['message'];
}


?>

    <div>
        <a href="logout.php">ログアウト</a>
    </div>
    <form action="" method="post">
        <dl>
            <dt><?php echo htmlspecialchars($member['name'], ENT_QUOTES) ?>さんメッセージどうぞ</dt>
            <dt>メッセージ</dt>
            <dd>
                <textarea name="message" id="" cols="50"
                          rows="5"><?php if (isset($message)): echo htmlspecialchars($message, ENT_QUOTES); endif ?></textarea>
                <input type="hidden" name="reply_post_id"
                       value="<?php if (isset($_REQUEST['res'])): echo htmlspecialchars($_REQUEST['res'], ENT_QUOTES); endif ?>">
            </dd>
        </dl>
        <div>
            <input type="submit" value="投稿する">
        </div>
    </form>

<?php foreach ($posts as $post): ?>

    <div style="display: flex">
        <img src="member_picture/<?php echo htmlspecialchars($post['picture'], ENT_QUOTES) ?>" alt="" width="48"
             height="48">
        <p><?php echo htmlspecialchars($post['message'], ENT_QUOTES) ?></p>
        <p>(<?php echo htmlspecialchars($post['name'], ENT_QUOTES) ?>)</p>
        [<a href="index.php?res=<?php echo htmlspecialchars($post['id'], ENT_QUOTES) ?>">Re</a>]
    </div>
    <p><?php echo htmlspecialchars($post['created'], ENT_QUOTES) ?></p>
    <?php if ($_SESSION['id'] == $post['member_id']): ?>
        [<a href="delete.php?id=<?php echo htmlspecialchars($post['id'], ENT_QUOTES) ?>" style="color: red">削除</a>]
    <?php endif ?>
<?php endforeach ?>




