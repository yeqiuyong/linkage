<?php


use Phalcon\Mvc\Model;

class Company extends Model
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $telephone;

    /**
     * @var string
     */
    public $address;

    /**
     * @var string
     */
    public $city;

    public function initialize()
    {
        $this->setSource("linkage_companies");
    }
}
