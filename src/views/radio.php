<?php if ($showLabel && $showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    <div <?= $options['wrapperAttrs'] ?> >
    <?php endif; ?>
<?php endif; ?>

<?php if ($showField): ?>
    <?= Form::radio($name, $options['value'], $options['checked'], $options['attr']) ?>

    <?php if(array_key_exists('label_template', $options) && $options['label_template']): ?>
    <?= view($options['label_template'], get_defined_vars())->render(); ?>
    <?php else: ?>
    <?php include 'label.php' ?>
    <?php endif; ?>

    <?php include 'help_block.php' ?>
<?php endif; ?>

<?php include 'errors.php' ?>

<?php if ($showLabel && $showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    </div>
    <?php endif; ?>
<?php endif; ?>
