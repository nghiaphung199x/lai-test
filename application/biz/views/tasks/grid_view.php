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
<div class="container-fluid">
	<div class="row manage-table" id="template_list">
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<h3 class="panel-title">
					<span class="tieude"><a href="<?php echo base_url() . 'tasks' ?>">Lược đồ</a></span>
					<span class="tieude active">Danh sách</span>
						
					<span class="panel-options custom">
					</span>
				</h3>
				<i class="fa fa-spinner fa-spin" id="loading_1"></i>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table detailed-reports table-reports table-bordered table-tree">
						<thead>
							<tr align="center" style="font-weight:bold">
								<td class="hidden-print" style="width: 25px;"><a href="#" class="expand_all">&nbsp</a></td>
								<td align="center">Tên Dự án/ Công việc</td>
								<td align="center" style="width: 8%;">Ưu tiên</td>
								<td align="center" style="width: 100px;">Bắt đầu</td>
								<td align="center" style="width: 100px;">Kết thúc</td>
								<td align="center" style="width: 256px;">Tiến độ</td>
								<td align="center" style="width: 10%;">Tình trạng</td>
								<td align="center" style="width: 20%;">Phụ trách</td>			
							</tr>
						</thead>
						<tbody>
							<tr data-tree="1">
								<td class="hidden-print" style="width: 25px;"><a href="javascript:;" class="expand_all">+</a></td>
								<td>Dự án Xây dựng Cầu đường Long Giang</td>
								<td align="center">Trung bình</td>
								<td align="center">01-09-2016 00:00:00</td>
								<td align="center">10-09-2016 00:00:00</td>
								<td align="center">
									<div class="clearfix">
										<div class="progress-bar" style="float: left;">
										  <div class="bar positive">
										    <span>80%</span>
										  </div>
										  <div class="bar negative">
										    <span></span>
										  </div>
										</div>	
										<div class="progress-text">Còn 2 ngày</div>
									</div>

									
								</td>
								<td align="center">Đang thực hiện</td>
								<td align="center"><strong>nghiaphung</strong></td>
							</tr>
							<tr data-parent="1">
								<td class="hidden-print" style="width: 25px;"><a href="javascript:;" class="expand_all">&nbsp</a></td>
								<td>Phân tích thiết kế hệ thống</td>
								<td align="center">Trung bình</td>
								<td align="center">01-09-2016 00:00:00</td>
								<td align="center">10-09-2016 00:00:00</td>
								<td align="center"></td>
								<td align="center">Đang thực hiện</td>
								<td align="center"><strong>tamdao</strong>, nghiaphung</td>
							</tr>
							<tr data-parent="1">
								<td class="hidden-print" style="width: 25px;"><a href="javascript:;" class="expand_all">&nbsp</a></td>
								<td>Xây dựng database</td>
								<td align="center">Trung bình</td>
								<td align="center">01-09-2016 00:00:00</td>
								<td align="center">10-09-2016 00:00:00</td>
								<td align="center"></td>
								<td align="center">Đang thực hiện</td>
								<td align="center">nghiaphung, tamdao</td>
							</tr>
						</tbody>
					</table>
				</div>
		
			</div>	
		</div>	
	</div>
	<div class="pagination hidden-print alternate text-center">
		<strong>1</strong>
		<a href="javascript:;" data-page="2">2</a>
		<a href="javascript:;" data-page="2" rel="next">&gt;</a>	
	</div>
</div>
<script type="text/javascript">
$( document ).ready(function() {
    $('.table-tree .expand_all').click(function() {
        var symbol = $(this).text();
    	var tr_element = $(this).closest('tr');
    	var table_element = $(this).closest('table');
    	var id = tr_element.attr('data-tree');

  		if(symbol == '+'){
  			table_element.find('tr[data-parent="'+id+'"]').hide();
  			$(this).text('-');
  		}else{
  			table_element.find('tr[data-parent="'+id+'"]').show();
  			$(this).text('+');
  		}
    });
});
</script>
<style>
.manage-table .panel-body {
	padding: 15px;
	
}

.manage-table tr td:first-child, .manage-table tr th:first-child {
	padding-left: 4px;
}

.progress-bar {
    position: relative;
    width: 150px;
    height: 20px;
    line-height: 20px;
    overflow: hidden;
    font-weight: bold;
    font-size: 12px;
    box-shadow: 0 4px 10px -5px rgba(0, 0, 0, 0.25);
    float: left;
}

.progress-text {
	float: left; height: 18px; line-height: 18px; margin-left: 10px;
}

.progress-bar .bar.positive {
    
    background: #4388c2;
    left: 0;
    width: 80%;
    -webkit-animation: animate-positive 1s;
    animation: animate-positive 1s;
}

.progress-bar .bar.negative {
    background: #489ee7;
    right: 0;
    width: 20%;
    -webkit-animation: animate-negative 1s;
    animation: animate-negative 1s;
}

.progress-bar .bar {
    position: absolute;
    top: 0;
    height: 100%;
    overflow: hidden;
}

.progress-bar .bar.positive span {
    left: 0;
    color: white;
}
.progress-bar .bar span {
    position: absolute;
    display: block;
    width: 150px;
    height: 100%;
    text-align: center;
}

.progress-bar .bar.negative span {
    right: 0;
    color: #fff;
}

@-webkit-keyframes animate-positive {
  0% { width:0%; }
}
@keyframes animate-positive {
  0% { width:0%; }
}

@-webkit-keyframes animate-negative {
  0% { width:100%; }  
}
@keyframes animate-negative {
  0% { width:100%; }  
}

</style>

<?php $this->load->view("partial/footer"); ?>