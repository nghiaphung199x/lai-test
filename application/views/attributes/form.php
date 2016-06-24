<?php $this->load->view("partial/header"); ?>
<div class="form">
    <?php echo form_open('attributes/save/' . (!isset($is_clone) ? $entity->id : ''), array('id' => 'form-attribute',)); ?>
        <?php form_hidden('redirect_code', 2); ?>
        <div class="row">
            <div class="col-md-8">
                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="ion-edit"></i>
                            <?php echo lang('attributes_basic_information'); ?>
                            <span class="sub hidden-xs hidden-sm"> <?php echo lang('common_fields_required_message'); ?> </span>
                        </h3>
                    </div>
                    <div class="panel-body bootstrap">

                        <div class="form-group">
                            <?php echo form_label(lang('attributes_field_name'), 'attribute_name', array('class' => '')); ?>
                            <div class="cl">
                                <?php echo form_input(array(
                                'name' => 'attribute[name]',
                                'id' => 'attribute_name',
                                'title' => lang('common_fields_required_alert'),
                                'class' => 'form-control form-inps required',
                                'value' => $entity->name
                            )); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php echo form_label(lang('attributes_field_code'), 'attribute_code', array('class' => '')); ?>
                                    <div class="cl">
                                        <?php echo form_input(array(
                                        'name' => 'attribute[code]',
                                        'id' => 'attribute_code',
                                        'title' => lang('common_fields_required_alert'),
                                        'class' => 'form-control form-inps required',
                                        'value' => $entity->code
                                    )); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <?php echo form_label(lang('attributes_field_type'), 'attribute_type', array('class' => '')); ?>
                                    <?php if (!empty($attribute_types)) :?>
                                    <select id="attribute_type" name="attribute[type]" class="form-control">
                                        <?php foreach ($attribute_types as $type => $label) :?>
                                        <option <?php if ($entity->type == $type) :?>selected="selected"<?php endif; ?> value="<?php echo $type; ?>"><?php echo lang($label); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <?php echo form_label(lang('attributes_field_description'), 'attribute_description', array('class' => '')); ?>
                            <div class="cl">
                                <?php echo form_textarea(array(
                                'name' => 'attribute[description]',
                                'id' => 'attribute_description',
                                'rows' => '3',
                                'class' => 'form-control',
                                'value' => $entity->description
                            )); ?>
                            </div>
                        </div>

                        <div class="clear">
                            <div class="btn-group">
                                <a class="btn btn-default" href="<?php echo site_url('attributes'); ?>"><?php echo lang('attributes_btn_list'); ?></a>
                                <?php echo form_submit(array(
                                    'name' => 'submit',
                                    'id' => 'submit',
                                    'value' => lang('attributes_btn_save'),
                                    'class' => 'btn btn-primary')
                            ); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="glyphicon glyphicon-list"></i>
                            <?php echo lang('attributes_option_information'); ?>
                        </h3>
                    </div>
                    <div class="panel-body bootstrap" id="options">
                        <button type="button" onclick="Attribute.Option.createNew('#origin-option', '#options')" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-plus"></i> <?php echo lang('attributes_option_add'); ?></button>
                        <div id="origin-option" style="display: none" class="form-group well well-sm mt-10">
                            <div class="row">
                                <div class="col-md-5">
                                    <label><?php echo lang('attributes_option_label'); ?></label>
                                    <input placeholder="<?php echo lang('attributes_option_label'); ?>" class="form-control attribute-option-label" value="" />
                                </div>
                                <div class="col-md-5">
                                    <label><?php echo lang('attributes_option_value'); ?></label>
                                    <input placeholder="<?php echo lang('attributes_option_value'); ?>" class="form-control attribute-option-value" value="" />
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <button onclick="$(this).parent().parent().parent().remove()" type="button" class="form-control btn btn-default"><i class="glyphicon glyphicon-remove"></i> <?php echo lang('attributes_option_remove'); ?></button>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($entity->options)) :?>
                        <?php $index = 1; foreach ($entity->options as $option) :?>
                        <div class="form-group well well-sm mt-10">
                            <div class="row">
                                <div class="col-md-5">
                                    <label><?php echo lang('attributes_option_label'); ?></label>
                                    <input name="attribute[options][<?php echo $index; ?>][label]" placeholder="<?php echo lang('attributes_option_label'); ?>" class="form-control attribute-option-label" value="<?php echo $option['label']; ?>" />
                                </div>
                                <div class="col-md-5">
                                    <label><?php echo lang('attributes_option_value'); ?></label>
                                    <input name="attribute[options][<?php echo $index; ?>][value]" placeholder="<?php echo lang('attributes_option_value'); ?>" class="form-control attribute-option-value" value="<?php echo $option['value']; ?>" />
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <button onclick="$(this).parent().parent().parent().remove()" type="button" class="form-control btn btn-default"><i class="glyphicon glyphicon-remove"></i> <?php echo lang('attributes_option_remove'); ?></button>
                                </div>
                            </div>
                        </div>
                        <?php $index++; endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="glyphicon glyphicon-cog"></i>
                            <?php echo lang('attributes_setting_information'); ?>
                        </h3>
                    </div>
                    <div class="panel-body bootstrap">
                        <table class="table">
                            <tbody>
                            <tr>
                                <td class="no-border">
                                    &nbsp;
                                    <?php
                                    $checkbox_options = array(
                                        'name' => 'attribute[required]',
                                        'id' => 'attribute_field_required',
                                        'value' => 1,
                                        'checked' => !empty($entity->required),
                                        'class' => 'module_checkboxes '
                                    );
                                    ?>
                                    <?php echo form_checkbox($checkbox_options) . '<label for="attribute_field_required"><span></span></label>'; ?>
                                    <span class="text-info"><?php echo lang('attribute_field_required'); ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    &nbsp;
                                    <?php
                                    $checkbox_options = array(
                                        'name' => 'attribute[filterable]',
                                        'id' => 'attribute_field_filterable',
                                        'value' => 1,
                                        'checked' => !empty($entity->filterable),
                                        'class' => 'module_checkboxes '
                                    );
                                    ?>
                                    <?php echo form_checkbox($checkbox_options) . '<label for="attribute_field_filterable"><span></span></label>'; ?>
                                    <span class="text-info"><?php echo lang('attribute_field_filterable'); ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    &nbsp;
                                    <?php
                                    $checkbox_options = array(
                                        'name' => 'attribute[sortable]',
                                        'id' => 'attribute_field_sortable',
                                        'value' => 1,
                                        'checked' => !empty($entity->sortable),
                                        'class' => 'module_checkboxes '
                                    );
                                    ?>
                                    <?php echo form_checkbox($checkbox_options) . '<label for="attribute_field_sortable"><span></span></label>'; ?>
                                    <span class="text-info"><?php echo lang('attribute_field_sortable'); ?></span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript">

    var Attribute = {};
    Attribute.Option = {};
    Attribute.Option.createNew = function(clone_selector, container_selector) {
        var $clone = $(clone_selector);
        if ($clone.size() == 0) {
            return false;
        }
        var $container = $(container_selector);
        if ($container.size() == 0) {
            return false;
        }
        var $item = $clone.clone().show();
        var id = (new Date()).getTime();
        $item.find(".attribute-option-label").attr("name", "attribute[options][" + id + "][label]");
        $item.find(".attribute-option-value").attr("name", "attribute[options][" + id + "][value]");
        $container.append($item);
        $item.find(".attribute-option-label").focus();
        return false;
    };

    var submitting = false;
    setTimeout(function(){$(":input:visible:first","#form-attribute").focus();}, 100);
    $(".module_checkboxes").change(function()
    {
        if ($(this).prop('checked'))
        {
            $(this).parent().find('input[type=checkbox]').not(':disabled').prop('checked', true);
        }
        else
        {
            $(this).parent().find('input[type=checkbox]').not(':disabled').prop('checked', false);
        }
    });
    $(".module_action_checkboxes").change(function()
    {
        if ($(this).prop('checked'))
        {
            $('#'+$(this).data('module-checkbox-id')).prop('checked', true);
        }
    });
    $('#form-attribute').validate({
        submitHandler: function(form) {
            $('#grid-loader').show();
            if (submitting) {
                return;
            }
            submitting = true;
            $(form).ajaxSubmit({
                success: function(response) {
                    $('#grid-loader').hide();
                    submitting = false;
                    show_feedback(response.success ? 'success': 'error', response.message, response.success ? <?php echo json_encode(lang('common_success')); ?>  : <?php echo json_encode(lang('common_error')); ?>);
                    if(response.redirect_code == 2 && response.success) {
                        window.location.href = '<?php echo site_url('attributes'); ?>';
                    } else {
                        $("html, body").animate({ scrollTop: 0 }, "slow");
                    }
                },
                <?php if(!$entity->id) :?>
                resetForm: true,
                <?php endif ?>
                dataType:'json'
            });
        }
    });
</script>
<?php $this->load->view("partial/footer"); ?>
