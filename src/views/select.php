<?php if ($showLabel && $showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    <div <?= $options['wrapperAttrs'] ?> >
    <?php endif; ?>
<?php endif; ?>

<?php if(array_key_exists('label_template', $options) && $options['label_template']): ?>
<?= view($options['label_template'], get_defined_vars())->render(); ?>
<?php else: ?>
<?php include 'label.php' ?>
<?php endif; ?>

<?php if ($showField): ?>
    <?php $emptyVal = $options['empty_value'] ? ['' => $options['empty_value']] : null; ?>
    <?= Form::select($name, (array)$emptyVal + $options['choices'], $options['selected'], $options['attr']) ?>
    <?php include 'help_block.php' ?>
<?php endif; ?>

<?php include 'errors.php' ?>

<?php if ($showLabel && $showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    </div>
    <?php endif; ?>
<?php endif; ?>
