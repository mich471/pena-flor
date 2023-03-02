<?php
namespace WeltPixel\ProductPage\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Session\SessionManagerInterface;
use WeltPixel\ProductPage\Helper\Data as WpHelper;


/**
 * Class VisitorCounterManager
 * @package WeltPixel\ProductPage\Model
 */
class VisitorCounterManager
{
    /**
     * @var string
     */
    protected $visitorCounterTableName;

    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var SessionManagerInterface
     */
    protected $coreSession;

    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var WpHelper
     */
    protected $wpHelper;

    /**
     * @param ResourceConnection $resource
     * @param SessionManagerInterface $coreSession
     * @param RemoteAddress $remoteAddress
     * @param WpHelper $wpHelper
     */
    public function __construct(
        ResourceConnection $resource,
        SessionManagerInterface $coreSession,
        RemoteAddress $remoteAddress,
        WpHelper $wpHelper
    )
    {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->coreSession = $coreSession;
        $this->remoteAddress = $remoteAddress;
        $this->wpHelper = $wpHelper;
        $this->visitorCounterTableName = 'weltpixel_product_visitor_counter';
    }

    /**
     * @return string
     */
    public function getVisitorCounterTableName()
    {
        return $this->visitorCounterTableName;
    }

    /**
     * @param int $productId
     * @param int $intervalCheck
     * @return int
     */
    public function updateCounter($productId, $intervalCheck) {
        $sessionId = $this->coreSession->getSessionId();
        $lastVisitAt = (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        $productVisitCount = 0;

        if ($this->wpHelper->getVisitorCounterSessionIdentifier() == \WeltPixel\ProductPage\Model\Config\Source\VisitorCounterSessionIdentifier::IDENTIFIER_IP) {
            $sessionId = $this->remoteAddress->getRemoteAddress();
        }

        $productCounterTableName = $this->getVisitorCounterTableName();
        $productCounterTable = $this->resource->getTableName($productCounterTableName);
        try {
            $visitorCounterData = [
                'session_id' => $sessionId,
                'product_id' => $productId,
                'last_visit_at' => $lastVisitAt
            ];
            $this->connection->insertOnDuplicate(
                $productCounterTable,
                $visitorCounterData,
                array_keys($visitorCounterData)
            );

            $this->connection->delete(
                $productCounterTable,
                [
                    $this->connection->quoteInto('session_id = ?', $sessionId),
                    $this->connection->quoteInto('product_id != ?', $productId)
                ]
            );
            $select = $this->connection->select()->from(
                ['main_table' => $productCounterTable],
                [new \Zend_Db_Expr('COUNT(main_table.entity_id)')]
            )->where(
                    'main_table.last_visit_at >= date_sub(NOW(), interval ' .$intervalCheck . ' second) AND ' .
                    'main_table.product_id = :product_id'
            );


            $bind = ['product_id' => $productId];
            $productVisitCount = $this->connection->fetchOne($select, $bind);


        } catch (\Exception $ex) {
        }

        return $productVisitCount;
    }

    public function clearLogTable() {
        $productCounterTableName = $this->getVisitorCounterTableName();
        $productCounterTable = $this->resource->getTableName($productCounterTableName);

        try {
            $this->connection->delete(
                $productCounterTable,
                'last_visit_at < date_sub(NOW(), interval 1 day)'
            );
        } catch (\Exception $ex) {
            print_r($ex->getMessage());
        }
    }
}
