<?php $this->load->view("partial/header"); ?>
<div class="form">
    <?php echo form_open('groups/save/' . (!isset($is_clone) ? $entity->group_id : ''), array('id' => 'form-group',)); ?>
        <?php form_hidden('redirect_code', 2); ?>
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="ion-edit"></i>
                    <?php echo lang('groups_basic_information'); ?>
                    <span class="sub hidden-xs hidden-sm"> <?php echo lang('common_fields_required_message'); ?> </span>
                </h3>
            </div>
            <div class="panel-body bootstrap">
                <div class="form-group">
                    <?php echo form_label(lang('groups_field_name'), 'group_name', array('class' => '')); ?>
                    <div class="cl">
                        <?php echo form_input(array(
                                'name' => 'group[name]',
                                'id' => 'group_name',
                                'title' => lang('common_fields_required_alert'),
                                'class' => 'form-control form-inps required',
                                'value' => $entity->name
                        )); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo form_label(lang('groups_field_description'), 'group_description', array('class' => '')); ?>
                    <div class="cl">
                        <?php echo form_textarea(array(
                                'name' => 'group[description]',
                                'id' => 'group_description',
                                'rows' => '3',
                                'class' => 'form-control',
                                'value' => $entity->description
                        )); ?>
                    </div>
                </div>
                <div class="clear">
                    <div class="btn-group">
                        <a class="btn btn-default" href="<?php echo site_url('groups'); ?>"><?php echo lang('groups_btn_list'); ?></a>
                        <?php echo form_submit(array(
                            'name' => 'submit',
                            'id' => 'submit',
                            'value' => lang('groups_btn_save'),
                            'class' => 'btn btn-primary')
                        ); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="icon ti-settings"></i>
                    <?php echo lang('groups_permission_information'); ?>
                </h3>
            </div>
            <div class="panel-body bootstrap">
                <ul id="permission_list" class="list-unstyled">
                    <?php
                    foreach($all_modules->result() as $module)
                    {
                        $checkbox_options = array(
                            'name' => 'permissions[]',
                            'id' => 'permissions'.$module->module_id,
                            'value' => $module->module_id,
                            'checked' => $this->Group->has_module_permission($module->module_id,$entity->group_id),
                            'class' => 'module_checkboxes '
                        );

                        if ($logged_in_employee_id != 1)
                        {
                            if(($current_employee_editing_self && $checkbox_options['checked']) || !$this->Employee->has_module_permission($module->module_id,$logged_in_employee_id))
                            {
                                $checkbox_options['disabled'] = 'disabled';

                                //Only send permission if checked
                                if ($checkbox_options['checked'])
                                {
                                    echo form_hidden('permissions[]', $module->module_id);
                                }
                            }
                        }
                        ?>
                        <li>
                            <?php echo form_checkbox($checkbox_options).'<label for="permissions'.$module->module_id.'"><span></span></label>'; ?>
                            <span class="text-success"><?php echo lang('module_'.$module->module_id);?>:</span>
                            <span class="text-warning"><?php echo lang('module_'.$module->module_id.'_desc');?></span>
                            <ul class="list-unstyled list-permission-actions">
                                <?php
                                foreach($this->Module_action->get_module_actions($module->module_id)->result() as $module_action)
                                {
                                    $checkbox_options = array(
                                        'name' => 'permissions_actions[]',
                                        'data-module-checkbox-id' => 'permissions'.$module->module_id,
                                        'class' => 'module_action_checkboxes',
                                        'id' => 'permissions_actions'.$module_action->module_id."|".$module_action->action_id,
                                        'value' => $module_action->module_id."|".$module_action->action_id,
                                        'checked' => $this->Group->has_module_action_permission($module->module_id, $module_action->action_id, $entity->group_id)
                                    );

                                    if ($logged_in_employee_id != 1)
                                    {
                                        if(($current_employee_editing_self && $checkbox_options['checked']) || (!$this->Employee->has_module_action_permission($module->module_id,$module_action->action_id,$logged_in_employee_id)))
                                        {
                                            $checkbox_options['disabled'] = 'disabled';

                                            //Only send permission if checked
                                            if ($checkbox_options['checked'])
                                            {
                                                echo form_hidden('permissions_actions[]', $module_action->module_id."|".$module_action->action_id);
                                            }
                                        }
                                    }
                                    ?>
                                    <li>
                                        <?php echo form_checkbox($checkbox_options).'<label for="permissions_actions'.$module_action->module_id."|".$module_action->action_id.'"><span></span></label>'; ?>
                                        <span class="text-info"><?php echo lang($module_action->action_name_key);?></span>
                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript">
    var submitting = false;
    setTimeout(function(){$(":input:visible:first","#form-group").focus();}, 100);
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
    $('#form-group').validate({
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
                        window.location.href = '<?php echo site_url('groups'); ?>';
                    } else {
                        $("html, body").animate({ scrollTop: 0 }, "slow");
                    }
                },
                <?php if(!$entity->group_id) :?>
                resetForm: true,
                <?php endif ?>
                dataType:'json'
            });
        }
    });
</script>
<?php $this->load->view("partial/footer"); ?>
