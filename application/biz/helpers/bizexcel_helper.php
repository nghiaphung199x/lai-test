<?php
require_once (APPPATH.'libraries/PHPExcel/PHPExcel.php');
require_once(APPPATH.'libraries/PHPExcel/PHPExcel/IOFactory.php');

class BizExcel {
	
	protected $oPHPExcel;
	
	protected $oWriter;
	
	protected $formattedFile;
	
	protected $newFileName;
	
	protected $excelPath;
	
	protected $numberRowStartBody;
	
	protected $dataExcel = array();
	
	protected $dataExtra = [];
	
	protected $headerOfBody = [];
	
	public function __construct($formattedFile = '') {
		$this->excelPath = DOCUMENT_PATH . 'excel/';
		$this->formattedFile = $this->excelPath . $formattedFile;
		if (is_file($this->formattedFile)) {
			$this->oPHPExcel = PHPExcel_IOFactory::createReader('Excel2007');
			$this->oPHPExcel = $this->oPHPExcel->load($this->formattedFile);
		}
	}
	
	public function setNewFileName($newFileName = '') {
		$this->newFileName = $newFileName;
		return $this;
	}
	
	public function setDataExcel($dataExcel = array()) {
		$this->dataExcel = $dataExcel;
		return $this;
	}
	
	public function generateFile($saveToLocal = true, $newFileName = '', $final = true) {
		if (!empty($newFileName)) {
			$this->newFileName = $newFileName;
		}
		
		if (!empty($this->headerOfBody)) {
			$this->buildHeaderOfTable();
		}
		
		if (!empty($this->dataExcel)) {
			$this->buildBobyOfTable();
		}
		
		if (!empty($this->dataExtra)) {
			$this->buildExtraData();
		}
		
		
		if ($final) {
			$objWriter = PHPExcel_IOFactory::createWriter($this->oPHPExcel, 'Excel2007');
			if ($saveToLocal) {
				$objWriter->save($this->excelPath . $this->newFileName);
				return null;
			} else {
				ob_start();
				$objWriter->save('php://output');
				$excelOutput = ob_get_clean();
				return $excelOutput;
			}
		}
	}
	
	public function setActiveSheet($index = 0, $sheetName = '') {
		if ($index) {
			$objWorkSheet = $this->oPHPExcel->createSheet($index);
			$this->oPHPExcel->setActiveSheetIndex($index);
		}
		
		if ($sheetName) {
			!empty($objWorkSheet) ? $objWorkSheet->setTitle($sheetName) : $this->oPHPExcel->getActiveSheet()->setTitle($sheetName);
		}
		return $this;
	}
	
	
	protected function buildBobyOfTable() {
		foreach ($this->dataExcel as $index => $row) {
			foreach ($this->headerOfBody as $cell) {
				if($cell['value_field'] == '__AUTO__') {
					$this->oPHPExcel->getActiveSheet()->setCellValue($cell['col'] . ($this->numberRowStartBody + $index + 1), $index + 1);
				} else {
					$this->oPHPExcel->getActiveSheet()->setCellValue($cell['col'] . ($this->numberRowStartBody + $index + 1), isset($row[$cell['value_field']]) ? $row[$cell['value_field']] : '');
				}
			}
		}
	}
	
	public function setNumberRowStartBody($numberRow = 1) {
		$this->numberRowStartBody = $numberRow;
		return $this;
	}
	
	public function setExtraData($extraData = []) {
		$this->dataExtra = $extraData;
		return $this;
	}
	
	protected function buildExtraData() {
		foreach ($this->dataExtra as $cellData) {
			$this->oPHPExcel->getActiveSheet()->setCellValue($cellData['cell'], $cellData['value']);
		}
		return $this;
	}
	
	
	public function setHeaderOfBody($headerOfBody) {
		$this->headerOfBody = $headerOfBody;
		return $this;
	}
	
	public function buildHeaderOfTable() {
		foreach ($this->headerOfBody as $headerCell) {
			$this->oPHPExcel->getActiveSheet()->setCellValue($headerCell['col'] . $this->numberRowStartBody, $headerCell['text']);
			$this->applyCellStyle($headerCell['col'] . $this->numberRowStartBody, $headerCell['styles']);
		}
	}
	
	protected function applyCellStyle($cellName = '', $styles = array()) {
		if ($styles['bold']) {
			$this->oPHPExcel->getActiveSheet()->getStyle($cellName)
												->getFont()
												->setBold(true);
		}
		if ($styles['is_fill']) {
			$this->oPHPExcel->getActiveSheet()->getStyle($cellName)
												->getFill()
												->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			
			$this->oPHPExcel->getActiveSheet()->getStyle($cellName)
												->getFill()
												->getStartColor()
												->setARGB($styles['color']);
		}
	}
}