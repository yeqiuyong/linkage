<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 27/2/16
 * Time: 6:48 PM
 */

namespace Multiple\Core\Libraries;

use Phalcon\Logger\Adapter\File as FileAdapter;

class Logger
{
    private static $DEBUG = 0;

    private static $INFO = 1;

    private static $WARNING = 2;

    private static $FATAL = 3;

    private static $FOPEN_WRITE_CREATE = 'ab';

    private $mLoger;

    private $level;

    public function __construct($path, $level)
    {
        $file = $path.'log-'.date('Y-m-d').'.log';

        if ( ! $fp = @fopen($file, self::$FOPEN_WRITE_CREATE))
        {
            return FALSE;
        }

        $this->mLoger = new FileAdapter($file);
    }

    public function debug($msg){
        if(self::$DEBUG != $this->level){
            return;
        }

        $this->mLoger->debug($msg);
    }

    public function info($msg){
        if(self::$INFO < $this->level){
            return;
        }

        $this->mLoger->info($msg);
    }

    public function warning($msg){
        if(self::$WARNING < $this->level){
            return;
        }

        $this->mLoger->warning($msg);
    }

    public function fatal($msg){
        if(self::$FATAL < $this->level){
            return;
        }

        $this->mLoger->error($msg);
    }
}