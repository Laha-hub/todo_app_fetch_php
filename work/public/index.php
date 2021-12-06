<?php

// 相対パスでの記述は避ける(__DIR__で絶対パス化)
require_once(__DIR__ . '/../app/config.php');

use MyApp\Utils;
use MyApp\Database;
use MyApp\Todo;

$pdo = Database::getInstance();

$todo = new Todo($pdo);
$todo->processPost();
$todos = $todo->getAll();

?>


<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>My Todos</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <main data-token="<?= Utils::h($_SESSION['token']); ?>">
    <header>
      <h1>Todos</h1>
      <span class="purge">Purge</span>
    </header>

    <form>
      <input type="text" name="title" id="" placeholder="Type new todo.">
    </form>

    <ul>
      <?php foreach ($todos as $todo) : ?>
      <li data-id="<?= Utils::h($todo->id); ?>">
        <input type="checkbox" <?= $todo->is_done ? 'checked' : ''; ?> />
        <span><?= Utils::h($todo->title); ?></span>
        <span class="delete">x</span>
      </li>
      <?php endforeach; ?>
    </ul>

  </main>
  <script src="js/main.js"></script>

</body>
</html>
