<?php

namespace App\Controller;

use App\Entity\AuthToken\FamilyInvitationToken;
use App\Entity\Child;
use App\Entity\Family;
use App\Entity\User;
use App\Form\FamilyType;
use App\Security\Token\AuthTokenManager;
use App\Serializer\AuthTokenSerializer;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class FamilyController extends AbstractController
{

    #[Route(name: 'family_index', path: '/families', methods: ['GET'])]
    public function index()
    {
        /** @var User $user */
        $user = $this->getUser();
        $families = $user->getFamilies();

        $allFamilies = $user->getRepresentedChildren()->reduce(function (Collection $acc, Child $child) {
            $acc->add($child->getFamily());
            return $acc;
        }, $families);

        return $this->render('family/index.html.twig', ['families' => $allFamilies]);
    }

    #[Route(name: 'family_create', path: '/create-family', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager)
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(FamilyType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Family $family */
            $family = $form->getData();
            $family->addUser($user);
            $entityManager->persist($family);
            $entityManager->flush();
            $this->addFlash('success', 'Famiglia "'.$family->getName().'" aggiunta');
            return $this->redirectToRoute('family_index');
        }

        return $this->render('family/form.html.twig', compact('form'));
    }

    #[Route(name: 'family_edit', path: '/families/{id}', methods: ['GET', 'POST'])]
    #[IsGranted('edit', 'family')]
    public function edit(Request $request, Family $family, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(FamilyType::class, $family);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Family $family */
            $family = $form->getData();
            $entityManager->persist($family);
            $entityManager->flush();
            $this->addFlash('success', 'Famiglia "'.$family->getName().'" modificata');
            return $this->redirectToRoute('family_index');
        }

        return $this->render('family/form.html.twig', compact('form', 'family'));
    }

    #[Route(name: 'accept_family_invitation', path: '/family-invitation', methods: ['GET'])]
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
        $token = $serializer->deserialize($t, new FamilyInvitationToken());
        /** @var FamilyInvitationToken|null $token */
        $token = $authTokenManager->findVerified(FamilyInvitationToken::class, $token->getSelector(), $token->getPlainVerifier());

        if (is_null($token)) {
            $this->addFlash('error', 'Link malformato o scaduto');
            return $this->redirectToRoute('index');
        }

        if ($user->getEmail() !== $token->getEmail()) {
            $this->addFlash('error', 'Questa operazione può essere completata solo dall\'utente che possiede la mail a cui è stato inviato l\'invito');
            return $this->redirectToRoute('index');
        }
        $user->addFamily($token->getFamily());
        $entityManager->persist($user);
        $entityManager->flush();
        $authTokenManager->incrementUsage($token);
        $this->addFlash('success', 'Benvenuto nella famiglia "'.$token->getFamily()->getName().'"');
        return $this->redirectToRoute('family_posts', ['id' => $token->getFamily()->getId()]);
    }
}
