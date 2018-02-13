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
     * @return bool|mixed|IRow article
     */
    public function getArticle($url)
    {
        return $this->context->table(self::TABLE_NAME)->where(self::COLUMN_URL, $url)->fetch();
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