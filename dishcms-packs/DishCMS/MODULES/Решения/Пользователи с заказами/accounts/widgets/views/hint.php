<?php
/** @var \accounts\widgets\Hint $this */
?>
<div style="display: none;" class="hint" id="hint<?=$this->id?>">
  <div class="form-hint">
    <div class="form-hint-img">
      <img src="<?= IBHelper::getElements(3)[$this->id]['preview'] ?>">
    </div>
    <div class="form-hint-description">
      <?= IBHelper::getElements(3)[$this->id]['description'] ?>
    </div>
  </div>
</div>