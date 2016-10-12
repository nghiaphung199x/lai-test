<?php $this->load->view("partial/header"); ?>
<div class="form">
    <?php echo form_open('attribute_sets/save/' . (!isset($is_clone) ? $entity->id : ''), array('id' => 'form-attribute_set',)); ?>
        <?php form_hidden('redirect_code', 2); ?>
        <div class="row">
            <div class="col-md-8">
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
                            <?php echo form_label(lang('attribute_sets_field_code'), 'attribute_set_code', array('class' => '')); ?>
                            <div class="cl">
                                <?php echo form_input(array(
                                'name' => 'attribute_set[code]',
                                'id' => 'attribute_set_code',
                                'title' => lang('common_fields_required_alert'),
                                'class' => 'form-control form-inps',
                                'value' => $entity->code
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
            </div>
            <div class="col-md-4">
                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <?php echo lang('attribute_sets_related_objects'); ?>
                        </h3>
                    </div>
                    <div class="panel-body bootstrap">
                        <table class="table">
                            <tbody>
                            <?php foreach ($related_objects as $value => $label) :?>
                            <tr>
                                <td class="no-border">
                                    &nbsp;
                                    <input <?php if (!empty($entity->related_objects) && in_array($value, $entity->related_objects)) :?>checked="checked"<?php endif; ?> type="checkbox" name="attribute_set[related_objects][]" value="<?php echo $value; ?>" id="ro-<?php echo $value; ?>" class="module_checkboxes" />
                                    <label for="ro-<?php echo $value; ?>"><span></span></label><span class="text-info"><?php echo lang($label); ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?php echo lang('attribute_sets_attribute_information'); ?>
                </h3>
            </div>
            <div class="panel-body bootstrap">
                <p><i><?php echo lang('attribute_sets_hint'); ?></i></p>
                <div class="row">
                    <div class="col-md-6">
                        <?php if (!empty($attribute_groups)) :?>
                            <?php foreach ($attribute_groups as $attribute_group) :?>
                                <div class="form-group">
                                    <p><i class="glyphicon glyphicon-list"></i> <strong><?php echo $attribute_group->name; ?></strong></p>
                                    <ul class="sortable attribute-group" id="attribute_group_<?php echo $attribute_group->id; ?>">
                                        <?php if (!empty($attributes_combined)) :?>
                                        <?php foreach ($attributes_combined as $attribute_combined) :?>
                                            <?php if ($attribute_combined->attribute_group_id == $attribute_group->id) :?>
                                                <li>
                                                    <input type="hidden" name="attribute[<?php echo $attribute_group->id; ?>][]" value="<?php echo $attribute_combined->id; ?>" />
                                                    <?php echo $attribute_combined->name; ?>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php if (!empty($attributes)) :?>
                            <p><i class="glyphicon glyphicon-list"></i> <strong><?php echo lang('attribute_sets_all_attributes'); ?></strong></p>
                            <ul id="attribute_group_0" class="sortable">
                                <?php $is_exists = false; foreach ($attributes as $attribute) :?>
                                <?php if (!isset($has_attributes[$attribute->id])) :?>
                                <li>
                                    <input type="hidden" name="attribute[0][]" value="<?php echo $attribute->id; ?>" />
                                    <?php echo $attribute->name; ?>
                                </li>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript">
    $(".sortable").sortable({
        connectWith: ".sortable",
        receive: function( event, ui ) {
            $(".sortable").each(function() {
                var id = $(this).attr("id").toString().replace("attribute_group_", "");
                var $attributes = $(this).find("li");
                $attributes.each(function() {
                    var name = "attribute["+id+"][]";
                    $(this).find("input[type=hidden]").attr("name", name);
                });
            });
        }
    });
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
