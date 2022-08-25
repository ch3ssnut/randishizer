<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Form\GenerateExcelType;
use App\Service\MealRandomizer;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class SpreadsheetController extends AbstractController
{

    private $mealRandomizer;
    private $entityManager;

    public function __construct(MealRandomizer $mealRandomizer, EntityManagerInterface $entityManager) 
    {
        $this->mealRandomizer = $mealRandomizer;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/generate", name="generate_spreadsheet")
     */
    public function spreadsheet(Request $request): Response
    {
        // This method is used to render /generate page

        $generateForm = $this->createForm(GenerateExcelType::class);
        $generateForm->handleRequest($request);
        if ($generateForm->isSubmitted() && $generateForm->isValid()) {
            $days_number = $generateForm->get('Number_of_days')->getData();
            return $this->generate_spreadsheet($days_number);
        }


        return $this->render('spreadsheet/generate.html.twig', [
            'form' => $generateForm->createView(),
        ]);
        

    }

    private function generate_spreadsheet(int $number_of_days): Response
    {
        // This method generates spreadsheet with menu 
        // TODO: generate also shopping list
        $spreadsheet = new Spreadsheet();

        // Setting types of meals
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A2', 'Breakfast')
            ->setCellValue('A3', 'Dinner')
            ->setCellValue('A4', 'Dessert')
            ->setCellValue('A5', 'Supper')
            ->setTitle('Menu');

        // Fetching shuffled meals array from MealRandomizer service
        $mealsArr = ['Breakfast', 'Dinner', 'Dessert', 'Supper'];

        $mealIndex = 0;
        // foreach ($mealsArr as $m) {
        //     ${'mealArr' . $mealIndex} = $this->mealRandomizer->MealRandomizer($m);
        //     $mealIndex++;
        // }
        $mealsRow = 2;
        foreach ($mealsArr as $meal) {
            $query = $this->entityManager->getRepository(Dish::class)->findMealsByDish($meal);
            // TODO: shuffle after debugging
            // shuffle($query);
            $mealsColumn = 'B';

            foreach ($query as $q) {
                $sheet->setCellValue($mealsColumn . $mealsRow, $q->getName());
                $ing = $q->getIngredients();
                $ingRow = count($mealsArr) + 2;
                $ingColumn = $mealsColumn;
                foreach ($ing as $i) {
                    while (true) {
                        $currentCell = $sheet->getCell($ingColumn . $ingRow)->getValue();
                        if ($currentCell === null) {
                            $sheet->setCellValue($ingColumn++ . $ingRow, $i->getName());
                            $sheet->setCellValue($ingColumn++ . $ingRow, $i->getAmmount());                   
                            $sheet->setCellValue($ingColumn . $ingRow, $i->getUnit());
                            break;
                        } elseif ($currentCell === $i->getName()) {
                            $sheet->setCellValue($ingColumn++ . $ingRow, $i->getName());
                            $sheet->setCellValue($ingColumn . $ingRow, $sheet->getCell($ingColumn . $ingRow)->getValue() + $i->getAmmount());
                            $ingColumn++;                   
                            $sheet->setCellValue($ingColumn . $ingRow, $i->getUnit());
                            break;
                        } else {
                            $ingRow++;
                            continue;
                        }
                    }
                    $ingColumn = $mealsColumn;
                    $ingRow = count($mealsArr) + 2;
                }
                for ($i = 1; $i <= 3; $i++) {
                    $mealsColumn ++;
                }
            }  
            $mealsRow++;
        }
    

        // Printing all days submitted by user into spreadsheet 
        $column = 'A';

        for ($i = 1; $i <= $number_of_days ; $i++ ) {
            $column++;
            $cell = $column . '1';
            $sheet->setCellValue($cell, 'Day number '.$i);
        }

        
        // Printing all meals into spreadsheet
        // If number of days > number of meals -> method is making new array again and shuffling

        // for ($i = 1; $i <= count($mealsArr); $i++) {
        //     $mealsColumn = 'A';
        //     $arrNo = $i - 1;
        //     $modMealArr = ${'mealArr' . $arrNo};
        //     for ($n = 1; $n <= $number_of_days; $n++) {
        //         $mealsColumn++;
        //         $mealCell = $mealsColumn . $i + 1;
        //         $sheet->setCellValue($mealCell, $modMealArr[0]);
        //         array_shift($modMealArr);
        //         if (!$modMealArr) {
        //             $modMealArr = ${'mealArr' . $arrNo};
        //             shuffle($modMealArr);
        //         }
                
        //     }
        // }

        $writer = new Xlsx($spreadsheet);
        
        $fileName = 'spreadsheet.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);
        

        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

}
