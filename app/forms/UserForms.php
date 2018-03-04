<?php

namespace App\Forms;

use Nette\Application\UI\Form;
use Nette\Object;
use Nette\Security\AuthenticationException;
use Nette\Security\User;
use Nette\Utils\ArrayHash;

class UserForms extends Object
{
    /** @var User $user */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param Form $form
     * @param null|ArrayHash $instructions
     * @param bool $register
     * @throws \Nette\Application\AbortException
     */
    private function login($form, $instructions, $register = false)
    {
        $presenter = $form->getPresenter();
        try{
            $username = $form->getValues()->username;
            $password = $form->getValues()->password;

            if ($register)
                $this->user->getAuthenticator()->register($username, $password);

            $this->user->login($username, $password);
            if ($instructions && $presenter) {
                if (isset($instructions->message))
                    $presenter->flashMessage($instructions->message);

                if (isset($instructions->redirection))
                    $presenter->redirect($instructions->redirection);
            }

        }catch (AuthenticationException $e){
            if ($presenter) {//if form is in presenter send error message to presenter
                $presenter->flashMessage($e->getMessage());
                $presenter->redirect('this'); // redirect
            } else {
                $form->addError($e->getMessage()); // else throw an error
            }
        }
    }

    /**
     * @param Form|null $form Null if new form have to be created else form will be extended of new 2 fields
     * @return Form
     */
    private function createBasicForm(Form $form = null)
    {
        $form = $form ? $form : new Form;
        $form->addText('username', 'Username')->setRequired();
        $form->addPassword('password', 'Password')->setRequired();
        return $form;
    }

    public function createLoginForm($instructions = null, Form $form = null)
    {
        $form = $this->createBasicForm($form);
        $form->addSubmit('submit', 'Login');
        $form->onSuccess[] = function (Form $form) use ($instructions){
            $this->login($form, $instructions);
        };
        return $form;
    }

    public function createRegisterForm($instructions = null, Form $form = null)
    {
        $form = $this->createBasicForm($form);
        $form->addPassword('password_repeat', 'Repeat password')
            ->addRule(Form::EQUAL, 'Passwords don\'t match.', $form['password'])
            ->setRequired();
        $form->addSubmit('register', 'Sign up');
        $form->onSuccess[] = function (Form $form) use ($instructions) {
            $this->login($form, $instructions, true);
        };
        return $form;
    }
}