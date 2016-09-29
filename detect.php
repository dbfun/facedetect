<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>Поиск лиц</title>
  </head>
  <body>
<?php

class FaceDetect {

  private $src, $dest, $size = 60, $minSize = 20, $tmpDir = 'tmp', $uri = '/face/', $images;
  public function __construct() {
    $this->tmpDir = trim($this->tmpDir, '/') . '/';
    $this->uri = '/' . trim($this->uri, '/') . '/';
  }

  public function __destruct() {
    if(isset($this->src) && file_exists($this->src)) unlink($this->src);
  }

  public function selfrun() {
    try {
      $this->upload();
      $this->detectFace();
      $this->showImage();
    } catch (Exception $e) {
      $uri = $this->uri . '?msg=' . urlencode($e->GetMessage());
      header('Location: ' . $uri);
      die();
    }
  }

  private function upload() {
    $message = 'Ошибка загрузки файла';
    if(!isset($_FILES['image']) || !is_array($_FILES['image'])) throw new Exception($message, 1);

    switch($_FILES['image']['error']) {
      case UPLOAD_ERR_OK:
        break;
      case UPLOAD_ERR_INI_SIZE:
      case UPLOAD_ERR_FORM_SIZE:
        throw new Exception($message . ' - файл слишком большой (лимит '.get_max_upload().' байт).');
      case UPLOAD_ERR_PARTIAL:
        throw new Exception($message . ' - файл загружен частично.');
      case UPLOAD_ERR_NO_FILE:
        throw new Exception($message . ' - файл пустой.');
      default:
        throw new Exception($message . ' - внутренняя ошибка #'.$_FILES['image']['error']);
    }
    if(!is_uploaded_file($_FILES['image']['tmp_name'])) {
      throw new Exception('Ошибка загрузки файла - неизвестная причина.');
    }
    $this->src = tempnam($this->tmpDir, 'fd-');
    if(!move_uploaded_file($_FILES['image']['tmp_name'], $this->src)) { // No error supporession so we can see the underlying error.
      throw new Exception('Невозможно переместить загруженный файл');
    }

  }

  private function detectFace() {
    $destExt = pathinfo($_FILES['image']['name'], PATHINFO_FILENAME) . 'x' . $this->size;
    $this->destExt = $this->tmpDir.$destExt;
    $size =& $_REQUEST['size'];
    if(isset($size)) {
      $size = (int)$size;
      $this->size = $size > $this->minSize ? $size : $this->minSize;
    }
    $cmd = "./fd.sh '" . escapeshellcmd($this->src) . "' '" . escapeshellcmd($this->destExt) . "' {$this->size}";
    $output = shell_exec($cmd);
    if(preg_match_all('~^DEST FILE: (.*?)$~m', $output, $m)) {
      $this->images = $m[1];
      return;
    }
    throw new Exception("Не удалось найти лицо", 1);
  }

  private function showImage() {
    foreach ($this->images as $image) {
      $binData = base64_encode(file_get_contents($image));
      unlink($image);
      ?>
      <img src="data:image/jpg;base64,<?=$binData;?>" width="<?=$this->size;?>" height="<?=$this->size;?>" alt="face" />
    <?php
    } ?>
    <div>
      <a href="<?=$this->uri;?>">Выбрать другое изображение</a>
    </div>
    <?php

  }

}

$FaceDetect = new FaceDetect();
$FaceDetect->selfrun();

?>

</body>
</html>