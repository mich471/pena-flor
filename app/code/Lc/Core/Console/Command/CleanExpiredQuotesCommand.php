<?php

namespace Lc\Core\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Magento\Store\Model\StoresConfig;




class CleanExpiredQuotesCommand extends Command
{
    const LIFETIME = 86400;

    /**
     * @var StoresConfig
     */
    protected $storesConfig;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory
     */
    protected $quoteCollectionFactory;

    /**
     * @var array
     */
    protected $expireQuotesFilterFields = [];

    /**
    * @var State $state
    */
    private $state;

    /**
     * @var HelperData $helperData
     */
    protected $helperData;

    /**
     * @var HelperEmail $helperEmail
     */
    protected $helperEmail;


    /**
     * @param State $state
     * @param StoresConfig $storesConfig
     * @param QuoteCollectionFactory $collectionFactory
     */
    public function __construct(
        State $state,
        StoresConfig $storesConfig,
        QuoteCollectionFactory $collectionFactory
    ) {
        parent::__construct();
        $this->storesConfig = $storesConfig;
        $this->quoteCollectionFactory = $collectionFactory;
        $this->state = $state;
//        $state->setAreaCode('frontend');
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('lc:core:clean-expired-quotes')
            ->setDescription('Clean expired quotes');
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Starting</info>");
        $lifetimes = $this->storesConfig->getStoresConfigByPath('checkout/cart/delete_quote_after');
        foreach ($lifetimes as $storeId => $lifetime) {
            $lifetime *= self::LIFETIME;

            /** @var $quotes \Magento\Quote\Model\ResourceModel\Quote\Collection */
            $quotes = $this->quoteCollectionFactory->create();

            $quotes->addFieldToFilter('store_id', $storeId);
            $quotes->addFieldToFilter('created_at', ['to' => date("Y-m-d", time() - $lifetime)]);
            $quotes->addFieldToFilter('is_active', 0);

            foreach ($this->getExpireQuotesAdditionalFilterFields() as $field => $condition) {
                $quotes->addFieldToFilter($field, $condition);
            }
            $output->writeln("quote qty to delete: ".$quotes->count());
            $quotes->walk('delete');
        }

        $output->writeln("<info>Finished</info>");
    }

    /**
     * Retrieve expire quotes additional fields to filter
     *
     * @return array
     */
    protected function getExpireQuotesAdditionalFilterFields()
    {
        return $this->expireQuotesFilterFields;
    }

    /**
     * Set expire quotes additional fields to filter
     *
     * @param array $fields
     * @return void
     */
    public function setExpireQuotesAdditionalFilterFields(array $fields)
    {
        $this->expireQuotesFilterFields = $fields;
    }

}