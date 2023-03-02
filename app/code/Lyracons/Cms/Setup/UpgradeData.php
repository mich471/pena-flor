<?php
namespace Lyracons\Cms\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Component\ComponentRegistrar;
use MSP\CmsImportExport\Api\ContentInterface;
use Magento\Store\Api\StoreRepositoryInterface;

/**
 * Upgrade Data script
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{

    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var ComponentRegistrar
     */
    private $componentRegistrar;
    
    /**
     * @var ContentInterface
     */
    protected $contentInterface;
    
    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepositoryInterface;

    public function __construct(
    ComponentRegistrar $componentRegistrar, ModuleListInterface $moduleList, ResourceInterface $moduleResource, ContentInterface $contentInterface, StoreRepositoryInterface $storeRepositoryInterface
    )
    {
        $this->moduleResource = $moduleResource;
        $this->moduleList = $moduleList;
        $this->componentRegistrar = $componentRegistrar;
        $this->contentInterface = $contentInterface;
        $this->storeRepositoryInterface = $storeRepositoryInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        // Obtenemos la version del modulo
        $module = $this->moduleList->getOne('Lyracons_Cms');
        
        echo 'Upgrade de ' . $context->getVersion() . ' a ' . $module['setup_version'];

        //obtenemos el path del modulo, para buscar los upgrades
        $path = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'Lyracons_Cms') . '/updates/';
        
        $filename = 'upgrade-' . $context->getVersion() . '-' . $module['setup_version'] . '.zip';
        $zipFile = $path . $filename;

        // busca un archivo upgrade-x.x.x-y.y.y.zip dentro de la carpeta updates/ de este mismo modulo
        if (file_exists($zipFile)) {

            //configurado para siempre actualizar
            $cmsMode = ContentInterface::CMS_MODE_UPDATE;
            $mediaMode = ContentInterface::CMS_MODE_UPDATE;

            //arma un array de mapeo de stores
            $storesMap = Array();
            $stores = $this->storeRepositoryInterface->getList();
            foreach ($stores as $storeInterface) {
                $storesMap[$storeInterface->getCode()] = $storeInterface->getCode();
            }

            //configura el actualizador ...
            $this->contentInterface
                ->setCmsMode($cmsMode)
                ->setMediaMode($mediaMode)
                ->setStoresMap($storesMap);

            //importa el zip
            $count = $this->contentInterface->importFromZipFile($zipFile, false);

            echo " \n Se actualizaron  $count  item(s) ";
        }
        $setup->endSetup();
    }
}
