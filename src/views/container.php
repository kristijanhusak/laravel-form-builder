<?php if ($showLabel || $showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
        <div <?= $options['wrapperAttrs'] ?>>
    <?php endif; ?>

    <?php if ($showLabel && $options['label'] !== false && $options['label_show']): ?>
        <?= Form::customLabel($name, $options['label'], $options['label_attr']) ?>
    <?php endif; ?>

    <?php if ($showField): ?>
        <div class="<?= $options['container_class'] ?? '' ?>">
            <?php foreach ((array)$options['children'] as $child): ?>
                <?= $child->render() ?>
            <?php endforeach; ?>
        </div>


        <?php include 'help_block.php' ?>
        <?php include 'errors.php' ?>
    <?php endif; ?>

    <?php if ($options['wrapper'] !== false): ?>
        </div>
    <?php endif; ?>
<?php endif; ?>
