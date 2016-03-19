<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 28/1/16
 * Time: 6:02 PM
 */

namespace Multiple\Models;

use Phalcon\Di;
use Phalcon\Mvc\Model;

use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\StatusCodes;
use Multiple\Core\Exception\DataBaseException;

class Contact extends Model
{

	public function initialize()
	{
		$this->setSource("linkage_contact");
	}

	public function add($name, $mobile, $email, $comments)
	{
		$now = time();

		$this->name = $name;
		$this->telephone = $mobile;
		$this->email = $email;
		$this->comments = $comments;
		$this->create_time = $now;
		$this->update_time = $now;
		$this->status = StatusCodes::COMPLAIN_HANDLING;

		if($this->save() == false){
			$message = '';
			foreach ($this->getMessages() as $msg) {
				$message .= (String)$msg . ",";
			}

			$logger = Di::getDefault()->get(Services::LOGGER);
			$logger->fatal($message);

			throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
		}
	}

}
