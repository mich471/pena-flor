<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Setup\Patch\Data;

use Amasty\Feed\Model\Feed;
use Amasty\Feed\Model\FeedRepository;
use Amasty\Feed\Model\Import;
use Amasty\Feed\Model\ResourceModel\Feed\CollectionFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpdateGoogleFeedDescriptionField implements DataPatchInterface
{
    /**
     * @var Import
     */
    private $import;

    /**
     * @var FeedRepository
     */
    private $feedRepository;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        Import $import,
        FeedRepository $feedRepository,
        CollectionFactory $collectionFactory
    ) {
        $this->import = $import;
        $this->feedRepository = $feedRepository;
        $this->collectionFactory = $collectionFactory;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply()
    {
        $this->import->update('google');

        $feedCollection = $this->collectionFactory->create()
            ->addFieldToFilter('xml_header', ['like' => '%base.google.com%'])
            ->addFieldToFilter('is_template', 0);

        // Add modifiers if there are no.
        // Or replace existing 'html_escape', 'length:500' with the necessary ones.
        /** @var Feed $feed */
        foreach ($feedCollection->getItems() as $feed) {
            $xmlContent = $feed->getXmlContent();
            preg_match("/<description>(.*?)\<\/description>/", $xmlContent, $description);
            if (!empty($description[1])) {
                preg_match("/modify=\"(.*?)\"/", $description[1], $modifier);
                if (isset($modifier[0])) {
                    $newDescription = $this->processModify($modifier, $description[0]);
                } else {
                    $newDescription = str_replace(
                        '}</description>',
                        ' modify="google_html_escape|length:5000"}</description>',
                        $description[0]
                    );
                }

                $newXmlContent = str_replace($description[0], $newDescription, $xmlContent);
                $feed->setXmlContent($newXmlContent);
                $this->feedRepository->save($feed);
            }
        }
    }

    private function processModify(array $modifier, string $description): string
    {
        if (!empty($modifier[1])) {
            $escapers = array_map(function ($escaper) {
                switch ($escaper) {
                    case 'html_escape':
                        $escaper = 'google_html_escape';
                        break;
                    case 'length:500':
                        $escaper = 'length:5000';
                }
                return $escaper;
            }, explode('|', $modifier[1]));

            foreach (['google_html_escape', 'length:5000'] as $escaper) {
                if (!in_array($escaper, $escapers)) {
                    $escapers[] = $escaper;
                }
            }

            $newModifier = str_replace($modifier[1], implode('|', $escapers), $modifier[0]);
        } else {
            $newModifier = 'modify="google_html_escape|length:5000"';
        }

        return str_replace($modifier[0], $newModifier, $description);
    }
}
