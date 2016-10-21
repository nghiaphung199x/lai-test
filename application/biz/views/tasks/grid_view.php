<?php $this->load->view("partial/header"); ?>
<link href="https://hstatic.net/0/0/global/design/plugins/font-awesome/4.5.0/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/style.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/responsive.css" type="text/css" media="screen" />

<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/task-core.js" ></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/script.js" ></script>

<div class="manage_buttons">
<div class="manage-row-options">
	<div class="email_buttons text-center">		
		<a href="javascript:;" class="btn btn-red btn-lg" title="Xóa" onclick="delete_template();"><span class="">Xóa lựa chọn</span></a>		
	</div>
</div>
<div class="cl">
	<div class="pull-left">
		<form action="" id="search_form" autocomplete="off" class="form-inline" method="post" accept-charset="utf-8">
			<div class="search no-left-border">
				<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span><input type="text" class="form-control ui-autocomplete-input" id="s_keywords" value="" placeholder="Tìm kiếm đự án" autocomplete="off">
			</div>
			<div class="clear-block hidden">
			    <i class="ion ion-close-circled"></i>
			</div>
		</form>
	</div>
	<div class="pull-right">
		<div class="buttons-list" style="padding-top: 0;">
			<div class="pull-right-btn">
				<a href="<?php echo base_url() . 'tasks/templateAdd' ?>" id="new-person-btn" class="btn btn-primary btn-lg" title="Thêm mới Template" style="margin-top: 16px;"><span class="">Thêm mới Template</span></a>					
			</div>
		</div>				
	</div>
    <div class="cl"></div>
</div>
</div>
<div class="container-fluid" id="project_grid_list">
	<div class="row manage-table">
		<div class="panel panel-piluku">
			<div class="panel-heading" style="border: 0; padding-left: 0;">
				<div class="gantt_title">
					<h3 class="panel-title">
						<span class="tieude"><a href="<?php echo base_url() . 'tasks'; ?>">Lược đồ</a></span>
						<span class="tieude active">Danh sách</span>
						<i class="fa fa-spinner fa-spin" id="loading_1"></i>	
					</h3>
				</div>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table tablesorter table-reports table-bordered table-tree" id="project_grid_table">
						<thead>
							<tr align="center" style="font-weight:bold">
								<td class="hidden-print" style="width: 50px;" colspan="2"><a href="#" class="expand_all">&nbsp</a></td>
								<td align="center" data-field="name">Tên Dự án</td>
								<td align="center" style="width: 8%;" data-field="prioty">Ưu tiên</td>
								<td align="center" style="width: 100px;" data-field="date_start">Bắt đầu</td>
								<td align="center" style="width: 100px;" data-field="date_end">Kết thúc</td>
								<td align="center" style="width: 256px;" data-field="progress">Tiến độ</td>
								<td align="center" style="width: 10%;" data-field="trangthai">Tình trạng</td>
								<td align="center" style="width: 20%;">Phụ trách</td>			
							</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
				</div>
			</div>	
		</div>	
	</div>
</div>
<style>
.detailed-reports i.fa-search {
	font-size: 16px;
    margin-right: 0;
}

#project_grid_table td {
    padding: 4px;
}

#project_grid_table td[data-field] {
    cursor: pointer;
}
</style>
<script type="text/javascript">
$( document ).ready(function() {
	load_list('project-grid', 1);
	$('body').on('click','.table-tree .expand_all',function(){
        var symbol = $(this).text();
    	var tr_element = $(this).closest('tr');
    	var table_element = $(this).closest('table');
    	var id = tr_element.attr('data-tree');

    	var tr_child = table_element.find('tr[data-parent="'+id+'"]');
  		if(symbol == '+'){
  			tr_child.hide();
  			$(this).text('-');

  		}else{
            var table_child     = $('#task_childs_'+id);
            var data_content    = table_child.attr('data-content');
            if(data_content == 0) {
                load_task_childs(id, 1);
            }

  			tr_child.show();
  			$(this).text('+');
  		}
	});

    //sort
    $('body').on('click','table [data-field]',function(){
        var attr     = $(this).attr('data-field');
        var table    = $(this).closest('table');
        var table_id = table.attr('id');
        if($(this).hasClass('header')) {
            if($(this).hasClass('headerSortUp')){
                $(this).removeClass('headerSortUp');
                $(this).addClass('headerSortDown');
            }else {
                $(this).removeClass('headerSortDown');
                $(this).addClass('headerSortUp');
            }
        }else {
            table.find('td').removeClass('header');
            table.find('td').removeClass('headerSortUp');
            table.find('td').removeClass('headerSortDown');
            $(this).addClass('header headerSortUp');
        }

        if(table_id == 'project_grid_table') {
            load_list('project-grid', 1);
        }else {

        }
    });
});
</script>

<?php $this->load->view("partial/footer"); ?>