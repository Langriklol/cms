<?php
/**
 * Created by PhpStorm.
 * User: lango
 * Date: 2/1/18
 * Time: 12:29 PM
 */

namespace App\CoreModule\Presenters;

use App\Forms\UserForms;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;


/**
 * Class AdministrationPresenter
 * @package App\CoreModule\Presenters
 */
class AdministrationPresenter extends BaseCorePresenter
{
    /** @var UserForms $userFormsFactory Factory for user forms */
    private $userFormsFactory;

    /** @var array $instructions */
    private $instructions;

    public function __construct(UserForms $userForm)
    {
        parent::__construct();
        $this->userFormsFactory = $userForm;
    }

    /* Called before any action of presenter and initialize variables */
    /**
     * @throws \Nette\Application\AbortException
     */
    public function startup()
    {
        parent::startup();
        $this->instructions = [
            'message' => null,
            'redirection' => ':Core:Administration:'
        ];
    }

    /** Redirects to administration if user is logged in
     * @throws \Nette\Application\AbortException
     */
    public function actionLogin()
    {
        if($this->getUser()->isLoggedIn()){
            $this->redirect(':Core:Administration:');
        }
    }

    /**
     * @throws \Nette\Application\AbortException
     */
    public function actionLogout()
    {
        $this->getUser()->logout();
        $this->redirect($this->loginPresenter);
    }

    public function renderDefault()
    {
        $identity = $this->getUser()->getIdentity();
        if($identity){
            $this->template->username = $identity->getData()['username'];
        }
    }

    /**
     * Returns component of login form from form factory
     * @return Form login form
     */
    protected function createComponentLoginForm()
    {
        $this->instructions['message'] = 'You have been successfully signed in';
        return $this->userFormsFactory->createLoginForm(ArrayHash::from($this->instructions));
    }

    /**
     * Returns component of register form from form factory
     * @return Form register form
     */
    protected function createComponentRegisterForm()
    {
        $this->instructions['message'] = 'You have been successfully signed up.';
        return $this->userFormsFactory->createRegisterForm(ArrayHash::from($this->instructions));
    }
}