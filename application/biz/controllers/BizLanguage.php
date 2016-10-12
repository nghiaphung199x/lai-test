<?php
require_once (APPPATH . "core/AuthController.php");

class BizLanguage extends AuthController 
{
	// TODO
	public function index(){
		$pdfContent = file_get_contents(VIEWPATH . 'sales/receipt_pdf.php');

		$this->load->library('BizPDF');
		$file = $this->bizpdf
					->setFileName('REC_001')
					->setContent($pdfContent)
					->generate();

		// header('Content-type: text/html; charset=UTF-8');
		header('Content-type: application/pdf');
		header('Content-Disposition: inline; filename="REC_POS_tmp.pdf"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize($file));
		header('Accept-Ranges: bytes');
		@readfile($file);
	}
}
?>