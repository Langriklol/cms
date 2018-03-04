<?php
/**
 * Created by PhpStorm.
 * User: lango
 * Date: 2/1/18
 * Time: 4:01 PM
 */

namespace App\CoreModule\Presenters;

use Nette\Application\UI\Form;
use Nette\InvalidStateException;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Nette\Utils\ArrayHash;

/**
 * Class ContactPresenter
 * @package App\CoreModule\Presenters
 */
class ContactPresenter extends BaseCorePresenter
{
    const EMAIL = '';

    /**
     * @return Form
     */
    protected function createComponentContactForm()
    {
        $form = new Form();
        $form->addText('email', 'Your email address')->setType('email')->setRequired();
        $form->addTextArea('message', 'Message')->setRequired()
            ->addRule(Form::MIN_LENGTH, 'The message must be at least %d characters long.', 25);
        $form->addSubmit('submit', 'send');
        $form->onSuccess[] = [$this, 'contactFormSucceed'];
        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws \Nette\Application\AbortException
     */
    public function contactFormSucceed(Form $form, ArrayHash $values)
    {
        try{
            $mail = new Message();
            $mail->setFrom($values->email)->addTo(self::EMAIL)
                ->setSubject('Contact mail')
                ->setBody($values->message);
            $mailer = new SendmailMailer();
            $mailer->send($mail);
            $this->flashMessage('Email was successfully sent.');
            $this->redirect('this');
        }catch (InvalidStateException $ex){
            $this->flashMessage('This mail cannot be sent.');
        }
    }
}