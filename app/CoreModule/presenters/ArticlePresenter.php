<?php
/**
 * Created by PhpStorm.
 * User: lango
 * Date: 2/1/18
 * Time: 12:39 AM
 */

namespace App\CoreModule\Presenters;

use App\CoreModule\Model\ArticleManager;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\ConstraintViolationException;
use Nette\Utils\ArrayHash;

/** @package App\CoreModule\Presenters */
class ArticlePresenter extends BaseCorePresenter
{
    const DEFAULT_URL = 'home';

    protected $articleManager;

    public function __construct(ArticleManager $articleManager)
    {
        parent::__construct();
        $this->articleManager = $articleManager;
    }

    /**
     * Loads and render the article into a template according to article url
     * @param string $url article url
     * @throws BadRequestException Throws exception if article does not exists
     */
    public function renderDefault(string $url = null)
    {
        if(!$url){
            $url = self::DEFAULT_URL;
        }

        $articleData = $this->articleManager->getArticle($url);
        list('article' => $article, 'comments' => $comments) = $articleData;

        if(!$article){
            throw new BadRequestException;
        }

        $this->template->article = $article;
        $this->template->comments = $comments;
    }

    /** Renders list of articles */
    public function renderList()
    {
        $this->template->articles = $this->articleManager->getArticles();
    }

    /**
     * @param string $url
     * @throws \Nette\Application\AbortException
     */
    public function actionRemove(string $url)
    {
        $this->articleManager->removeArticle($url);
        $this->flashMessage('Article was successfully removed.');
        $this->redirect(':Core:Article:List');
    }

    /** @param string $url */
    public function actionEditor(string $url = null)
    {
        if ($url){
            ($article = $this->articleManager->getArticle($url)) ?
                $this['editorForm']->setDefaults($article) :
                $this->flashMessage('Article was not found.');
        }
    }


    /**
     * @param string $url Url of article
     * @param float $rating Rating to be count
     * @throws BadRequestException Throws exception if not AJAX
     */
    public function actionRate(string $url, float $rating)
    {
        if($this->isAjax())
        {
            $this->articleManager->rateArticle($url, $rating);
        }else{
            throw new BadRequestException();
        }
    }

    /**
     *  Creates editor form component
     * @return Form editor form
     */
    protected function createComponentEditorForm()
    {
        $form = new Form();
        $form->addHidden('article_id');
        $form->addText('title', 'Title')->setRequired();
        $form->addText('url', 'Url')->setRequired();
        $form->addText('description', 'Description')->setRequired();
        $form->addTextArea('content', 'Content');
        $form->addSubmit('submit', 'Save article');
        $form->onSuccess[] = [$this, 'editorFormSucceed'];
        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws \Nette\Application\AbortException
     */
    public function editorFormSucceed(Form $form, ArrayHash $values)
    {
        try{
            $this->articleManager->saveArticle($values);
            $this->flashMessage('Article successfully saved.');
            $this->redirect(':Core:Article:', $values->url);
        }catch(ConstraintViolationException $e){
            $this->flashMessage('Article with this url already exists.');
        }
    }
}