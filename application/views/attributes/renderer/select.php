<?php if (!empty($attribute)) :?>
<?php if (!empty($attribute->options)) :?>
<?php $options = @unserialize($attribute->options); ?>
<?php endif; ?>
<select class="form-control <?php if (!empty($attribute->required) && $attribute->required == Attribute::YES) :?>required<?php endif; ?>" name="attributes[<?php echo $attribute->id; ?>]">
    <?php if (!empty($options)) :?>
    <?php foreach ($options as $option) :?>
    <option <?php if (!empty($attribute_values[$attribute->id]) && $attribute_values[$attribute->id]->entity_value == $option['value']) :?>selected="selected"<?php endif; ?> value="<?php echo $option['value']; ?>">
        <?php echo $option['label']; ?>
    </option>
    <?php endforeach; ?>
    <?php endif; ?>
</select>
<?php endif; ?>