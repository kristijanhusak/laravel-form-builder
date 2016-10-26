<?php if ($options['wrapper'] !== false): ?>
<div <?= $options['wrapperAttrs'] ?> >
<?php endif; ?>

<?= Form::button($options['label'], $options['attr']) ?>
<?php include 'help_block.php' ?>
<?php
if (isset($options['right_wrapper'])):
    echo "</div>";
endif;
?>
<?php if ($options['wrapper'] !== false): ?>
</div>
<?php endif; ?>
