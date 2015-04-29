<?php if ($options['wrapper'] !== false): ?>
<div <?= $options['wrapperAttrs'] ?> >
<?php endif; ?>

<?= Form::button($options['label'], $options['attr']) ?>
<?php if ($options['help_block']['text']): ?>
    <<?= $options['help_block']['tag'] ?> <?= $options['help_block']['helpBlockAttrs'] ?>>
        <?= $options['help_block']['text'] ?>
    </<?= $options['help_block']['tag'] ?>>
<?php endif; ?>

<?php if ($options['wrapper'] !== false): ?>
</div>
<?php endif; ?>
