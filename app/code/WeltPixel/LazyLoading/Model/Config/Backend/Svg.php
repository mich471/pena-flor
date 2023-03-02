<?php
/**
 * System config image field backend model
 */
namespace WeltPixel\LazyLoading\Model\Config\Backend;

/**
 * Class Svg
 * @package Weltpixel\LazyLoading\Config\Model\Config\Backend
 */
class Svg extends \Magento\Config\Model\Config\Backend\File
{
    /**
     * Getter for allowed extensions of uploaded files
     *
     * @return string[]
     */
    protected function _getAllowedExtensions()
    {
        return ['jpg', 'jpeg', 'gif', 'png', 'svg'];
    }
}
