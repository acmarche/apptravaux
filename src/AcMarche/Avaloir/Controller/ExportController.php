<?php

namespace AcMarche\Avaloir\Controller;

use AcMarche\Avaloir\Entity\Avaloir;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Export controller.
 *
 * @Route("/export")
 * @IsGranted("ROLE_TRAVAUX_AVALOIR")
 */
class ExportController extends AbstractController
{
    /**
     *
     * @Route("/avaloirs", name="export_avaloirs_xls", methods={"GET"})
     *
     */
    public function AvaloirXls()
    {
        $spreadsheet = new Spreadsheet();
        $this->avaloirXSLObject($spreadsheet);

        $writer = new Xlsx($spreadsheet);

        $fileName = 'avaloirs.xls';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);

        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @param Request $request
     * @param Spreadsheet $spreadsheet
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function avaloirXSLObject(Spreadsheet $spreadsheet)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $em = $this->getDoctrine()->getManager();

        $avaloirs = $em->getRepository(Avaloir::class)->findAll();

        $ligne = 1;

        /**
         * titre des colonnes
         */
        $colonnes = array('Id', 'Village', 'Rue', 'latitude', 'longitude', 'Descriptif', 'Dates');

        $lettre = "A";
        foreach ($colonnes as $colonne) {
            //$sheet->getColumnDimension('A')->setWidth(20);
            $sheet->setCellValue($lettre . $ligne, $colonne);
            //    $sheet->getStyle($lettre.$ligne)->applyFromArray($font);
            $lettre++;
        }

        $ligne++;

        foreach ($avaloirs as $avaloir) {
            $dates = $avaloir->getDates();

            $lettre = "A";
            $sheet->setCellValue($lettre++ . $ligne, $avaloir->getId());
            $sheet->setCellValue($lettre++ . $ligne, $avaloir->getLocalite());
            $sheet->setCellValue($lettre++ . $ligne, $avaloir->getLatitude());
            $sheet->setCellValue($lettre++ . $ligne, $avaloir->getLongitude());
            $sheet->setCellValue($lettre++ . $ligne, $avaloir->getRue());
            $sheet->setCellValue($lettre++ . $ligne, $avaloir->getDescriptif());

            foreach ($dates as $date) {
                $jour = $date->getJour();
                $sheet->setCellValue($lettre++ . $ligne, $jour->format('d-m-Y'));
            }

            $ligne++;
        }
    }
}
