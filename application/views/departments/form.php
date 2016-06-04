<?php $this->load->view("partial/header"); ?>
<div class="form">
    <?php echo form_open('departments/save/' . (!isset($is_clone) ? $entity->department_id : ''), array('id' => 'form-department',)); ?>
        <?php form_hidden('redirect_code', 2); ?>
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="ion-edit"></i>
                    <?php echo lang('departments_basic_information'); ?>
                    <span class="sub hidden-xs hidden-sm"> <?php echo lang('common_fields_required_message'); ?> </span>
                </h3>
            </div>
            <div class="panel-body bootstrap">

                <?php if (!empty($parents)) :?>
                <div class="form-group">
                    <?php echo form_label(lang('departments_field_parent').' :', 'department_parent_id', array('class' => '')); ?>
                    <div class="cl">
                        <select id="department_parent_id" name="department[parent_id]" class="form-control">
                            <option value="0" <?php if (empty($entity->parent_id)) :?>selected="selected"<?php endif; ?>>-- <?php echo lang('departments_root'); ?> --</option>
                            <?php foreach ($parents as $parent) :?>
                            <option <?php if ($parent->department_id == $entity->department_id) :?>disabled="true"<?php endif; ?> <?php if ($entity->parent_id == $parent->department_id) :?>selected="selected"<?php endif; ?> value="<?php echo $parent->department_id; ?>">
                                <?php echo $this->Department->get_level_line($parent, '--', true, false) . ' ' . $parent->name; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <?php echo form_label(lang('departments_field_name'), 'department_name', array('class' => '')); ?>
                    <div class="cl">
                        <?php echo form_input(array(
                                'name' => 'department[name]',
                                'id' => 'department_name',
                                'title' => lang('common_fields_required_alert'),
                                'class' => 'form-control form-inps required',
                                'value' => $entity->name
                        )); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo form_label(lang('departments_field_description'), 'department_description', array('class' => '')); ?>
                    <div class="cl">
                        <?php echo form_textarea(array(
                                'name' => 'department[description]',
                                'id' => 'department_description',
                                'rows' => '3',
                                'class' => 'form-control',
                                'value' => $entity->description
                        )); ?>
                    </div>
                </div>
                <div class="clear">
                    <div class="btn-group">
                        <a class="btn btn-default" href="<?php echo site_url('departments'); ?>"><?php echo lang('departments_btn_list'); ?></a>
                        <?php echo form_submit(array(
                            'name' => 'submit',
                            'id' => 'submit',
                            'value' => lang('departments_btn_save'),
                            'class' => 'btn btn-primary')
                        ); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript">
    var submitting = false;
    setTimeout(function(){$(":input:visible:first","#form-department").focus();}, 100);
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
    $('#form-department').validate({
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
                        window.location.href = '<?php echo site_url('departments'); ?>';
                    } else {
                        $("html, body").animate({ scrollTop: 0 }, "slow");
                    }
                },
                <?php if(!$entity->department_id) :?>
                resetForm: true,
                <?php endif ?>
                dataType:'json'
            });
        }
    });
</script>
<?php $this->load->view("partial/footer"); ?>
