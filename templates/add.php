<nav class="nav">
  <ul class="nav__list container">
      <?php foreach ($categories as $category): ?>
          <li class="nav__item">
              <a href="category.php?id=<?=$category['id']?>"><?= esc($category['name']) ?></a>
          </li>
      <?php endforeach; ?>
  </ul>
</nav>
<form class="form form--add-lot container <?= isset($errors) ? "form--invalid" : ""; ?>" action="add.php" method="post" enctype="multipart/form-data"> <!-- form--invalid -->
  <h2>Добавление лота</h2>
  <div class="form__container-two">
      <?php $classname = isset($errors['lot-name']) ? "form__item--invalid" : ""; ?>
      <div class="form__item <?= $classname; ?>">
      <label for="lot-name">Наименование <sup>*</sup></label>
      <input id="lot-name" type="text" name="lot-name" value="<?= getPostVal('lot-name'); ?>" placeholder="Введите наименование лота">
      <?php if ($classname): ?>
          <span class="form__error"><?= $errors['lot-name']; ?></span>
      <?php endif ?>
    </div>
    <?php $classname = isset($errors['category-id']) ? "form__item--invalid" : ""; ?>
    <div class="form__item <?= $classname; ?>">
      <label for="category">Категория <sup>*</sup></label>
      <select id="category" name="category-id">
          <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>"
                      <?php if ($cat['id'] == getPostVal('category-id')): ?>selected<?php endif; ?>><?=$cat['name'];
                  ?></option>
          <?php endforeach; ?>
      </select>
      <?php if ($classname): ?>
        <span class="form__error"><?= $errors['category-id']; ?></span>
      <?php endif ?>
    </div>
  </div>
  <?php $classname = isset($errors['message']) ? "form__item--invalid" : ""; ?>
  <div class="form__item form__item--wide <?= $classname; ?>">
    <label for="message">Описание <sup>*</sup></label>
    <textarea id="message" name="message" placeholder="Напишите описание лота"><?= getPostVal('message'); ?></textarea>
    <?php if ($classname): ?>
      <span class="form__error"><?= $errors['message']; ?></span>
    <?php endif ?>
  </div>
  <?php $classname = isset($errors['file']) ? "form__item--invalid" : ""; ?>
  <div class="form__item form__item--file <?= $classname; ?>">
    <label>Изображение <sup>*</sup></label>
    <div class="form__input-file">
      <input class="visually-hidden" type="file" id="lot-img" name="lot-img"  value="<?= $lot['path']; ?>">
      <label for="lot-img">
        Добавить
      </label>
      <?php if ($classname): ?>
        <span class="form__error"><?= $errors['file']; ?></span>
      <?php endif ?>
    </div>
  </div>
  <div class="form__container-three">
    <?php $classname = isset($errors['lot-rate']) ? "form__item--invalid" : ""; ?>
    <div class="form__item form__item--small <?= $classname; ?>">
      <label for="lot-rate">Начальная цена <sup>*</sup></label>
      <input id="lot-rate" type="text" name="lot-rate" placeholder="0"  value="<?= getPostVal('lot-rate'); ?>">
      <?php if ($classname): ?>
        <span class="form__error"><?= $errors['lot-rate']; ?></span>
      <?php endif ?>
    </div>
    <?php $classname = isset($errors['lot-rate']) ? "form__item--invalid" : ""; ?>
    <div class="form__item form__item--small <?= $classname; ?>">
      <label for="lot-step">Шаг ставки <sup>*</sup></label>
      <input id="lot-step" type="text" name="lot-step" placeholder="0" value="<?= getPostVal('lot-step'); ?>">
      <?php if ($classname): ?>
        <span class="form__error"><?= $errors['lot-step']; ?></span>
      <?php endif ?>
    </div>
    <?php $classname = isset($errors['lot-date']) ? "form__item--invalid" : ""; ?>
    <div class="form__item <?= $classname; ?>">
      <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
      <input class="form__input-date" id="lot-date" type="text" name="lot-date" value="<?= getPostVal('lot-date'); ?>" placeholder="Введите дату в формате ГГГГ-ММ-ДД">
      <?php if ($classname): ?>
        <span class="form__error"><?= $errors['lot-date']; ?></span>
      <?php endif ?>
    </div>
  </div>
  <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
  <button type="submit" class="button">Добавить лот</button>
</form>
