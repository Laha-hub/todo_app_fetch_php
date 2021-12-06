<?php

namespace MyApp;

class Todo
{

    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        Token::create();
    }

    public function processPost()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Token::validate();

            $action = filter_input(INPUT_GET, 'action');

            switch ($action) {
                case 'add':
                    $id = $this->add();
                    header('Content-Type: application/json');
                    echo json_encode(['id' => $id]);
                    break;
                case 'toggle':
                    $isDone = $this->toggle();
                    header('Content-Type: application/json');
                    echo json_encode(['is_done' => $isDone]);
                    break;
                case 'delete':
                    $this->delete();
                    break;
                case 'purge':
                    $this->purge();
                    break;
                default:
                    exit;
            }

            exit;
        }
    }

    // *** Create
    private function add()
    {
        $title = trim(filter_input(INPUT_POST, 'title'));
        if ( $title === '') {
            return;
        }

        $stmt = $this->pdo->prepare('INSERT INTO todos (title) VALUES (:title)');
        // bindValue()で型指定
        $stmt->bindValue(':title', $title, \PDO::PARAM_STR);
        $stmt->execute();
        // デフォルトの返り値がstringなのでintにキャスト
        return (int) $this->pdo->lastInsertId();
    }

    // *** Update
    private function toggle()
    {
        $id = filter_input(INPUT_POST, 'id');
        if (empty($id)) {
            return;
        }

        // 該当idの有無をチェック
        $stmt = $this->pdo->prepare("SELECT * FROM todos WHERE id = :id");
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $todo = $stmt->fetch();
        if (empty($todo)) {
            // JSのfetchにエラーを返す
            header('HTTP', true, 404);
            exit;
        }

        // is_doneの切替なので、"NOT is_done"
        $stmt = $this->pdo->prepare("UPDATE todos SET is_done = NOT is_done WHERE id = :id ");
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        // $todoはis_done UPDATE前の状態（DB最新状態ではない）を保持しているので、否定（!）処理を行う。
        // MySQLの真偽値は0, 1管理であり、JSで使い勝手をよくするため、booleanにキャストしておく。
        return (boolean) !$todo->is_done;
    }

    // *** Delete
    private function delete()
    {
        $id = filter_input(INPUT_POST, 'id');
        if (empty($id)) {
            return;
        }

        $stmt = $this->pdo->prepare("DELETE FROM todos WHERE id = :id ");
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    // *** Purge
    private function purge()
    {

        $this->pdo->query("DELETE FROM todos WHERE is_done = 1");

    }

    // Read
    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM todos ORDER BY id DESC");
        $todos = $stmt->fetchAll();
        return $todos;
    }

}
