<?php $this->load->view("partial/header"); ?>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-6">	
			<div class="buttons-list">
				<div class="pull-right-btn">
					<ul class="list-inline print-buttons">
						<li>
							<button class="btn btn-primary btn-lg hidden-print" id="print_button" onclick="print_receipt()" > <?php echo lang('common_print'); ?> </button>		
						</li>
						<li>
							<a href="<?php echo $next_url; ?>" class="btn btn-primary btn-lg hidden-print"> <?php echo lang('common_continue'); ?> </a>		
						</li>
					</ul>
				</div>
			</div>				
		</div>
	</div>
	<div class="row">
		<div class="row" id="cash_drawer_wrapper">
			<div class="col-md-12" id="cash_drawer_wrapper_inner">
				<div class="panel panel-piluku">
					<?php echo $print_block_html; ?>
				</div>
				<!--container-->
			</div>		
		</div>
	
		
	</div>
</div>
<script type="text/javascript">
function print_receipt()
{
	var conten_pdf = document.getElementById('pdf_content').outerHTML;
	var css = $('#cash_drawer_wrapper style[type="text/css"]').html();
	var html ='<style type="text/css">';
	html += css ;
	html +='</style>';
	html+=conten_pdf;
	newWin = window.open("");
	newWin.document.write(html);
	newWin.print();
 }
</script>
<?php $this->load->view("partial/footer"); ?>
