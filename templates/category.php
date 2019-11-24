<nav class="nav">
  <ul class="nav__list container">
      <?php foreach ($categories as $category): ?>
          <li class="nav__item">
              <a href="category.php?id=<?=$category['id']?>"><?= esc($category['name']) ?></a>
          </li>
      <?php endforeach; ?>
  </ul>
</nav>
<div class="container">
    <section class="lots">
        <h2>Все лоты в категории «<span><?= $category_name ?></span>»</h2>
        <?php if (empty($lots)): ?>
            <p>В данного категории нет лотов</p>
        <?php endif ?>
        <ul class="lots__list">
            <?php foreach ($lots as $lot): ?>
                <?=include_template('_lot.php', ['lot' => $lot,
                    'expiryTime' => get_dt_range($lot['expiry_date'])])?>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php if ($pages_count > 1): ?>
        <ul class="pagination-list">
            <li class="pagination-item pagination-item-prev"><a href="<?=$prev_page_link?>">Назад</a></li>
            <?php foreach ($pages as $page): ?>
                <li class="pagination-item <?php if ($page == $cur_page): ?>pagination-item-active<?php endif; ?>"><a href="<?=$url?>&page=<?=$page;?>"><?=$page;?></a></li>
            <?php endforeach; ?>
            <li class="pagination-item pagination-item-next"><a href="<?=$next_page_link?>">Вперед</a></li>
        </ul>
    <?php endif ?>
</div>
