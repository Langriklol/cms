<?php
/**
 * Created by PhpStorm.
 * User: lango
 * Date: 2/16/18
 * Time: 4:19 PM
 */

namespace App\CoreModule\Presenters;

use App\Presenters\BasePresenter;

abstract class BaseCorePresenter extends BasePresenter
{
    /** @var string Presenter link for logging user in in whole CoreModule */
    protected $loginPresenter = ':Core:Administration:login';
}