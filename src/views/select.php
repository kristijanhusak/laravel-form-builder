<?php if ($showLabel && $showField): ?>
<div <?= $options['wrapperAttrs'] ?> >
<?php endif; ?>

    <?php if ($showLabel): ?>
    <?= Form::label($name, $options['label'], $options['label_attr']) ?>
    <?php endif; ?>

    <?php if ($showField): ?>
    <?= Form::select($name, (array)$options['empty_value'] + $options['choices'], (array)$options['selected'], $options['attr']) ?>
    <?php endif; ?>

    <?php if ($showError && isset($errors)): ?>
        <?= $errors->first($name, '<div '.$options['errorAttrs'].'>:message</div>') ?>
    <?php endif; ?>

<?php if ($showLabel && $showField): ?>
</div>
<?php endif; ?>
