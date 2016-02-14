<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/2/16
 * Time: 3:41 PM
 */

namespace Multiple\Models;

use Phalcon\Mvc\Model;


class ClientUserRole extends Model
{
    public function initialize(){
        $this->setSource("linkage_user_role");

        $this->hasOne('role_id', 'Multiple\Models\Role', 'role_id', array(  'alias' => 'role',
            'reusable' => true ));
    }


}
