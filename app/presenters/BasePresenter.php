<?php

namespace App\Presenters;

use Nette\Application\UI\Presenter;

/**
 * Base presenter for all application presenters.
 * @package App\Presenters
 */
abstract class BasePresenter extends Presenter
{
    /** @var null|string Address of presenter for logging user */
    protected $loginPresenter = null;

    protected function startup()
    {
        parent::startup();
        if (!$this->getUser()->isAllowed($this->getName(), $this->getAction())) {
            $this->flashMessage('You are not signed in or have no permissions to do this.');
            if ($this->loginPresenter) $this->redirect($this->loginPresenter);
        }
    }

    /** Is caling before rendering begins and getting variables into the layout */
    protected function beforeRender()
    {
        parent::beforeRender();
        $this->template->admin = $this->getUser()->isInRole('admin');
    }
}
