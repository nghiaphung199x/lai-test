<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">Phê duyệt Công việc</h4>
        </div>
        <div class="modal-body">
            <form method="POST" name="task_pheduyet_form" id="task_pheduyet_form" class="form-horizontal" enctype="multipart/form-data">
                <input type="hidden" name="task_id" value="<?php echo $arrParam['id']; ?>" />
                <div class="clearfix hang">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group" style="margin-bottom: 0">
                                <label class="col-md-3 col-lg-2 control-label">Phê duyệt</label>
                                <div class="col-md-9 col-lg-10">
                                    <select name="pheduyet_select" id="pheduyet_select" class="form-control">
                                        <option value="1">Có</option>
                                        <option value="0">Không</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 0; margin-top: 20px; display: none;" id="reson_section">
                                <label class="col-md-3 col-lg-2 control-label">Lý do</label>
                                <div class="col-md-9 col-lg-10">
                                    <textarea name="pheduyet_note" id="pheduyet_note" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>

        </div>
        <div class="modal-footer">
            <a href="javascript:;" onclick="pheduyet_confirm();" class="btn btn-primary">Lưu</a>
        </div>
    </div>
</div>
<script type="text/javascript">
    $( document ).ready(function() {
        $('body').on('change','#pheduyet_select',function(){
            var pheduyet_select = $(this).val();
            if(pheduyet_select == 1)
                $('#reson_section').hide();
            else
                $('#reson_section').show();
        });
    });
</script>