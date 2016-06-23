<?php $this->load->view("partial/header"); ?>
<div class="form">
    <?php echo form_open('attribute_sets/save/' . (!isset($is_clone) ? $entity->id : ''), array('id' => 'form-attribute_set',)); ?>
        <?php form_hidden('redirect_code', 2); ?>
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="ion-edit"></i>
                    <?php echo lang('attribute_sets_basic_information'); ?>
                    <span class="sub hidden-xs hidden-sm"> <?php echo lang('common_fields_required_message'); ?> </span>
                </h3>
            </div>
            <div class="panel-body bootstrap">

                <div class="form-group">
                    <?php echo form_label(lang('attribute_sets_field_name'), 'attribute_set_name', array('class' => '')); ?>
                    <div class="cl">
                        <?php echo form_input(array(
                                'name' => 'attribute_set[name]',
                                'id' => 'attribute_set_name',
                                'title' => lang('common_fields_required_alert'),
                                'class' => 'form-control form-inps required',
                                'value' => $entity->name
                        )); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo form_label(lang('attribute_sets_field_description'), 'attribute_set_description', array('class' => '')); ?>
                    <div class="cl">
                        <?php echo form_textarea(array(
                                'name' => 'attribute_set[description]',
                                'id' => 'attribute_set_description',
                                'rows' => '3',
                                'class' => 'form-control',
                                'value' => $entity->description
                        )); ?>
                    </div>
                </div>
                <div class="clear">
                    <div class="btn-group">
                        <a class="btn btn-default" href="<?php echo site_url('attribute_sets'); ?>"><?php echo lang('attribute_sets_btn_list'); ?></a>
                        <?php echo form_submit(array(
                            'name' => 'submit',
                            'id' => 'submit',
                            'value' => lang('attribute_sets_btn_save'),
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
    setTimeout(function(){$(":input:visible:first","#form-attribute_set").focus();}, 100);
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
    $('#form-attribute_set').validate({
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
                        window.location.href = '<?php echo site_url('attribute_sets'); ?>';
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
