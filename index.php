<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>Поиск лиц</title>
  </head>
  <style>
    .msg-alert { margin: 10px 0; padding: 20px 10px; background: #ffd0d0; border-radius: 5px; border: 1px solid #ff8989; }
  </style>
  <body>
    <?php
    $msg = $_REQUEST['msg'];
    if(isset($msg) && $msg) { ?>

    <div class="msg-alert">
      <?=htmlspecialchars($msg);?>
    </div>

    <?php } ?>
    <p>Поиск лиц на фотографии</p>
    <form action="/face/detect.php" method="post" enctype="multipart/form-data">
      <input type="file" name="image" />
      <input type="text" name="size" value="60" />
      <input type="submit" value="Отправить" />
    </form>
  </body>
</html>