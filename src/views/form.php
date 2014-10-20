<?php if ($showStart): ?>
    <?php if ($model && $model->exists): ?>
        <?= Form::model($model, $formOptions) ?>
    <?php else: ?>
        <?= Form::open($formOptions) ?>
    <?php endif; ?>
<?php endif; ?>

<?php if ($showFields): ?>
    <?php foreach ($fields as $field): ?>
        <?= $field->render() ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php if ($showEnd): ?>
    <?= Form::close() ?>
<?php endif; ?>
