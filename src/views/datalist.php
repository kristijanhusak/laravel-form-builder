<datalist id="<?php echo e($options['id'] ?: $options['name']) ?>">
	<?php foreach ($options['choices'] as $value => $label): ?>
		<option value="<?php echo e(is_int($value) ? $label : $value) ?>">
			<?php echo !is_int($value) && $label != $value ? e($label) : '' ?>
		</option>
	<?php endforeach ?>
</datalist>
