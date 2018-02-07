<?php
/**
 * Created by PhpStorm.
 * User: Ihor-PC
 * Date: 06.02.2018
 * Time: 13:29
 */

namespace app\models;

use app\core\Model;
use PDO;

class Model_article extends Model
{
    public function getArticles($limit_from, $per_page)
    {
        $connection = $this->getDbConnection();

        $stmt = $connection->prepare('SELECT `id`, `header`, SUBSTRING(`text`,1,300) AS `trimed_content` FROM `habr_articles` LIMIT ?, ?');
        $stmt->bindValue(1, $limit_from, PDO::PARAM_INT);
        $stmt->bindValue(2, $per_page, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return (!empty($data)) ? $data : false;

    }

    public function getArticle($id)
    {
        $connection = $this->getDbConnection();

        $stmt = $connection->prepare('SELECT `id`, `header`, `text` FROM test_db.`habr_articles` WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getImages($articleId)
    {
        $connection = $this->getDbConnection();

        $stmt = $connection->prepare('SELECT file_name FROM test_db.habr_article_images WHERE article_id = :articleId');
        $stmt->execute(['articleId' => $articleId]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function saveArticle($content)
    {
        $connection = $this->getDbConnection();
        $stmt = $connection->prepare('INSERT INTO test_db.habr_articles VALUES (NULL, :header, :text)');
        $stmt->execute([
            'header' => $content['header'],
            'text' => $content['text']
        ]);

        if (isset($content['images'])) {

            $articleId = $connection->lastInsertId();
            $this->uploadImages($content['images'], $connection, $articleId);

        }
    }
/*
    public function saveArticles($contents)
    {
        $queryValuesString = '';
        $count = count($contents);
        for ($i = 0; $i < $count; $i++) {
            if($i == ($count - 1)) {
                $queryValuesString .= '(NULL, ?, ?, NULL)';
            } else {
                $queryValuesString .= '(NULL, ?, ?, NULL),';
            }

        }

        $values = [];
        foreach ($contents as $article)  {
            $values[] = $article['header'];
            $values[] = $article['content'];
        }


        $connection = $this->getDbConnection();
        $sql = "INSERT INTO test_db.habr_articles VALUES $queryValuesString ";
        $stmt = $connection->prepare($sql);
        $stmt->execute($values);
    }
*/
    protected function uploadImages(array $imageUrls, $connection, $articleId)
    {
        if (empty($imageUrls)) {

            return false;
        }

        $uploadPath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/img/';

        foreach ($imageUrls as $url) {

            $fileName = basename($url);
            if (copy($url, $uploadPath . $fileName)) {

                $stmt = $connection->prepare('INSERT INTO test_db.habr_article_images VALUES (NULL, :fileName, :articleId)');
                $stmt->execute([
                    'fileName' => $fileName,
                    'articleId' => $articleId
                ]);
            }
        }
    }

    public function getCountRows()
    {
        $connection = $this->getDbConnection();
        $count = $connection->query("SELECT COUNT(*) as count FROM test_db.habr_articles")->fetchColumn();

        return $count;

    }
}
