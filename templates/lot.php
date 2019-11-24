<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $category): ?>
            <li class="nav__item">
                <a href="category.php?id=<?=$category['id']?>"><?= esc($category['name']) ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<section class="lot-item container">
    <h2><?= esc($lot['name']) ?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="/uploads/<?= esc($lot['img']) ?>" width="730" height="548" alt="<?= esc($lot['name']) ?>">
            </div>
            <p class="lot-item__category">Категория: <span><?= esc($lot['category']) ?></span></p>
            <p class="lot-item__description"><?= esc($lot['description']) ?></p>
        </div>
        <div class="lot-item__right">

            <div class="lot-item__state">
                <?php if ($expiryTime) :?>
                    <div class="lot-item__timer timer <?= (int) $expiryTime[0] === 0 ? 'timer--finishing' : ''?>">
                        <?= implode(':', $expiryTime) ?>
                    </div>
                <?php endif ?>
                <div class="lot-item__cost-state">
                    <div class="lot-item__rate">
                        <span class="lot-item__amount">Текущая цена</span>
                        <span class="lot-item__cost"><?= format_price(esc($lot['price'])) ?></span>
                    </div>
                    <div class="lot-item__min-cost">
                        Мин. ставка <span><?= esc($lot['step']) ?></span>
                    </div>
                </div>
                <?php if ($show_bet_block) :?>
                    <form class="lot-item__form" action="<?= 'lot.php?id='.$lot['id'] ?>" method="post" autocomplete="off">
                        <?php $classname = isset($errors['cost']) ? "form__item--invalid" : ""; ?>
                        <p class="lot-item__form-item form__item <?= $classname; ?>">
                            <label for="cost">Ваша ставка</label>
                            <input id="cost" type="text" name="cost" placeholder="<?= esc($lot['price']) + esc($lot['step']) ?>">
                            <span class="form__error"><?= $errors['cost'] ?></span>
                        </p>
                        <button type="submit" class="button">Сделать ставку</button>
                    </form>
                <?php endif ?>
            </div>

            <?php if (!empty($bets)) :?>
                <div class="history">
                    <h3>История ставок (<span><?= count($bets) ?></span>)</h3>
                    <table class="history__list">
                        <?php foreach ($bets as $bet): ?>
                            <tr class="history__item">
                                <td class="history__name"><?= esc($bet['user']) ?></td>
                                <td class="history__price"><?= esc($bet['price']) ?></td>
                                <td class="history__time"><?= format_bet_date($bet['time']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php endif ?>
        </div>
    </div>
</section>
