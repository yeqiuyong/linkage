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

	public function getContact4Admin($pagination, $offset, $size){
		$contacts = self::find([
			'conditions' => 'status != :status:',
			'bind' => ['status' => StatusCodes::NOTICE_DELETE],
		]);

		$results = [];
		foreach ($contacts as $contact) {
			$result = [];
			$result['id'] = $contact->id;
			$result['name'] = $contact->name;
			$result['telephone'] = $contact->telephone;
			$result['email'] = $contact->email;
			$result['create_time'] = $contact->create_time;
			$result['comments'] = $contact->comments;
			$result['status'] = $contact->status;

			array_push($results,$result);
		}

		return $results;
	}

	public function getContactDetail4Admin($contactId){
		$contact = self::findFirst([
			'conditions' => 'id = :contact_id:',
			'bind' => ['contact_id' => $contactId]
		]);

		if(isset($contact->id)){
			return [
				'id' => $contact->id,
				'name' => $contact->name,
				'telephone' => $contact->telephone,
				'email' => $contact->email,
				'comments' => $contact->comments,
				'create_time' => $contact->create_time,
			];
		}else{
			throw new DataBaseException(ErrorCodes::DATA_FIND_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FIND_FAIL]);
		}
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

	public function updateStatus($conId, $status){
		$contact = self::findFirst([
			'conditions' => 'id = :id:',
			'bind' => ['id' => $conId]
		]);

		if(!isset($contact->id)){
			throw new UserOperationException(ErrorCodes::USER_ADVERTISE_NOT_FOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_ADVERTISE_NOT_FOUND]);
		}

		$contact->status = $status;
		$contact->update_time = time();

		if($contact->update() == false){
			$message = '';
			foreach ($contact->getMessages() as $msg) {
				$message .= (String)$msg. ',';
			}
			$logger = Di::getDefault()->get(Services::LOGGER);
			$logger->fatal($message);

			throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
		}
	}

}
