<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
    <ul class="promo__list">
        <?php foreach ($categories as $category): ?>
            <li class="promo__item promo__item--<?= esc($category['char_code']) ?>">
                <a class="promo__link" href="category.php?id=<?=$category['id']?>"><?= esc($category['name']) ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
<section class="lots">
    <div class="lots__header">
        <h2>Открытые лоты</h2>
    </div>
    <ul class="lots__list">
        <?php foreach ($lots as $lot): ?>
            <?=include_template('_lot.php', ['lot' => $lot,
                                                    'expiryTime' => get_dt_range($lot['expiry_date'])])?>
        <?php endforeach; ?>
    </ul>
</section>
