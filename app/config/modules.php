<?php

/**
 * Register application modules
 */
//Register the installed modules
$application->registerModules(array(
    'api' => array(
        'className' => 'Multiple\API\Module',
        'path' => APP_PATH.'app/modules/api/Module.php'
    ),

    'frontend' => array(
        'className' => 'Multiple\Frontend\Module',
        'path' => APP_PATH.'app/modules/frontend/Module.php'
    ),

    'backend' => array(
        'className' => 'Multiple\Backend\Module',
        'path' => APP_PATH.'app/modules/backend/Module.php'
    )
));
