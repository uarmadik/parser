<?php

namespace app\controllers;

use app\models\Model_article;
use app\models\Model_parser;


class Controller_parser
{
    protected $url = 'https://habrahabr.ru/all/';
    protected $selector = '.content-list__item_post .post';
    protected $quantityParseElements = 5;

    public function index()
    {
        $qty = $this->quantityParseElements - 1;
        $selector = $this->selector . ':lt(' . $qty. ')';

        $model = new Model_parser();
        $data = $model->getWebPage($this->url);

        if (empty($data)) {

            throw new \Exception('The page ' . $this->url . "/n did not parse!");
        }

        $articleLinks = $model->getPageElementLinks($data, $selector);

        if (empty($articleLinks)) {

            throw new \Exception('Can not get link from page ' . $this->url);
        }

        // переход по ссылкам

        $modelArticle = new Model_article();

        foreach ($articleLinks as $article) {

            $url = $article['link'];
            $page = $model->getWebPage($url);
            $pageContent = $model->getArticleContent($page, '.post__text', '.post__text img');

            $modelArticle->saveArticle($pageContent);

        }
    }
}