<?php if (!defined('BIZ_PATH')) exit('No direct script access allowed');
require_once (BIZ_PATH.'/libraries/dompdf/autoload.inc.php');
use Dompdf\Dompdf;

class BizPDF {

	protected $_dompdf = null;

	protected $_stream = false;

	protected $_fileName = '';

	protected $_content = '';

	protected $_dir = '';

	protected $_pageSize = 'A4';

	public function __construct()
	{
		$this->_dompdf = new Dompdf(array('enable_remote' => true));
		$this->_dir = DOCUMENT_PATH . 'pdf';

		$this->ci =& get_instance();
		$this->_pageSize = $this->ci->config->item('config_sales_receipt_pdf_size') ? ucfirst($this->ci->config->item('config_sales_receipt_pdf_size')) : 'A4';
	}

	public function setFileName($fileName = '')
	{
		$this->_fileName = $fileName . '.pdf';
		return $this;
	}

	public function setContent($content = '')
	{
		// $this->_content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
		$this->_content = $content;
		return $this;
	}

	public function generate()
	{
		$this->_dompdf->set_paper($this->_pageSize, "portrait");
		$this->_dompdf->load_html($this->_content);
		// $this->_dompdf->load_html($this->_content, 'UTF-8');
		$this->_dompdf->render();
		if($this->_stream)
		{
			$this->_dompdf->stream($this->_fileName);	
		} else {
			file_put_contents($this->_dir . '/' . $this->_fileName, $this->_dompdf->output());
			chmod($this->_dir . '/' . $this->_fileName, 0664);
			return $this->_dir . '/' . $this->_fileName;
		}
	}
}
