<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 28/1/16
 * Time: 6:02 PM
 */

namespace Multiple\Models;

use Phalcon\Mvc\Model;

class Company extends Model
{
    public function initialize(){
        $this->setSource("linkage_company");

        $this->hasMany('company_id', 'Multiple\Models\ClientUser', 'company_id', array(  'alias' => 'users',
            'reusable' => true ));
    }
}
