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

class AdminProfile extends Model
{
    public $profile_id;

    public function initialize()
    {
        $this->setSource("linkage_admin_profile");
    }


}
