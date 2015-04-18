<?php if ($showStart): ?>
    <?php if ($model): ?>
        <?= Form::model($model, $formOptions) ?>
    <?php else: ?>
        <?= Form::open($formOptions) ?>
    <?php endif; ?>
<?php endif; ?>

<?php if ($showFields): ?>
    <?php foreach ($fields as $field): ?>
    	<?php if( ! in_array($field->getName(), $exclude) ) { ?>
        	<?= $field->render() ?>
		<?php } ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php if ($showEnd): ?>
    <?= Form::close() ?>
<?php endif; ?>
