<?php if (!empty($attribute_sets) && !empty($entity_info)) :?>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">
            <?php echo lang("common_select_attribute_set"); ?>
        </h3>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label for="attribute_set_id" class="col-sm-3 col-md-3 col-lg-2 control-label"><?php echo lang('common_select_attribute_set'); ?></label>
            <div class="col-sm-9 col-md-9 col-lg-10">
                <span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
                <select onchange="$('form').submit()" name="attribute_set_id" id="attribute_set_id" class="form-control">
                    <option value="0"><?php echo lang('common_select_attribute_set'); ?></option>
                    <?php foreach ($attribute_sets as $attribute_set) :?>
                    <option <?php if ($entity_info->attribute_set_id == $attribute_set->id) :?>selected="selected"<?php endif; ?> value="<?php echo $attribute_set->id; ?>"><?php echo $attribute_set->name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>