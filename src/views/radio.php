<?php if ($showLabel && $showField && !$options['is_child']): ?>
<div <?= $options['wrapperAttrs'] ?> >
<?php endif; ?>

    <?php if ($showField): ?>
        <?= Form::radio($name, $options['default_value'], $options['checked'], $options['attr']) ?>
    <?php endif; ?>

    <?php if ($showLabel): ?>
        <?php if ($options['is_child']): ?>
            <label <?= $options['labelAttrs'] ?>><?= $options['label'] ?></label>
        <?php else: ?>
            <?= Form::label($name, $options['label'], $options['label_attr']) ?>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($showError && isset($errors)): ?>
        <?= $errors->first($name, '<div '.$options['errorAttrs'].'>:message</div>') ?>
    <?php endif; ?>

<?php if ($showLabel && $showField && !$options['is_child']): ?>
</div>
<?php endif; ?>
