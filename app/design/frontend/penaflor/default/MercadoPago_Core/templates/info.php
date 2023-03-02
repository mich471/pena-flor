<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @var \Magento\Payment\Block\Info $block
 * @see \Magento\Payment\Block\Info
 */
$specificInfo = $block->getSpecificInformation();
$title = $block->escapeHtml($block->getMethod()->getTitle());
?>
<dl class="payment-method">
    <dt class="title"><?= /* @noEscape */ $title ?></dt>
<?php if ($specificInfo): ?>
    <dd class="content">
        <table class="data table" style=" width: -webkit-fill-available;">
            <caption class="table-caption" style="text-align: left;"><?= /* @noEscape */ $title ?></caption>
            <?php foreach ($specificInfo as $label => $value): ?>
                <tr>
                    <th scope="row"><?= $block->escapeHtml(__($label)) ?></th>
                    <td>
                        <?php if ($label === 'Link'): ?>
                            <a href="<?= /* @noEscape */ nl2br($block->escapeHtml(implode("\n", $block->getValueAsArray($value, true)))) ?>" target="_blank" >
                                <?= __('Generate Ticket') ?>
                            </a>
                        <?php else: ?>
                            <?= /* @noEscape */ nl2br($block->escapeHtml(implode("\n", $block->getValueAsArray(__($value), true)))) ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </dd>
<?php endif;?>
</dl>
<?= $block->getChildHtml() ?>
