<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 28/1/16
 * Time: 6:02 PM
 */

namespace Multiple\Models;

use Phalcon\Mvc\Model;
use Phalcon\Db\RawValue;

class Contact extends Model
{

	public $id;

	public $name;

	public $email;

	public $comments;

	public $created_at;

	public function initialize()
	{
		$this->setSource("linkage_contact");
	}

	public function beforeCreate()
	{
		$this->created_at = new RawValue('now()');
	}

}
