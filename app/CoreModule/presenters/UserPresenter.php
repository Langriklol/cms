<?php
/**
 * Created by PhpStorm.
 * User: lango
 * Date: 3/26/18
 * Time: 2:35 PM
 */

namespace App\CoreModule\Presenters;


use App\Model\UserManager;
use Nette\Utils\ArrayHash;

class UserPresenter extends BaseCorePresenter
{
    private $userManager;

    public function __construct(UserManager $userManager)
    {
        parent::__construct();
        $this->userManager = $userManager;
    }

    public function renderDefault(int $id)
    {
        if($id){
            $user = $this->userManager->findUser($id);
            $articles = $user->related('article')->order('datetime');
            bdump($articles);

            $this->template->user = $user;
            $this->template->userArticles = $articles;

        }else{
            $this->flashMessage('Invalid user!');
        }
    }

}