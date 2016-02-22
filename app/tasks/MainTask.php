<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 22/2/16
 * Time: 10:37 PM
 */

use Multiple\Models\Company;

class MainTask extends \Phalcon\Cli\Task
{
    public function mainAction()
    {
        echo "\nThis is the default task and the defaffffult action \n";
    }

    /**
     * @param array $params
     */
    public function testAction(array $params)
    {
        echo sprintf('hello %s', $params[0]) . PHP_EOL;
        echo sprintf('best regards, %s', $params[1]) . PHP_EOL;


        $companies = Company::find(
        );

        foreach ($companies as $company) {
            echo  $company->company_id;
            echo $company->name;
            echo $company->contactor;
            echo $company->service_phone_1;
            echo $company->create_time;

        }
    }
}