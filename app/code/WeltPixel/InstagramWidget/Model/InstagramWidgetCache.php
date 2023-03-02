<?php
namespace WeltPixel\InstagramWidget\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstagramWidgetCache
{

    /**
     * @var string
     */
    protected $instagramCacheTableName;

    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @param ResourceConnection $resource
     */
    public function __construct(
        ResourceConnection $resource
    ) {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->instagramCacheTableName = 'weltpixel_instagram_cache';
    }

    /**
     * @return string
     */
    public function getInstagramCacheTableName()
    {
        return $this->connection->getTableName($this->instagramCacheTableName);
    }

    /**
     * @param string $cacheId
     * @return string
     */
    public function getInstagramContentByCacheId($cacheId)
    {
        $tableName = $this->getInstagramCacheTableName();
        $select = $this->connection->select()
            ->from(
                ['t' => $tableName],
                ['content']
            )
            ->where(
                "t.cache_id = :cache_id"
            );
        $bind = ['cache_id'=>$cacheId];
        $result = $this->connection->fetchOne($select, $bind);

        return $result;
    }

    /**
     * @param $caheId
     * @param $instagramContent
     */
    public function saveInstagramContentByCacheId($caheId, $instagramContent)
    {
        $insertData = [
            'cache_id' => $caheId,
            'content' => $instagramContent
        ];
        $tableName = $this->getInstagramCacheTableName();
        $deleteWhereCondition = [
            $this->connection->quoteInto('cache_id = ?', $caheId),
        ];
        $this->connection->delete($tableName, $deleteWhereCondition);
        $this->connection->insert($tableName, $insertData);
    }
}
