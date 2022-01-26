<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Entity\Ingredient;
use App\Form\DishType;
use App\Form\IngredientType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DishesController extends AbstractController
{
    /**
     * @Route("/dishes", name="dishes")
     */
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $dishes = $this->getUser()->getDishes();

        //Creating add dish form
        $dish = new Dish();
        $dish->setOwner($this->getUser());


        $form = $this->createForm(DishType::class, $dish);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($form->getData());
            $entityManager->flush();

            return $this->redirectToRoute('dishes');
        }


        return $this->render('dishes/dishes.html.twig', [
            'dishes' => $dishes,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/dishes/{id}", name="edit_dishes")
     */
    public function edit(int $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        $dish = $entityManager->getRepository(Dish::class)
            ->findOneBy([
                'owner' => $this->getUser(),
                'id' => $id
            ]);
        
        $ingredients = $dish->getIngredients();

        $ingredient = new Ingredient();
        $ingredient->setDish($dish);

        $dishForm = $this->createForm(IngredientType::class, $ingredient);
        $dishForm->handleRequest($request);
        if ($dishForm->isSubmitted() && $dishForm->isValid()) {
            $entityManager->persist($dishForm->getData());
            $entityManager->flush();

            return $this->redirectToRoute('edit_dishes', ['id' => $id]);
        }

        return $this->render('dishes/edit.html.twig', [
            'dish' => $dish,
            'ingredients' => $ingredients,
            'form' => $dishForm->createView(),
        ]);
    }
}
