<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Export extends CI_Controller {
    public function __construct() {
		parent::__construct();
        $this->load->model('MidtransModel', 'midtrans');
        $this->load->model('GalleryModel', 'gallery');
        $this->load->model('DonationModel', 'donation');
	}

    public function downloadReport() {
        $spreadsheet = new Spreadsheet();

        $style = array(
            'font' => array(
                'size' => 12,
                'name' => 'Arial'
            )
        );

        $spreadsheet->getDefaultStyle()->applyFromArray($style);

        $sheet = $spreadsheet->getActiveSheet();

        // set header
        $sheet->getStyle("A1:E1")->getFont()->setBold(true);
        $sheet->getColumnDimension("B")->setAutoSize(true);
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal');
        $sheet->setCellValue('C1', 'Total Donasi');

        $pos    = 2;
        $no     = 1;
        $total  = 0;
		$list	= $this->donation->getDonationByDate();
        
        foreach ($list as $row) {
            $sheet->setCellValueByColumnAndRow(1, $pos, $no);
            $sheet->setCellValueByColumnAndRow(2, $pos, date('d F Y', strtotime($row['transaction_time'])));
            $sheet->setCellValueByColumnAndRow(3, $pos, "Rp. " . number_format($row['amount'], 0, ',', '.'));
            $no++;
            $pos++;
            $total += $row['amount'];
        }

        $sheet->setCellValueByColumnAndRow(2, $pos, 'Total Semua Donasi');
        $sheet->setCellValueByColumnAndRow(3, $pos, 'Rp. ' . number_format($total, 0, ',', '.'));
        $writer = new Xlsx($spreadsheet);

        // force download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="report.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }
}
