<?php

namespace App\Controller;

use App\Entity\AuthToken\ChildInvitationToken;
use App\Entity\AuthToken\FamilyInvitationToken;
use App\Entity\Child;
use App\Entity\Family;
use App\Entity\User;
use App\Form\ChildType;
use App\Security\Token\AuthTokenManager;
use App\Security\Voter\EntityAction;
use App\Serializer\AuthTokenSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ChildController extends AbstractController
{
    #[Route(path: '/family/{id}/child', name: 'create_child', methods: ['GET', 'POST'])]
    #[IsGranted(EntityAction::EDIT->value, 'family')]
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

    #[Route(path: '/family/{id}/children', name: 'children_list', methods: ['GET'])]
    #[IsGranted(EntityAction::VIEW->value, 'family')]
    public function index(Family $family): Response
    {
        return $this->render('child/index.html.twig', compact('family'));
    }

    #[Route(path: '/child/{id}', name: 'edit_child', methods: ['GET', 'POST'])]
    #[IsGranted(EntityAction::EDIT->value, 'child')]
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

    #[Route(name: 'accept_child_invitation', path: '/child-invitation', methods: ['GET'])]
    function acceptFamilyInvitation(
        Request $request,
        AuthTokenManager $authTokenManager,
        AuthTokenSerializer $serializer,
        EntityManagerInterface $entityManager
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $t = $request->query->get('t');
        $token = $serializer->deserialize($t, new ChildInvitationToken());
        /** @var ChildInvitationToken|null $token */
        $token = $authTokenManager->findVerified(ChildInvitationToken::class, $token->getSelector(), $token->getPlainVerifier());

        if (is_null($token)) {
            $this->addFlash('error', 'Link malformato o scaduto');
            return $this->redirectToRoute('index');
        }

        if ($user->getEmail() !== $token->getEmail()) {
            $this->addFlash('error', 'Questa operazione può essere completata solo dall\'utente che possiede la mail a cui è stato inviato l\'invito');
            return $this->redirectToRoute('index');
        }
        $child = $token->getChild();
        $user->addRepresrentedChild($token->getChild());
        $entityManager->persist($user);
        $entityManager->flush();
        $authTokenManager->incrementUsage($token);
        $this->addFlash('success', 'Benvenuto nella famiglia "'.$child->getFamily()->getName().'"');
        return $this->redirectToRoute('family_posts', ['id' => $child->getFamily()->getId()]);
    }
}
