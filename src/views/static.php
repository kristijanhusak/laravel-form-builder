<?php if ($showLabel && $showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    <div <?= $options['wrapperAttrs'] ?> >
    <?php endif; ?>
<?php endif; ?>

<?php if ($showLabel && $options['label'] !== false && $options['label_show']): ?>
    <?= Form::customLabel($name, $options['label'], $options['label_attr']) ?>
<?php endif; ?>
        <?php
        if (isset($options['right_wrapper'])):
            echo "<div " . app('laravel-form-helper')->prepareAttributes((array)$options['right_wrapper']) . " >";
        endif;
        ?>
<?php if ($showField): ?>

    <<?= $options['tag'] ?> <?= $options['elemAttrs'] ?>><?= $options['value'] ?></<?= $options['tag'] ?>>

    <?php include 'help_block.php' ?>

<?php endif; ?>
<?php
if (isset($options['right_wrapper'])):
    echo "</div>";
endif;
?>

<?php if ($showLabel && $showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    </div>
    <?php endif; ?>
<?php endif; ?>
