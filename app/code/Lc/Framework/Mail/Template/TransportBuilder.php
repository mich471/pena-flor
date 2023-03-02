<?php

namespace Lc\Framework\Mail\Template;

use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\TransportBuilder as MagentoTransportBuilder;

class TransportBuilder extends MagentoTransportBuilder
{

    /**
     * Set mail from address
     *
     * @param string|array $from
     * @param string|int $scopeId
     * @return $this
     * @throws MailException
     * @deprecated Magento has deprecated this method and instead uses
     * setFromBySope for the same reasons.
     *
     * @see \Magento\Framework\Mail\Template\TransportBuilder::setFromByScope()
     *
     */
    public function setFrom($from, $scopeId = null)
    {
        return $this->setFromByScope($from, $scopeId);
    }

}
