<?php


namespace Gpf\MercadoPago\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\File\Csv as CsvProcessor;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\State;

class ExportOrders extends Command
{

    const NAME_ARGUMENT = "name";
    const NAME_OPTION = "option";
    /**
     * @var CsvProcessor
     */
    protected $csvProcessor;
    /**
     * @var DirectoryList
     */
    protected $directoryList;

    protected $inputFileName = "mp_orders.csv";

    protected $outputFilePrefix = "exported_mp_orders_";
    /**
     * @var OrderRepository
     */
    protected $orderRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    /**
     * @var State
     */
    protected $state;

    /**
     * ExportOrders constructor.
     * @param CsvProcessor $csvProcessor
     * @param DirectoryList $directoryList
     * @param OrderRepository $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param State $state
     */
    public function __construct(
        CsvProcessor $csvProcessor,
        DirectoryList $directoryList,
        OrderRepository $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        State $state
    ) {
        parent::__construct("gpf:mercadopago:exportorders");
        $this->csvProcessor = $csvProcessor;
        $this->directoryList = $directoryList;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
        $name = $input->getArgument(self::NAME_ARGUMENT);
        //$option = $input->getOption(self::NAME_OPTION);
        if (!is_null($name)) {
            $this->inputFileName = $name;
        }
        $output->writeln("Reading file " . $this->inputFileName);
        $file = $this->directoryList->getPath('var') . "/importexport/" . $this->inputFileName;
        $data = $this->csvProcessor->getData($file);
        $output->writeln('Processing ' . count($data) . " registers");
        if (count($data)) {
            $export = [];
            foreach ($data as $item) {
                $incrementId = trim(reset($item));
                $output->writeln("Load Order by Increment ID: " . $incrementId);
                $searchCriteria = $this->searchCriteriaBuilder->addFilter(
                    'increment_id',
                    $incrementId, 'eq')->create();
                $list = $this->orderRepository->getList($searchCriteria)->getItems();
                if (!count($list)) {
                    $output->writeln("Order not found: " . $incrementId);
                    continue;
                }
                /** @var $order OrderInterface */
                $order = reset($list);
                if (!$order->getEntityId()) {
                    $output->writeln("Order not found: " . $incrementId);
                    continue;
                }

                $additionalInformation = $order->getPayment()->getAdditionalInformation();
                $transactionId = $order->getPayment()->getLastTransId();

                if (!$transactionId && isset($additionalInformation['id'])) {
                    $transactionId = (string)$additionalInformation['id'];
                }

                if (!$transactionId) {
                    $output->writeln("Not found required information for order: " . $incrementId);
                    continue;
                }
                $item = [
                    'increment_id' => $incrementId,
                    'transaction_id' => $transactionId
                ];
                $export[] = $item;
            }
            if (count($export)) {
                $this->csvProcessor->saveData($this->getNewFileName(), $export);
            }
        }
        $output->writeln('End Process..');
    }

    protected function getNewFileName()
    {
        return $this->directoryList->getPath('var') . "/importexport/" . $this->outputFilePrefix . date('YmdHis') . '.csv';
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        //$this->setName("gpf:mercadopago:exportorders");
        $this->setDescription("ExportOrders");
        $this->setDefinition([
            new InputArgument(self::NAME_ARGUMENT, InputArgument::OPTIONAL, "Name"),
            new InputOption(self::NAME_OPTION, "-a", InputOption::VALUE_NONE, "Option functionality")
        ]);
        parent::configure();
    }
}
