<?= Form::button($options['label'], $options['attr']) ?>
<?php if ($options['help_block']['text']): ?>
    <<?= $options['help_block']['tag'] ?> <?= $options['helpBlockAttrs'] ?>><?= $options['help_block']['text'] ?></<?= $options['help_block']['tag'] ?>>
<?php endif; ?>
