<nav class="nav">
  <ul class="nav__list container">
      <?php foreach ($categories as $category): ?>
          <li class="nav__item">
              <a href="all-lots.html"><?= esc($category['name']) ?></a>
          </li>
      <?php endforeach; ?>
  </ul>
</nav>

<section class="rates container">
    <h2>Мои ставки</h2>
    <table class="rates__list">
        <?php foreach ($rates as $rate): ?>
            <tr class="rates__item <?= $rate['rate_class'] ?>">
                <td class="rates__info">
                    <div class="rates__img">
                        <img src="/uploads/<?= $rate['lot_img'] ?>" width="54" height="40" alt="<?= esc($rate['lot_name']) ?>">
                    </div>
                    <div>
                        <h3 class="rates__title"><a href="<?= 'lot.php?id=' . $rate['lot_id'] ?>"><?= esc($rate['lot_name']) ?></a></h3>
                        <?php if ($rate['winner_id']): ?>
                            <p><?= esc($rate['contacts']) ?></p>
                        <?php endif; ?>
                    </div>
                </td>
                <td class="rates__category">
                    <?= esc($rate['lot_category']) ?>
                </td>
                <td class="rates__timer">
                    <div class="timer <?= $rate['timer_class'] ?>"><?= $rate['timer_message'] ?></div>
                </td>
                <td class="rates__price">
                    <?= esc($rate['price']) ?>
                </td>
                <td class="rates__time">
                    <?= format_bet_date($rate['dt_add']) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</section>
