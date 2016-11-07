<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">Danh sách công việc</h4>
        </div>
        <div class="modal-body">
            <div class="panel-body nopadding table_holder table-responsive table_list">
                <table class="tablesorter table table-hover sortable_table" id="template_task_list">
                    <tbody>
                    <?php
                    if(!empty($items)) {
                        foreach($items as $val) {
                            $id   = $val['id'];
                            $name = str_repeat("&nbsp&nbsp&nbsp",$val['level'] - 1) . '<span data-editable>'.$val['name'].'</span>';
                            if(isset($orderings['t_'.$val['id']])) {
                                $child_ids = $orderings[$val['id']];
                                $child_ids = implode(',', $child_ids);
                            }

                            ?>
                            <tr>
                                <td><?php echo $name; ?></td>
                                <td class="right"><a href="javascript:;" data-id="<?php echo $id; ?>" data-child="<?php echo $child_ids; ?>" onclick="del_template_task(this);">Xóa</a></td>
                            </tr>
                        <?php
                        }
                    }else {
                        ?>
                        <tr><td colspan="2"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
                    <?php
                    }
                    ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>