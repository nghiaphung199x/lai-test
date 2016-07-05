<div id="data-import" data-action="<?php echo site_url('suppliers/action_import_data'); ?>">
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
                    <option value="basic:<?php echo $field; ?>"><?php echo lang('suppliers_' . $field); ?></option>
                    <?php endforeach; ?>
                    <?php foreach ($person_fields as $field) :?>
                    <option value="person:<?php echo $field; ?>"><?php echo lang('common_' . $field); ?></option>
                    <?php endforeach; ?>
                </optgroup>
                <optgroup class="attributes-by-set" id="attributes-by-set" label="<?php echo lang('common_attributes_by_set'); ?>"></optgroup>
            </select>
        </div>
        <?php endif; ?>
    </div>
    <table class="table table-hover mt-10">
        <thead>
            <tr>
                <th width="30px"></th>
                <th>
                    <input type="checkbox" id="chk-all" />
                    <label for="chk-all"><span></span></label>
                </th>
                <?php foreach ($columns as $column) :?>
                <th>
                    <select old_value="" onfocus="this.old_value = this.value" onchange="return action_select_column($(this))" name="columns[<?php echo $column; ?>]" class="form-control">
                        <option value="0"><?php echo lang('common_column') . ' ' . $column; ?></option>
                        <optgroup label="<?php echo lang('common_basic_attributes'); ?>">
                            <?php foreach ($fields as $field) :?>
                            <option value="basic:<?php echo $field; ?>"><?php echo lang('suppliers_' . $field); ?></option>
                            <?php endforeach; ?>
                            <?php foreach ($person_fields as $field) :?>
                            <option value="person:<?php echo $field; ?>"><?php echo lang('common_' . $field); ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                        <optgroup class="attributes-by-set" label="<?php echo lang('common_attributes_by_set'); ?>"></optgroup>
                    </select>
                </th>
                <?php endforeach; ?>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php for ($index = 1; $index <= $num_rows; $index++) :?>
                <tr>
                    <td width="45px" <?php if ($index > 1) :?>class="selected"<?php endif; ?>><strong><?php echo ($index); ?>. </strong></td>
                    <td <?php if ($index > 1) :?>class="selected"<?php endif; ?>>
                        <input <?php if ($index > 1) :?>checked="checked"<?php endif; ?> name="selected_rows[<?php echo ($index); ?>]" value="1" class="chk-row" type="checkbox" id="chk-row-<?php echo $index; ?>" />
                        <label for="chk-row-<?php echo $index; ?>"><span></span></label>
                    </td>
                    <?php $column_index = 0; foreach ($columns as $column) :?>
                    <td <?php if ($index > 1) :?>class="selected"<?php endif; ?>>
                        <?php $value = $sheet->getCellByColumnAndRow($column_index, $index)->getValue(); (is_object($value)) ? $value = $value->getPlainText() : $value; ?>
                        <input name="rows[<?php echo ($index); ?>][<?php echo $column; ?>]" type="text" class="form-control" value="<?php echo $value; ?>" />
                        <?php unset($value); ?>
                    </td>
                    <?php $column_index++; endforeach; ?>
                    <td <?php if ($index > 1) :?>class="selected"<?php endif; ?>>
                        <button type="button" onclick="$(this).parent().parent().remove()" class="btn btn-sm btn-primary"><?php echo lang('common_delete'); ?></button>
                    </td>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>
    <div class="clearfix">
        <div class="pull-right">
            <button type="button" class="btn btn-primary" onclick="action_import_data('data-import');"><?php echo lang('common_submit'); ?></button>
        </div>
    </div>
</div>
<script type="text/javascript">

    $("#chk-all").click(function() {
        var checked = $(this).prop("checked");
        $(".chk-row").each(function() {
            $(this).prop("checked", checked);
            if (checked) {
                $(this).parent().parent().find("td").addClass("selected");
            } else {
                $(this).parent().parent().find("td").removeClass("selected");
            }
        });
    });

    $(".chk-row").click(function() {
        var checked = $(this).prop("checked");
        if (checked) {
            $(this).parent().parent().find("td").addClass("selected");
        } else {
            $(this).parent().parent().find("td").removeClass("selected");
        }
    });

    var selected_columns = [];
    var common_not_yet_selected_columns = "<?php echo lang('common_not_yet_selected_columns'); ?>";
    var common_not_yet_selected_rows = "<?php echo lang('common_not_yet_selected_rows'); ?>";
    var common_select_duplicated_column = "<?php echo lang('common_select_duplicated_column'); ?>";
    var common_not_yet_selected_duplicate_field = "<?php echo lang('common_not_yet_selected_duplicate_field'); ?>";

    function get_options_after_select_attribute_set(data) {
        var options = "";
        console.log(data);
        for (var index = 0; index < data.length; index++) {
            options += "<option value='extend:" + data[index].attribute_id + "'>" + data[index].name + "</option>";
        }
        return options;
    };
    function action_load_attributes_by_set($select, selector) {
        var action = $select.attr("data-action");
        var $containers = $(selector);
        if ($containers.size() == 0) {
            return false;
        }
        var data = {
            "attribute_set_id": $select.val()
        };
        if (data.attribute_set_id == 0) {
            $containers.each(function() {
                $(this).html("");
            });
            return false;
        }
        $.ajax({
            url: action,
            data: data,
            type: "POST",
            cache: false,
            success: function(response) {
                response = $.parseJSON(response);
                if (response.success && response.attributes != null) {
                    var options = get_options_after_select_attribute_set(response.attributes);
                    $containers.each(function() {
                        $(this).html(options);
                    });
                }
            },
            error: function() {
                show_feedback('error', null, <?php echo json_encode(lang('common_error')); ?>);
            }
        });
        return false;
    };
    function action_select_column($select) {
        var value = $select.val();
        var old_value = $select.prop("old_value");
        if (value != old_value) {
            var index = selected_columns.indexOf(old_value);
            if (index > -1) {
                selected_columns.splice(index, 1);
            }
        }
        if (selected_columns.indexOf(value) >= 0) {
            show_feedback('error', common_select_duplicated_column, <?php echo json_encode(lang('common_error')); ?>);
            $select.prop('selectedIndex', 0);
            var index = selected_columns.indexOf(value);
            if (index > -1) {
                selected_columns.splice(value, 1);
            }
            return false;
        } else {
            selected_columns.push(value);
        }
        return true;
    };
    function action_import_data(container_id) {
        var $container = $("#" + container_id);
        if ($container.size() == 0) {
            return false;
        }
        var $rows = $(".chk-row:checked");
        if ($rows.size() == 0) {
            show_feedback('error', common_not_yet_selected_rows, <?php echo json_encode(lang('common_error')); ?>);
            return false;
        }
        if (selected_columns.length == 0) {
            show_feedback('error', common_not_yet_selected_columns, <?php echo json_encode(lang('common_error')); ?>);
            return false;
        }
        var check_duplicate_field = $("#check-duplicate-field").val();
        if (check_duplicate_field == 0) {
            show_feedback('error', common_not_yet_selected_duplicate_field, <?php echo json_encode(lang('common_error')); ?>);
            return false;
        }
        var data = $container.find("input,select").serialize();
        var action = $container.attr("data-action");
        $.ajax({
            url: action,
            data: data,
            type: "POST",
            cache: false,
            success: function(response) {
                response = $.parseJSON(response);
                $container.html("");
                if (response.success) {
                    show_feedback('success', response.message, <?php echo json_encode(lang('common_success')); ?>);
                } else {
                    show_feedback('error', response.message, <?php echo json_encode(lang('common_error')); ?>);
                }
            },
            error: function() {
                show_feedback('error', null, <?php echo json_encode(lang('common_error')); ?>);
            }
        });
        return false;
    };
</script>