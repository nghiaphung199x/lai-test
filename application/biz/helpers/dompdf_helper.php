<?php if (!defined('BIZ_PATH')) exit('No direct script access allowed');
require_once (BIZ_PATH.'/libraries/dompdf/autoload.inc.php');
use Dompdf\Dompdf;

function dompdf_generate($html, $filename, $stream = true) {
	$dompdf = new Dompdf();
	$dompdf->load_html($html);
	$dompdf->render();
	
	if ($stream) {
		$dompdf->stream($filename);
	} else {
		return $dompdf->output();
	}
}
