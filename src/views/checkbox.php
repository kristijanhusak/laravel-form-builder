<?php if ($showLabel && $showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    <div <?= $options['wrapperAttrs'] ?> >
    <?php endif; ?>
<?php endif; ?>

    <?php if ($showField): ?>
        <?= Form::checkbox($name, $options['value'], $options['checked'], $options['attr']) ?>

        <?php if ($options['help_block']['text']): ?>
            <<?= $options['help_block']['tag'] ?> <?= $options['help_block']['helpBlockAttrs'] ?>>
                <?= $options['help_block']['text'] ?>
            </<?= $options['help_block']['tag'] ?>>
        <?php endif; ?>

    <?php endif; ?>

    <?php if ($showLabel && $options['label'] !== false): ?>
        <?php if ($options['is_child']): ?>
            <label <?= $options['labelAttrs'] ?>><?= $options['label'] ?></label>
        <?php else: ?>
            <?= Form::label($name, $options['label'], $options['label_attr']) ?>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($showError && isset($errors)): ?>
        <?php foreach ($errors->get($nameKey) as $err): ?>
            <div <?= $options['errorAttrs'] ?>><?= $err ?></div>
        <?php endforeach; ?>
    <?php endif; ?>

<?php if ($showLabel && $showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    </div>
    <?php endif; ?>
<?php endif; ?>
