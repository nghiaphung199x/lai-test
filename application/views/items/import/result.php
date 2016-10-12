<div id="data-import" data-action="<?php echo site_url('items/action_import_data'); ?>">
    <div class="row">
        <?php if (!empty($attribute_sets)) :?>
        <div class="col-md-4">
            <label><i class="icon ti-settings"></i> <?php echo lang('common_select_attribute_set'); ?></label>
            <select data-action="<?php echo site_url('attribute_sets/action_get_attributes'); ?>" onchange="action_load_attributes_by_set($(this), '.attributes-by-set')" class="form-control" name="attribute_set_id" id="attribute_set">
                <option value="0"><?php echo lang('common_select_attribute_set'); ?></option>
                <?php foreach ($attribute_sets as $attribute_set) :?>
                <option value="<?php echo $attribute_set->id; ?>"><?php echo $attribute_set->name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        <?php if (!empty($fields)) :?>
        <div class="col-md-4">
            <label><i class="icon ti-settings"></i> <?php echo lang('common_select_field_to_check_duplicate'); ?></label>
            <select class="form-control" name="check_duplicate_field" id="check-duplicate-field">
                <option value="0"><?php echo lang('common_select_field_to_check_duplicate'); ?></option>
                <optgroup label="<?php echo lang('common_basic_attributes'); ?>">
                    <?php foreach ($fields as $field) :?>
                    <option value="basic:<?php echo $field; ?>"><?php echo lang('common_' . $field); ?></option>
                    <?php endforeach; ?>
                </optgroup>
                <optgroup class="attributes-by-set" id="attributes-by-set" label="<?php echo lang('common_attributes_by_set'); ?>"></optgroup>
            </select>
        </div>
        <?php endif; ?>
    </div>
    <?php $this->load->view('items/import/result/rows'); ?>
    <div class="clearfix">
        <div class="pull-right">
            <button type="button" class="btn btn-primary" onclick="action_import_data('data-import');"><?php echo lang('common_submit'); ?></button>
        </div>
    </div>
</div>

<?php $this->load->view('import/client_script'); ?>