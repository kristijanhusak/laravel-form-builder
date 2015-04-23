<?php if ($showLabel && $showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    <div <?= $options['wrapperAttrs'] ?> >
    <?php endif; ?>
<?php endif; ?>

    <?php if ($showField): ?>
        <?= $options['children']['first']->render([], true, true, false) ?>
        <?= $options['children']['second']->render([], true, true, false) ?>

        <?php if ($options['help_block']['text']): ?>
            <<?= $options['help_block']['tag'] ?> <?= $options['help_block']['helpBlockAttrs'] ?>>
                <?= $options['help_block']['text'] ?>
            </<?= $options['help_block']['tag'] ?>>
        <?php endif; ?>

    <?php endif; ?>

    <?php if ($showError && isset($errors)): ?>
        <?= $options['children']['first']->render([], false, false, true) ?>
        <?= $options['children']['second']->render([], false, false, true) ?>
    <?php endif; ?>

<?php if ($showLabel && $showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    </div>
    <?php endif; ?>
<?php endif; ?>
