<script type="text/javascript">

    var submitting = false;

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
        if (submitting) {
            return;
        }
        submitting = true;
        $('#grid-loader').show();
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
                submitting = false;
                $('#grid-loader').hide();
            },
            error: function() {
                show_feedback('error', null, <?php echo json_encode(lang('common_error')); ?>);
                submitting = false;
                $('#grid-loader').hide();
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
        if (submitting) {
            return;
        }
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
        submitting = true;
        $('#grid-loader').show();
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
                if (response.error_html != undefined) {
                    $container.html(response.error_html);
                }
                if (response.success) {
                    show_feedback('success', response.message, <?php echo json_encode(lang('common_success')); ?>);
                } else {
                    show_feedback('error', response.message, <?php echo json_encode(lang('common_error')); ?>);
                }
                submitting = false;
                $('#grid-loader').hide();
            },
            error: function() {
                show_feedback('error', null, <?php echo json_encode(lang('common_error')); ?>);
                submitting = false;
                $('#grid-loader').hide();
            }
        });
        return false;
    };

</script>