<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 27/1/16
 * Time: 11:45 AM
 */

namespace Multiple\Core\Libraries;

class CustomSerializer extends \League\Fractal\Serializer\ArraySerializer
{
    public function collection($resourceKey, array $data)
    {
        if ($resourceKey == 'parent') {
            return $data;
        }

        return array($resourceKey ?: 'data' => $data);
    }

    public function item($resourceKey, array $data)
    {
        if ($resourceKey == 'parent') {
            return $data;
        }

        return array($resourceKey ?: 'data' => $data);
    }
}