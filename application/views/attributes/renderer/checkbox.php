<?php if (!empty($attribute)) :?>
    <?php if (!empty($attribute_values[$attribute->id]->entity_value)) :?>
        <?php $attribute_values[$attribute->id]->entity_value = explode(',', $attribute_values[$attribute->id]->entity_value); ?>
    <?php endif; ?>
    <?php if (!empty($attribute->options)) :?>
    <?php $options = @unserialize($attribute->options); ?>
    <?php endif; ?>
    <?php if (!empty($options)) :?>
        <div class="clearfix">
            <?php $index = 1; foreach ($options as $option) :?>
                <div class="pull-left mr-10">
                <input id="attribute-option-<?php echo $index; ?>" <?php if (!empty($attribute_values[$attribute->id]) && in_array($option['value'], $attribute_values[$attribute->id]->entity_value)) :?>checked="checked"<?php endif; ?> type="checkbox" name="attributes[<?php echo $attribute->id; ?>][]" value="<?php echo $option['value']; ?>" class="module_checkboxes" />
                <label for="attribute-option-<?php echo $index; ?>">
                    <span class="ml-10"></span>
                    <?php echo $option['label']; ?>
                </label>
                </div>
            <?php $index++; endforeach; unset($index); ?>
            <div class="clearfix"></div>
        </div>
    <?php endif; ?>
<?php endif; ?>