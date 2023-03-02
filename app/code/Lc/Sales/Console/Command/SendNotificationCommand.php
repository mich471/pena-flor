<?php

namespace Lc\Sales\Console\Command;


use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Lc\Sales\Helper\Transactional\Email;

class SendNotificationCommand extends Command
{

    /**
     * @var OrderRepository $orderRepository
     */
    protected $orderRepository;

    /**
     * @var Email $emailHelper
     */
    protected $emailHelper;

    /**
     * SendNotificationCommand constructor.
     * @param OrderRepository $orderRepository
     * @param Email $emailHelper
     */
    public function __construct(
        OrderRepository $orderRepository,
        Email $emailHelper
    )
    {
        parent::__construct();
        $this->orderRepository = $orderRepository;
        $this->emailHelper = $emailHelper;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('lc:sales:send-notification')
            ->setDescription('Send email order notification to customer');
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var $order Order */
        $order = $this->orderRepository->get(274);
        $templatePath = 'sales_email/order_comment/template';
        $this->emailHelper->sendEmailToCustomer($order, $templatePath);
        $output->writeln(".");
        $output->writeln("order id to process: ");
        $output->writeln("<info>Finished</info>");
    }
}
