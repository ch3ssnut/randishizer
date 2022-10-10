<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Entity\Ingredient;
use App\Entity\UsersIngredient;
use App\Form\DishType;
use App\Form\IngredientType;
use App\Form\UsersIngredientType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
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

        $usersIngredient = new UsersIngredient();
        $usersIngredient->setOwner($this->getUser());

        // Form to create Ingredients and their specific units for specific user.
        $usersIngredientForm = $this->createForm(UsersIngredientType::class, $usersIngredient);
        $usersIngredientForm->handleRequest($request);
        if ($usersIngredientForm->isSubmitted() && $usersIngredientForm->isValid()) {
            $entityManager->persist($usersIngredientForm->getData());
            $entityManager->flush();

            return $this->redirectToRoute('edit_dishes', ['id' => $id]);
        }

        // Getting 
        // TODO: fix n+1 problem
        $dish = $entityManager->getRepository(Dish::class)
            ->findOneBy([
                'owner' => $this->getUser(),
                'id' => $id
            ]);
        $ingredients = $dish->getIngredients();

        $ingredient = new Ingredient();
        $ingredient->setDish($dish);
        
        // Form to add or remove previously created ingredients to a specific dish.
        $dishForm = $this->createForm(IngredientType::class, $ingredient);
        $dishForm->handleRequest($request);
        if ($dishForm->isSubmitted() && $dishForm->isValid()) {
            if ($dishForm->get('Submit')->isClicked()) {
                $ingredient->setUnit($dishForm->get('name')->getData()->getUnit());
                $entityManager->persist($dishForm->getData());
                $entityManager->flush();
            }
            if ($dishForm->get('Remove')->isClicked()) {
                if ($dishForm->get('name')->getData()->getOwner() === $this->getUser()) {
                    $entityManager->remove($dishForm->get('name')->getData());
                    $entityManager->flush();
                }
            }

            return $this->redirectToRoute('edit_dishes', ['id' => $id]);
        }

        // dd($dishForm->get('name')->getData());

        return $this->render('dishes/edit.html.twig', [
            'dish' => $dish,
            'ingredients' => $ingredients,
            'form' => $dishForm->createView(),
            'users_ingredient_form' => $usersIngredientForm->createView(),
        ]);
    }

    /**
     * @Route("/dishes/remove/{id}", name="remove_dishes")
     */
    public function remove(int $id, EntityManagerInterface $entityManager): Response
    {
        $dish = $entityManager->getRepository(Dish::class)
            ->findOneBy([
                'owner' => $this->getUser(),
                'id' => $id,
            ]);

        $entityManager->remove($dish);
        $entityManager->flush();

        return $this->redirectToRoute('dishes');
    }

    
    /**
     * @Route("dishes/{id}/remove_ingredinet/{ing_id}", name="remove_ingredinet")
     */
    public function remove_ingredinet(int $id, int $ing_id, EntityManagerInterface $entityManager): Response
    {

        $ingredient = $entityManager->getRepository(Ingredient::class)->findIngredientsByDish($id, $ing_id);
        $entityManager->remove($ingredient[0]);
        $entityManager->flush();

        return $this->redirectToRoute('edit_dishes', ['id' => $id]);
    }

    // public function remove_users_ingredient(): Response
    // {
        
    //     return self;
    // }
}
