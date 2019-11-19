<nav class="nav">
  <ul class="nav__list container">
      <?php foreach ($categories as $category): ?>
          <li class="nav__item">
              <a href="all-lots.html"><?= esc($category['name']) ?></a>
          </li>
      <?php endforeach; ?>
  </ul>
</nav>
<div class="container">
    <section class="lots">
        <h2>Результаты поиска по запросу «<span><?= $search ?></span>»</h2>
        <?php if (empty($lots)): ?>
            <p>Ничего не найдено по вашему запросу</p>
        <?php endif ?>
        <ul class="lots__list">
            <?php foreach ($lots as $lot): ?>
                <?=include_template('_lot.php', ['lot' => $lot,
                    'expiryTime' => get_dt_range($lot['expiry_date'])])?>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php if (!empty($lots)): ?>
        <ul class="pagination-list">
            <li class="pagination-item pagination-item-prev"><a>Назад</a></li>
            <li class="pagination-item pagination-item-active"><a>1</a></li>
            <li class="pagination-item"><a href="#">2</a></li>
            <li class="pagination-item"><a href="#">3</a></li>
            <li class="pagination-item"><a href="#">4</a></li>
            <li class="pagination-item pagination-item-next"><a href="#">Вперед</a></li>
        </ul>
    <?php endif ?>
</div>
