<?php if (!empty($attribute)) :?>
<input class="form-control <?php if (!empty($attribute->required) && $attribute->required == Attribute::YES) :?>required<?php endif; ?>" type="text" name="attributes[<?php echo $attribute->id; ?>]" <?php if (!empty($attribute_values[$attribute->id])) :?>value="<?php echo $attribute_values[$attribute->id]->entity_value; ?>"<?php endif; ?> />
<?php endif; ?>