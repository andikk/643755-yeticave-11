<li class="lots__item lot">
    <div class="lot__image">
        <img src="<?= $lot['url'] ?>" width="350" height="260" alt="">
    </div>
    <div class="lot__info">
        <span class="lot__category"><?= $lot['category'] ?></span>
        <h3 class="lot__title"><a class="text-link" href="pages/lot.html"><?= esc($lot['name']) ?></a></h3>
        <div class="lot__state">
            <div class="lot__rate">
                <span class="lot__amount">Стартовая цена</span>
                <span class="lot__cost"><?= format_price(esc($lot['price'])) ?></span>
            </div>
            <?php if ($expiryTime) :?>
                <div class="lot__timer timer <?= (int) $expiryTime[0] === 0 ? 'timer--finishing' : ''?>">
                    <?= implode(':', $expiryTime) ?>
                </div>
            <?php endif ?>

        </div>
    </div>
</li>
