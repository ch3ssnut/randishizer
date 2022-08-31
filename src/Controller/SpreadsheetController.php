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
            $daysNumber = $generateForm->get('Number_of_days')->getData();
            $daysNumberShopping = $generateForm->get('Number_of_shopping_days')->getData();
            return $this->generate_spreadsheet($daysNumber, $daysNumberShopping);
        }


        return $this->render('spreadsheet/generate.html.twig', [
            'form' => $generateForm->createView(),
        ]);
        

    }

    private function generate_spreadsheet(int $numberOfDays, int $shoppingListDays): Response
    {
        // This method generates spreadsheet with menu 
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
        $mealsRow = 2;
        // Getting dishes for each meal and shuffling meals array, setting starting spreadsheet column as 'B'
        foreach ($mealsArr as $meal) {
            $query = $this->entityManager->getRepository(Dish::class)->findMealsByDish($meal);
            // shuffle($query);
            $mealsColumn = 'B';
            $dayCounter = 1;
            while(true) {
                if ($numberOfDays > count($query)) {
                    $query = array_merge($query, $query);
                } elseif ($numberOfDays < count($query)) {
                    $query = array_splice($query, count($query) - $numberOfDays);
                    break;
                } else {
                    break;
                }
            }
            $shoppingListColumn = $mealsColumn;
            // Looping all dishes to get each ingretien
            foreach ($query as $q) {
                $sheet->setCellValue($mealsColumn . $mealsRow, $q->getName());
                $ing = $q->getIngredients();
                $ingRow = count($mealsArr) + 2;
                $ingColumn = $shoppingListColumn;
                foreach ($ing as $i) {
                    while (true) {
                        $currentCell = $sheet->getCell($ingColumn . $ingRow)->getValue();
                        if ($currentCell === null) {
                            // If cell is empty filling it with ingredient|ammount|unit
                            // if ($i->getName() === '1b') {
                            //     dd($dayCounter);
                            // }
                            $sheet->setCellValue($ingColumn++ . $ingRow, $i->getName());
                            $sheet->setCellValue($ingColumn++ . $ingRow, $i->getAmmount());                   
                            $sheet->setCellValue($ingColumn . $ingRow, $i->getUnit());
                            break;
                        } elseif ($currentCell === $i->getName()) {
                            // If cell isn't empty checking if name filled inside cell is same as name of current ingredint
                            // if name is same then summing ammount of ingredient
                            $sheet->setCellValue($ingColumn++ . $ingRow, $i->getName());
                            $sheet->setCellValue($ingColumn . $ingRow, $sheet->getCell($ingColumn . $ingRow)->getValue() + $i->getAmmount());
                            $ingColumn++;                   
                            $sheet->setCellValue($ingColumn . $ingRow, $i->getUnit());
                            break;
                        } else {
                            // If name of ingredinet isn't same the loop increments row number so it can check next row
                            $ingRow++;
                            continue;
                        }
                    }
                    $ingColumn = $shoppingListColumn;
                    $ingRow = count($mealsArr) + 2;
                }
                /*
                if $shoppingListDays > 1 column for ingredients stays the same, and $dayCounter is incremented
                when $shoppingListDays is same as $dayCounter it increments 3 times $shoppingListDays so ingredients will be same column as meals column
                */
                if ($dayCounter === $shoppingListDays) {
                    for ($i = 1; $i <= 3 * $shoppingListDays; $i++) {
                        $shoppingListColumn ++;
                    }
                    $dayCounter = 1;
                } else {
                    $dayCounter++;
                }
                for ($i = 1; $i <= 3; $i++) {
                    $mealsColumn ++;
                }
            }  
            // Inrementing $mealsRow so it will fill next row with meals
            $mealsRow++;
        }
    

        // Printing all days submitted by user into spreadsheet 
        $column = 'B';

        for ($i = 1; $i <= $numberOfDays ; $i++ ) {
            $cell = $column . '1';
            $sheet->setCellValue($cell, 'Day number '.$i);
            for ($j = 1; $j <= 3; $j++) {
                $column++;
            }
        }


        $writer = new Xlsx($spreadsheet);
        
        $fileName = 'spreadsheet.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);
        

        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

}
