<table class="table table-hover" id="data-import" data-action="<?php echo site_url('item_kits/action_import_data'); ?>">
    <thead>
        <tr>
            <th width="30px"></th>
            <th>
                <input type="checkbox" id="chk-all" />
                <label for="chk-all"><span></span></label>
            </th>
            <?php foreach ($columns as $column) :?>
            <th>
                <select name="columns[<?php echo $column; ?>]" class="form-control">
                    <option value="0"><?php echo $column; ?>. </option>
                    <?php foreach ($fields as $field) :?>
                    <option value="<?php echo $field; ?>"><?php echo $field; ?></option>
                    <?php endforeach; ?>
                </select>
            </th>
            <?php endforeach; ?>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php for ($index = 2; $index <= $num_rows; $index++) :?>
            <tr>
                <td width="30px"><strong><?php echo ($index); ?>. </strong></td>
                <td>
                    <input name="selected_rows[<?php echo ($index); ?>]" value="1" class="chk-row" type="checkbox" id="chk-row-<?php echo $index; ?>" />
                    <label for="chk-row-<?php echo $index; ?>"><span></span></label>
                </td>
                <?php foreach ($columns as $column) :?>
                <td>
                    <input name="rows[<?php echo ($index); ?>][<?php echo $column; ?>]" type="text" class="form-control" value="<?php echo $sheet->getCellByColumnAndRow($column, $index)->getValue(); ?>" />
                </td>
                <?php endforeach; ?>
                <td>
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
<script type="text/javascript">
    $("#chk-all").click(function() {
        var checked = $(this).prop("checked");
        $(".chk-row").each(function() {
            $(this).prop("checked", checked);
        });
    });
    var selected_columns = [];
    function action_select_column() {

    };
    function action_import_data(container_id) {
        var $container = $("#" + container_id);
        if ($container.size() == 0) {
            return false;
        }
        var $rows = $(".chk-row:checked");
        if ($rows.size() == 0) {
            show_feedback('error', "Bạn chưa chọn hàng nào", <?php echo json_encode(lang('common_error')); ?>);
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
                show_feedback('success', response.message, <?php echo json_encode(lang('common_success')); ?>);
            },
            error: function() {
                show_feedback('error', null, <?php echo json_encode(lang('common_error')); ?>);
            }
        });
        return false;
    };
</script>