<?php
/**
 * Created by PhpStorm.
 * User: lango
 * Date: 1/31/18
 * Time: 9:10 PM
 */

namespace App\CoreModule\Model;

use App\Model\BaseManager;
use Nette\Database\Table\IRow;
use Nette\Database\Table\Selection;
use Nette\Utils\ArrayHash;

/**
 * Class ArticleManager
 * @package App\CoreModule\Model
 */
class ArticleManager extends BaseManager
{
    const TABLE_NAME = 'article';
    const COLUMN_ID = 'article_id';
    const COLUMN_URL = 'url';


    /** @return Selection list of articles */
    public function getArticles()
    {
        return $this->context->table(self::TABLE_NAME)->order(self::COLUMN_ID . ' DESC');
    }

    /**
     * @param $url
     * @return array article with comments
     */
    public function getArticle($url)
    {
        $article = $this->context->table(self::TABLE_NAME)->where(self::COLUMN_URL, $url)->fetch();
        $comments = $article->related('comment')->order('timestamp');
        return ['article' => $article, 'comments' => $comments];
    }

    public function getUserArticle(int $id)
    {
        return $this->context->table(self::TABLE_NAME)->where('user_id', $id)->fetchAll();
    }

    /**
     * @param string $url Article url
     * @return float star rating
     */
    public function getArticleRating($url): float
    {
        $rating  = $this->context->table(self::TABLE_NAME)->select('rating')->where('url', $url);
        $rating = explode(',', $rating);
        return $rating[0];
    }

    /**
     * @param string $url URL of article
     * @param float $rating Rating of article (0 to 5 - star rating; steps by 0.5)
     */
    public function rateArticle($url, $rating)
    {
        $article = $this->getArticle($url)[0];
        if(!$article->rating) {
            $currentRating = explode(',', $article->rating);
            list($currentRating, $count) = $currentRating;
            $added = $count++;

            // Rating is 4.5 by 12 people - 4.5,12; new rating is 4 stars. Then: 4.5*12 + 4 / 13(count of resulting ratings)
            //equals 4.462 stars which will be rounded to 4.5 :)
            // Need to get from average to sum of arithmetic avg. Then value of new rating is added.
            // Dividing by new sum of all ratings and getting new rating
            $article->rating = (($currentRating * $count + $rating) / $added) . ",{$added}";
        }
        else{
            $article->rating = "{$rating},1";
        }
        $this->context->table('article')->where('article_id', $article->article_id)->update($article);
    }

    /**
     * @param array|ArrayHash article
     */
    public function saveArticle($article)
    {
        if(!$article[self::COLUMN_ID]){
            $article['article_id'] = null;
            $this->context->table(self::TABLE_NAME)->insert($article);
        }else{
            $this->context->table(self::TABLE_NAME)->where(self::COLUMN_ID, $article[self::COLUMN_ID])->update($article);
        }
    }

    /**
     * @param string $url
     */
    public function removeArticle($url)
    {
        $this->context->table(self::TABLE_NAME)->where(self::COLUMN_URL, $url)->delete();
    }


}