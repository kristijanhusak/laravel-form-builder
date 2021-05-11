<?php if ($showStart): ?>
    <?= Form::open($formOptions) ?>
<?php endif; ?>

<?php if ($showFields): ?>
    <?php foreach ($fields as $field): ?>
        <?= $field->render() ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php if ($showEnd): ?>
    <?= Form::close() ?>
<?php endif; ?>
