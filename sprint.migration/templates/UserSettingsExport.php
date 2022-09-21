<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/**
 * @var $version
 * @var $description
 * @var $iblock
 * @var $extendUse
 * @var $extendClass
 * @var $moduleVersion
 * @var $tabExport
 * @var $fieldExports
 * @var $optionExports
 * @formatter:off
 */

?><?php echo "<?php\n" ?>

namespace Sprint\Migration;

<?php echo $extendUse ?>

class <?php echo $version ?> extends <?php echo $extendClass ?>

{
    protected $description = "<?php echo $description ?>";

    protected $moduleVersion = "<?php echo $moduleVersion ?>";

    /**
    * @throws Exceptions\HelperException
    * @return bool|void
    */
    public function up()
    {
        $helper = $this->getHelperManager();

        <?php if (count($tabExport)) { ?>$helper->UserSettings()->saveTab("<?= $tabExport['CODE']?>", <?= var_export($tabExport, 1) ?>);<?php } ?>

        <?php foreach ($fieldExports as $field) { ?>$helper->UserSettings()->saveField("<?= $field['UF']['FIELD_NAME']?>", <?= var_export($field, 1) ?>);<?php } ?>

        <?php foreach ($optionExports as $fieldCode => $optionValue) { ?>$helper->UserSettings()->setOption("<?= $fieldCode?>", <?= var_export($optionValue, 1) ?>);<?php } ?>
    }

    public function down()
    {
    //your code ...
    }
}
