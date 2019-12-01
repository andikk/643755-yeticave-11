<nav class="nav">
    <ul class="nav__list container">
        <?php if (is_array($categories)) : ?>
            <?php foreach ($categories as $category): ?>
                <li class="nav__item">
                    <a href="category.php?id=<?=$category['id']?>"><?= esc($category['name']) ?></a>
                </li>
            <?php endforeach; ?>
        <?php endif ?>
    </ul>
</nav>
<p style="margin-left: 20px;"><?php print_r($error); ?></p>
