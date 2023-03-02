<?php
declare(strict_types=1);

namespace SummaSolutions\OrderCustomizations\Setup\Patch\Data;

use Exception;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Status;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\StatusFactory as StatusResourceFactory;

class NewOrderStatuses implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    const CUSTOM_STATE_STATUS_LABEL = [
        "Pago Confirmado" => "pago_confirmado",
        "Pedido en Preparación" => "en_preparacion",
        "Pedido Enviado" => "enviado_gls",
        "Pedido Facturado" => "facturado_gls",
        "Pedido Realizado" => "realizado",
        "Pedido Entregado" => "entregado_gls",
        "Pago en Proceso" => "payment_on_processing"
    ];

    Const MAGENTO_STATE_STATUS_RELATIONSHIP = [
        "pago_confirmado" => Order::STATE_PROCESSING,
        "en_preparacion" => order::STATE_PROCESSING,
        "enviado_gls" => Order::STATE_COMPLETE,
        "facturado_gls" => order::STATE_PROCESSING,
        "realizado" => Order::STATE_NEW,
        "entregado_gls" => Order::STATE_COMPLETE,
        "payment_on_processing" => Order::STATE_NEW
    ];

    /**
     * Status Factory
     *
     * @var StatusFactory
     */
    protected StatusFactory $statusFactory;
    /**
     * Status Resource Factory
     *
     * @var StatusResourceFactory
     */
    protected StatusResourceFactory $statusResourceFactory;

    /**
     * @var ModuleDataSetupInterface
     */
    protected ModuleDataSetupInterface $moduleDataSetup;

    /**
     * InstallData constructor
     *
     * @param StatusFactory $statusFactory
     * @param StatusResourceFactory $statusResourceFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        StatusFactory $statusFactory,
        StatusResourceFactory $statusResourceFactory,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->statusFactory = $statusFactory;
        $this->statusResourceFactory = $statusResourceFactory;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @inheirtDoc
     */
    public static function getDependencies()
    {
        return [];
    }
    /**
     * @inheirtDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * Adds new Types of Order state_statuses to have
     * compatibility with fulfillment process
     *
     * @return void
     * @throws Exception
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        foreach (self::CUSTOM_STATE_STATUS_LABEL as $statusLabel => $state) {
            $statusResource = $this->statusResourceFactory->create();
            /** @var Status $status */
            $status = $this->statusFactory->create();
            $status->setData([
                'status' => $state,
                'label' => $statusLabel,
            ]);
            $status->assignState(self::MAGENTO_STATE_STATUS_RELATIONSHIP[$state], false, true);
            try {
                $statusResource->save($status);
            } catch (AlreadyExistsException $exception) {
                // Nothing else needed, status already exists because the patch is running again
                // or status was aggregated manually
            }
        }
        $this->moduleDataSetup->getConnection()->endSetup();
    }
}
