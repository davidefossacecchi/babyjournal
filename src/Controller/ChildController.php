<?php

namespace App\Controller;

use App\Entity\Child;
use App\Entity\Family;
use App\Form\ChildType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ChildController extends AbstractController
{
    #[Route(path: '/family/{id}/child', name: 'create_child', methods: ['GET', 'POST'])]
    #[IsGranted('view', 'family')]
    public function create(Request $request, Family $family, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ChildType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Child $child */
            $child = $form->getData();
            $child->setFamily($family);
            $em->persist($child);
            $em->flush();
            $this->addFlash('success', $child->getName().' aggiunto/a con successo');
            return $this->redirectToRoute('family_posts', ['id' => $family->getId()]);
        }

        return $this->render(
            'child/form.html.twig', [
                'familyId' => $family->getId(),
                'form' => $form
            ]);
    }

    #[Route(path: '/child/{id}', name: 'edit_child', methods: ['GET', 'POST'])]
    #[IsGranted('edit', 'child')]
    public function edit(Request $request, Child $child, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ChildType::class, $child);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Child $child */
            $child = $form->getData();
            $em->persist($child);
            $em->flush();
            $this->addFlash('success', $child->getName().' modificato/a con successo');
            return $this->redirectToRoute('family_posts', ['id' => $child->getFamily()->getId()]);
        }

        return $this->render('child/form.html.twig', compact('form'));
    }
}
