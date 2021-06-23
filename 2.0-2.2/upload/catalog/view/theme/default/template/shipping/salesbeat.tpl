<script src="<?= $link_script ?>" type="text/javascript"></script>
<div class="salesbeat_description">
    <?php if (!empty($info)): ?>
        <div id="sb-cart-widget"></div>
        <div id="sb-cart-widget-result">
            <?php
            foreach ($info as $field):
                if (!empty($field['value'])):
                    ?>
                    <p><span class="salesbeat-summary-label"><?= $field['name'] ?>:</span> <?= $field['value'] ?></p>
                    <?php
                endif;
            endforeach;
            ?>

            <p><a href="" class="sb-reshow-cart-widget">Изменить данные доставки</a></p>
		</div>
        <input type="hidden" name="need[]" value="<?= $code ?>">

        <script type="text/javascript">
            SalesbeatCatalogDelivery.reshow({
                url: "<?= $setting['url']; ?>",
                token: "<?= $setting['token']; ?>",
                city_code: "<?= $setting['city_code']; ?>",
                products: <?= $setting['products']; ?>
            });
        </script>
    <?php else: ?>
        <div id="sb-cart-widget"></div>
        <div id="sb-cart-widget-result"></div>
        <script type="text/javascript">
            SalesbeatCatalogDelivery.init({
                url: "<?= $setting['url']; ?>",
                token: "<?= $setting['token']; ?>",
                city_code: "<?= $setting['city_code']; ?>",
                products: <?= $setting['products']; ?>
            });
        </script>
    <?php endif; ?>
</div>