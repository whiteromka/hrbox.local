<?php

/** @var yii\web\View $this */
/** @var string $rows */

use app\assets\SiteIndexAsset;

SiteIndexAsset::register($this);
?>
<div class="site-index">
    <p class="text-info">Нужно прочесть /web/readme.txt . Для того что бы нагенерировать 10 фалов по 100 000 строк.</p>
    <h5>Hundred odd rows</h5>
    <h6 id="js-loader-message">Uploading data...
        <b id="js-timer"></b>
    </h6>
    <div id="js-ajax-content"></div>
</div>
