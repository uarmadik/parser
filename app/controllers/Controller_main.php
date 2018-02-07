<?php


namespace app\controllers;

use app\core\View;
use app\models\Model_article;


class Controller_main
{
    protected $perPage = 3;

    public function index()
    {
        $modelArticle = new Model_article();
        $articles = $modelArticle->getArticles(1, $this->perPage);

        $pages = $this->countPages($modelArticle);

        $this->page();

    }

    public function page($currentPage = 1)
    {
        if (!is_numeric($currentPage)) {

            return false;
        }

        $from = ($currentPage * $this->perPage) - $this->perPage;

        $modelArticle = new Model_article();
        $articles = $modelArticle->getArticles($from, $this->perPage);
        $pages = $this->countPages($modelArticle);

        $view = new View();
        $view->generate('main_view', [
            'articles' => $articles,
            'pages' => $pages,
            'currentPage' => $currentPage
        ]);

    }

    public function article($id)
    {
        if (!is_numeric($id)) {

            return false;
        }

        $modelArticle = new Model_article();
        $article = $modelArticle->getArticle($id);
        $images = $modelArticle->getImages($id);

        $view = new View();
        $view->generate('article_view', [
            'article' => $article,
            'images' => $images
        ]);
    }

    protected function countPages($modelArticle = null)
    {
        if (empty($modelArticle)) {

            $modelArticle = new Model_article();
        }

        $countRows = $modelArticle->getCountRows();
        $countPages = ceil($countRows / $this->perPage);

        return $countPages;
    }
}