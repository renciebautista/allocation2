<?php

class ActivityPDF extends \TCPDF {

	//Page header
	public function Header() {

		// $image_file = $_SERVER["DOCUMENT_ROOT"].'assets/images/logo.png';
		$image_file = storage_path().'/uploads/tempfiles/logo.png';
	    $this->Image($image_file, 0, 5, 30, '', 'PNG', '', 'T', false, 300, 'R', false, false, 0, false, false, false);

		$this->SetFont('helvetica', 'B', 13);
		$this->MultiCell(0, 14,'', 0, 'L', 0, 1, '', '', true);
		$this->MultiCell(0, 0,'Unilever Philippines, Inc.', 0, 'L', 0, 1, '', '', true);
		$this->SetFont('helvetica', '', 10);
		$this->MultiCell(0, 0,'Customer Marketing Department', 0, 'L', 0, 1, '', '', true);
		
	}

	// Page footer
	public function Footer() {
		// Position at 15 mm from bottom
		$this->SetY(-15);
		// Set font
		$this->SetFont('helvetica', 'I', 8);
		// Page number
		$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
	}
}