<?php if ($showLabel && $showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    <div <?= $options['wrapperAttrs'] ?> >
    <?php endif; ?>
<?php endif; ?>

    <?php if ($showLabel && $options['label'] !== false): ?>
        <label <?= $options['labelAttrs'] ?>><?= $options['label'] ?></label>
    <?php endif; ?>

    <?php if ($showField): ?>
        <<?= $options['tag'] ?> <?= $options['elemAttrs'] ?>><?= $options['value'] ?></<?= $options['tag'] ?>>

        <?php if ($options['help_block']['text']): ?>
            <<?= $options['help_block']['tag'] ?> <?= $options['help_block']['helpBlockAttrs'] ?>>
                <?= $options['help_block']['text'] ?>
            </<?= $options['help_block']['tag'] ?>>
        <?php endif; ?>

    <?php endif; ?>


<?php if ($showLabel && $showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    </div>
    <?php endif; ?>
<?php endif; ?>
