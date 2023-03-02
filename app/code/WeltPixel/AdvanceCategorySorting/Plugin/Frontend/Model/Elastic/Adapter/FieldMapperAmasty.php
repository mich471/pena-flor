<?php

namespace WeltPixel\AdvanceCategorySorting\Plugin\Frontend\Model\Elastic\Adapter;

class FieldMapperAmasty
{
    /**
     * @param FieldMapperInterface $subject
     * @param $result
     * @param array $context
     * @return mixed
     */
    public function afterBuildEntityFields(\Amasty\ElasticSearch\Model\Indexer\Structure\EntityBuilder\Product $subject, $result)
    {
        $result['created_at'] = [
            'type' => 'date',
            'fields' => [
                'sort_created_at' => [
                    'type' => 'keyword',
                    'index' => false
                ]
            ]
        ];
        $result['wp_sortby_new'] = [
            'type' => 'date',
            'fields' => [
                'sort_wp_sortby_new' => [
                    'type' => 'keyword',
                    'index' => false
                ]
            ]
        ];

        $result['wp_sortby_rates'] = [
            'type' => 'float',
            'fields' => [
                'sort_wp_sortby_rates' => [
                    'type' => 'keyword',
                    'index' => false
                ]
            ]
        ];

        $result['wp_sortby_review'] = [
            'type' => 'float',
            'fields' => [
                'sort_wp_sortby_review' => [
                    'type' => 'keyword',
                    'index' => false
                ]
            ]
        ];

        $result['wp_sortby_sales'] = [
            'type' => 'float',
            'fields' => [
                'sort_wp_sortby_sales' => [
                    'type' => 'keyword',
                    'index' => false
                ]
            ]
        ];

        return $result;
    }
}
