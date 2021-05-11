<fieldset <?= Html::attributes($options['wrapper']) ?>>
    <?php if ($options['label']): ?>
        <?= Html::tag('legend', $options['label'], $options['label_attr']); ?>
    <?php endif; ?>

    <?php foreach ($fields as $field): ?>
        <?= $field->render(); ?>
    <?php endforeach; ?>
</fieldset>
