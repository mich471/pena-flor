<?php

namespace Lc\Framework\Serialize\Serializer;

use  Magento\Framework\Serialize\Serializer\Json as CoreJson;

class Json extends CoreJson
{

    /**
     * {@inheritDoc}
     * @since 100.2.0
     */
    public function unserialize($string)
    {
        $result = json_decode($string, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $result = @unserialize($string);
            if ($result === false) {
                throw new \InvalidArgumentException('Unable to unserialize value.');
            }
            return $result;
        }
        return $result;
    }

}
