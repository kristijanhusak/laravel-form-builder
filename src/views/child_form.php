<?php if ($showLabel && $showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    <div <?= $options['wrapperAttrs'] ?> >
    <?php endif; ?>
<?php endif; ?>

<?php /** label rendering section */ ?>
<?php if ($showLabel && $options['label'] !== false && $options['label_show']): ?>
    <?php if(array_key_exists('label_template', $options) && $options['label_template']): ?>
        <?= view($options['label_template'], get_defined_vars())->render(); ?>
    <?php else: ?>
        <?php include labelBlockPath(); ?>
    <?php endif; ?>
<?php endif; ?>

<?php if ($showField): ?>
    <?php foreach ((array)$options['children'] as $child): ?>
        <?php if( ! in_array( $child->getRealName(), (array)$options['exclude']) ) { ?>
            <?= $child->render() ?>
        <?php } ?>
    <?php endforeach; ?>

    <?php include helpBlockPath(); ?>

<?php endif; ?>

<?php include errorBlockPath(); ?>

<?php if ($showLabel && $showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    </div>
    <?php endif; ?>
<?php endif; ?>
