<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 23/2/16
 * Time: 11:30 PM
 */

namespace Multiple\Core\Libraries;

use Phalcon\Http\Request\File;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\UploadException;

class UPyun
{
    private static $IMAGE_TYPE = ["image/gif", "image/jpeg", "image/pjpeg", "image/png"];

    private static $IMAGE_SIZE = 3000000;

    private static $SERVER_UPLOAD_PATH = '/www/tmp/image/';

    public function __construct(){
    }

    public function uploadImage(File $file){
        if (!isset($file)){
            throw new UploadException(ErrorCodes::GEN_UPLOAD_FILE_NOT_FOUND,ErrorCodes::$MESSAGE[(ErrorCodes::GEN_UPLOAD_FILE_NOT_FOUND)]);
        }

        if($file->getSize() > self::$IMAGE_SIZE || $file->getSize() == 0){
            throw new UploadException(ErrorCodes::GEN_UPLOAD_FILE_SIZE_ERROR,ErrorCodes::$MESSAGE[(ErrorCodes::GEN_UPLOAD_FILE_SIZE_ERROR)]);
        }

        $is_allow_type = 0;
        foreach(self::$IMAGE_TYPE as $type)
        {
            if($file->getType() == $type){
                $is_allow_type = 1;
                break;
            }
        }
        if(!$is_allow_type){
            throw new UploadException(ErrorCodes::GEN_UPLOAD_FILE_TYPE_ERROR,ErrorCodes::$MESSAGE[(ErrorCodes::GEN_UPLOAD_FILE_TYPE_ERROR)]);
        }

        $extension = explode("/", $file->getType())[1];
        $fileName = self::$SERVER_UPLOAD_PATH . $file->getName();

        $file->moveTo($fileName);
        $image_md5 = md5_file($fileName);
        $serverPath = "image". date('/Y/m/d/').$image_md5.'.'.$extension;

        return $this->uploadToYouPaiYun($fileName, $serverPath);

    }

    private function uploadToYouPaiYun($filePath, $serverPath){
        $bucketName   = 'linkage';
        $operatorName = 'zhouxin';
        $operatorPwd  = 'linkage@456';

        //被上传的文件路径
        $fileSize = filesize($filePath);
        //文件上传到服务器的服务端路径
        $uri = "/$bucketName/$serverPath";

        //生成签名时间。得到的日期格式如：Thu, 11 Jul 2014 05:34:12 GMT
        $date = gmdate('D, d M Y H:i:s \G\M\T');
        $sign = md5("PUT&{$uri}&{$date}&{$fileSize}&".md5($operatorPwd));

        $ch = curl_init('http://v0.api.upyun.com' . $uri);

        $headers = array(
            "Expect:",
            "Date: ".$date, // header 中需要使用生成签名的时间
            "Authorization: UpYun $operatorName:".$sign
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_PUT, true);

        $fh = fopen($filePath, 'rb');
        curl_setopt($ch, CURLOPT_INFILE, $fh);
        curl_setopt($ch, CURLOPT_INFILESIZE, $fileSize);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);

        if(curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200) {
            return $bucketName . '.b0.upaiyun.com/' . $serverPath;

        } else {
            $errorMessage = sprintf("UPYUN API ERROR:%s", $result);
            echo $errorMessage;
        }
        curl_close($ch);

    }


}
