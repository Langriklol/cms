<?php
/**
 * Created by PhpStorm.
 * User: lango
 * Date: 3/26/18
 * Time: 2:35 PM
 */

namespace App\CoreModule\Presenters;


use App\Model\UserManager;
use Nette\Application\BadRequestException;
use Nette\Database\Context;

class UserPresenter extends BaseCorePresenter
{
    private $userManager;
    private $context;

    public function __construct(UserManager $userManager, Context $context)
    {
        parent::__construct();
        $this->userManager = new $userManager;
        $this->context = $context;
    }

    public function renderDefault(int $id)
    {
        if($id){
            $this->template->user = $this->context->table('user')
                ->select('username, profile_pic, description, created_at')
                ->where('user_id', $id);
        }else{
            $this->flashMessage('Invalid user!');
        }
    }

}