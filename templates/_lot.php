<li class="lots__item lot">
    <div class="lot__image">
        <img src="<?= $lot['url'] ?>" width="350" height="260" alt="">
    </div>
    <div class="lot__info">
        <span class="lot__category"><?= $lot['category'] ?></span>
        <h3 class="lot__title"><a class="text-link" href="pages/lot.html"><?= $lot['name'] ?></a></h3>
        <div class="lot__state">
            <div class="lot__rate">
                <span class="lot__amount">Стартовая цена</span>
                <span class="lot__cost"><?= format_price($lot['price']) ?></span>
            </div>
            <?php if (get_dt_range($lot['expiry_date'])) :?>
                <div class="lot__timer timer <?php if ((int) get_dt_range($lot['expiry_date'])[0] == 0) echo 'timer--finishing'?>">
                    <?= implode(':', get_dt_range($lot['expiry_date'])) ?>
                </div>
            <?php endif ?>

        </div>
    </div>
</li>
