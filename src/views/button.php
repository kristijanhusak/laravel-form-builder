<?php if ($options['wrapper'] !== false): ?>
<div <?= $options['wrapperAttrs'] ?> >
<?php endif; ?>

<?= Form::button($options['label'], $options['attr']) ?>
<?php include helpBlockPath(); ?>

<?php if ($options['wrapper'] !== false): ?>
</div>
<?php endif; ?>
