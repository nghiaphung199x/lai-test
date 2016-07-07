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
                    <option value="basic:<?php echo $field; ?>"><?php echo lang('common_' . $field); ?></option>
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