<?php

namespace App\Presenters;

use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Tracy\ILogger;

class ErrorPresenter extends BasePresenter
{
	/** @var ILogger */
	private $logger;


	public function __construct(ILogger $logger)
	{
	    parent::__construct();
		$this->logger = $logger;
	}

    /**
     * @param $exception
     * @throws AbortException
     */
    public function renderDefault($exception)
    {
        $serverError = false;
        // Pokud jde o chybu v dotazu.
        if ($exception instanceof BadRequestException) {
            $this->logger->log(
                "HTTP code {$exception->getCode()}: {$exception->getMessage()} in {$exception->getFile()}:{$exception->getLine()}", 'access');
        } else {
            $this->setView('500');
            $this->logger->log($exception, ILogger::EXCEPTION); // Logs exception
            $serverError = true;
        }

        // If it is AJAX query, error will be in payload
        if ($this->isAjax()) {
            $this->payload->error = true;
            $this->terminate();
        } elseif (!$serverError) { // If not the server error
            $this->redirect(':Core:Article:', 'error'); // Redirect to own error page
        }
        //Otherwise will render error code 500
    }

    /*
	/**
	 * @return Nette\Application\IResponse
	 *//*
	public function run(Nette\Application\Request $request)
	{
		$e = $request->getParameter('exception');

		if ($e instanceof Nette\Application\BadRequestException) {
			// $this->logger->log("HTTP code {$e->getCode()}: {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}", 'access');
			list($module, , $sep) = Nette\Application\Helpers::splitName($request->getPresenterName());
			$errorPresenter = $module . $sep . 'Error4xx';
			return new Responses\ForwardResponse($request->setPresenterName($errorPresenter));
		}

		$this->logger->log($e, ILogger::EXCEPTION);
		return new Responses\CallbackResponse(function () {
			require __DIR__ . '/templates/Error/500.phtml';
		});
	}*/
}
