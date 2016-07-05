<?php $this->load->view("partial/header"); ?>

<script type="text/javascript">
    $(document).ready(function () {

        var table_columns = ['', 'id', 'name', 'description', ''];
        enable_sorting("<?php echo site_url("$controller_name/sorting"); ?>", table_columns, <?php echo $per_page; ?>, <?php echo json_encode($order_col);?>, <?php echo json_encode($order_dir);?>);
        enable_select_all();
        enable_checkboxes();
        enable_row_selection();
        enable_search('<?php echo site_url("$controller_name");?>',<?php echo json_encode(lang("common_confirm_search"));?>);
        enable_delete(<?php echo json_encode(lang($controller_name . "_confirm_delete"));?>,<?php echo json_encode(lang($controller_name . "_none_selected"));?>);
        enable_cleanup(<?php echo json_encode(lang("attribute_sets_confirm_cleanup"));?>);

    <?php if ($this->session->flashdata('manage_success_message')) :?>
        show_feedback('success', <?php echo json_encode($this->session->flashdata('manage_success_message')); ?>, <?php echo json_encode(lang('common_success')); ?>);
    <?php endif; ?>

    });

    function post_bulk_form_submit(response) {
        window.location.reload();
    }

</script>
<div class="manage_buttons">
    <div class="manage-row-options hidden">
        <div class="email_buttons attribute_sets">
            <?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) :?>
            <?php echo anchor("$controller_name/delete",
                              '<span class="">' . lang("common_delete") . '</span>',
                              array('id' => 'delete', 'class' => 'btn btn-red btn-lg disabled', 'title' => lang("common_delete")));
            ?>
            <?php endif; ?>
            <a href="#" class="btn btn-lg btn-clear-selection btn-warning"><?php echo lang('common_clear_selection'); ?></a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?php echo form_open("$controller_name/search",array('id'=>'search_form', 'autocomplete'=> 'off')); ?>
            <div class="search no-left-border">
                <input type="text" class="form-control" name='search' id='search' value="<?php echo H($search); ?>" placeholder="<?php echo lang('common_search'); ?> <?php echo lang('module_'.$controller_name); ?>"/>
            </div>
            <div class="clear-block <?php echo ($search == '') ? 'hidden' : '' ?>">
                <a class="clear" href="<?php echo site_url($controller_name.'/clear_state'); ?>">
                    <i class="ion ion-close-circled"></i>
                </a>
            </div>
            <?php echo form_close() ?>
        </div>
        <div class="col-md-8">
            <div class="buttons-list attribute_sets-buttons">
                <div class="pull-right-btn">

                    <?php if ($this->Employee->has_module_action_permission('attributes', 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) :?>
                        <?php echo anchor("attributes", '<span class="">' . lang('attributes_manage') . '</span>', array('class' => 'btn btn-primary btn-lg', 'title' => lang($controller_name . '_new'))); ?>
                    <?php endif ?>

                    <?php if ($this->Employee->has_module_action_permission('attribute_groups', 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) :?>
                        <?php echo anchor("attribute_groups", '<span class="">' . lang('attribute_groups_manage') . '</span>', array('class' => 'btn btn-primary btn-lg', 'title' => lang($controller_name . '_new'))); ?>
                    <?php endif ?>

                    <?php if ($this->Employee->has_module_action_permission($controller_name, 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) :?>
                    <?php echo anchor("$controller_name/view/-1/",
                                      '<span class="">' . lang($controller_name . '_new') . '</span>',
                                      array('class' => 'btn btn-primary btn-lg', 'title' => lang($controller_name . '_new')));
                    ?>
                    <?php endif ?>
                    <div class="piluku-dropdown">
                        <button type="button" class="btn btn-more dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <i class="ion-android-more-horizontal"></i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <?php if ($this->Employee->has_module_action_permission($controller_name, 'manage_categories', $this->Employee->get_logged_in_employee_info()->person_id)) :?>
                            <li>
                                <?php echo anchor("$controller_name/categories",
                                                  '<span class="">' . lang("attribute_sets_manage_categories") . '</span>',
                                                  array('class' => '', 'title' => lang('attribute_sets_manage_categories')));
                                ?>
                            </li>
                            <?php endif; ?>
                            <?php if ($this->Employee->has_module_action_permission($controller_name, 'manage_tags', $this->Employee->get_logged_in_employee_info()->person_id)) { ?>
                            <li>
                                <?php echo anchor("$controller_name/manage_tags",
                                                  '<span class="">' . lang("attribute_sets_manage_tags") . '</span>',
                                                  array('class' => '', 'title' => lang('attribute_sets_manage_tags')));
                                ?>
                            </li>
                            <?php } ?>
                            <?php if ($this->Employee->has_module_action_permission($controller_name, 'count_inventory', $this->Employee->get_logged_in_employee_info()->person_id)) :?>
                            <li>
                                <?php echo anchor("$controller_name/count",
                                '<span class="">' . lang("attribute_sets_count_inventory") . '</span>',
                                array('class' => '',
                                    'title' => lang('attribute_sets_count_inventory')));
                                ?>
                            </li>
                            <?php endif ?>

                            <?php if ($this->Employee->has_module_action_permission($controller_name, 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) :?>
                            <li>
                                <?php echo anchor("$controller_name/excel_import/",
                                                  '<span class="">' . lang("common_excel_import") . '</span>',
                                                  array('class' => '', 'title' => lang('common_excel_import')));
                                ?>
                            </li>
                            <li>
                                <?php echo anchor("$controller_name/excel_export/",
                                                  '<span class="">' . lang("common_excel_export") . '</span>',
                                                  array('class' => '', 'title' => lang('common_excel_export')));
                                ?>
                            </li>
                            <?php endif; ?>
                            <?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) :?>
                            <li>
                                <?php echo anchor("$controller_name/cleanup",
                                                  '<span class="">' . lang("attribute_sets_cleanup_old_attribute_sets") . '</span>',
                                                  array('id' => 'cleanup', 'class' => '', 'title' => lang("attribute_sets_cleanup_old_attribute_sets")));
                                ?>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row manage-table">
        <div class="panel panel-piluku">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?php echo lang('common_list_of') . ' ' . lang('module_' . $controller_name); ?>
                    <span title="<?php echo $total_rows; ?> total <?php echo $controller_name ?>" class="badge bg-primary tip-left"><?php echo $total_rows; ?></span>
                    <div class="panel-options custom">
                        <?php if ($pagination) :?>
                        <div class="pagination pagination-top hidden-print  text-center" id="pagination_top">
                            <?php echo $pagination;?>
                        </div>
                        <?php endif  ?>
                    </div>
                </h3>
            </div>
            <div class="panel-body nopadding table_holder table-responsive">
                <?php echo $manage_table; ?>
            </div>
        </div>
    </div>
</div>

<?php if ($pagination) :?>
<div class="text-center">
    <div class="row pagination hidden-print alternate text-center" id="pagination_bottom">
        <?php echo $pagination;?>
    </div>
</div>
<?php endif; ?>
</div>

<?php $this->load->view("partial/footer"); ?>