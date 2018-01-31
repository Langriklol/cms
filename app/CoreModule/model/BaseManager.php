<?php
/**
 * Created by PhpStorm.
 * User: lango
 * Date: 1/31/18
 * Time: 8:57 PM
 */

namespace App\Module;
use Nette\Database\Context;
use Nette\Object;

class BaseManager extends Object
{
    /** @var Context database context */
    protected $context;

    /**
     * BaseManager constructor.
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }
}