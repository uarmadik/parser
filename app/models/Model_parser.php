<?php

namespace app\models;
use phpQuery;

class Model_parser
{

    protected $user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36';

    public function getWebPage($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);

        $content = curl_exec($ch);
        $err     = curl_errno($ch);
        $errmsg  = curl_error($ch);
        $header  = curl_getinfo($ch);

        curl_close($ch);

        $header['errno'] = $err;
        $header['errmsg'] = $errmsg;
        $header['content'] = $content;

        if (($header['errno'] != 0) || ($header['http_code'] != 200)) {
            exit($header['errmsg']);
        } else {
            return $header['content'];
        }

    }

    public function getPageElementLinks($page, $selector)
    {
        $doc = phpQuery::newDocument($page);

        $elements = $doc->find($selector)->elements;

        if (empty($elements)) {

            throw new \Exception('Did not find link by selector ' . $selector);
        }

        $countElements = count($elements);

        $links = [];

        for ($i = 0; $i < $countElements; $i++) {

            $article = pq($elements[$i]);
            $links[$i]['header'] = $article->find('.post__title a.post__title_link')->text();
            $links[$i]['link'] = $article->find('.post__title a.post__title_link')->attr('href');
        }

        return $links;
    }

    public function getArticleContent($page, $selector, $selectorImg = null)
    {
        $doc = phpQuery::newDocument($page);

        $header = $doc->find('.post__title-text')->elements;
        $headerTag = pq($header);
        $content['header'] = $headerTag->text();

        $element = $doc->find($selector)->elements;

        if (isset($selectorImg)) {

            $images = $doc->find($selectorImg)->elements;

            foreach ($images as $image) {

                $image = pq($image);
                $imageUrl = $image->attr('src');
                $content['images'][] = $imageUrl;
            }
        }

        $article = pq($element);
        $content['text'] = $article->text();
        return $content;

    }

}