<?php

namespace Lyracons\CustomerActivation\Model\Mail\Template;

use Magento\Framework\Mail\Template\TransportBuilder as MagentoTransportBuilder;

class TransportBuilder extends MagentoTransportBuilder
{

    /**
     * Set mail from address
     *
     * @param string|array $from
     * @param string|int $scopeId
     * @return $this
     */
    public function setFrom($from, $scopeId = null)
    {
        $result = $this->_senderResolver->resolve($from, $scopeId);
        $this->message->setFrom($result['email'], $result['name']);
        return $this;
    }

}