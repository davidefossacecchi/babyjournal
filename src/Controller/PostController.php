<?php

namespace App\Controller;

use App\Entity\Family;
use App\Entity\Post;
use App\Form\PostType;
use App\Post\PostImageManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class PostController extends AbstractController
{

    #[Route(name: 'post_add', path: '/families/{id}/post', methods: ['GET', 'POST'])]
    #[IsGranted('view', 'family')]
    public function newPostAction(
        Request $request,
        Family $family,
        EntityManagerInterface $entityManager,
        PostImageManagerInterface $imageManager
    )
    {
        $post = new Post();
        $postForm = $this->createForm(PostType::class, $post);
        $postForm->handleRequest($request);

        if($postForm->isSubmitted() && $postForm->isValid()) {
            $image = $postForm->get('image')->getData();;

            if ($image) {
                $hash = $imageManager->getHash($image);
                $filename = $imageManager->moveImage($image);
            }

            $post->setFamily($family)
                ->setAuthor($this->getUser())
                ->setImagePath($filename)
                ->setHash($hash);

            $entityManager->persist($post);
            $entityManager->flush();
            $this->addFlash('success', 'Post caricato correttamente');
            return $this->redirectToRoute('family_posts', ['id' => $family->getId()]);
        }

        return $this->render('family/post.html.twig', ['form' => $postForm]);
    }

    #[Route(name: 'family_posts', path: '/families/{id}/posts', methods: ['GET'])]
    #[IsGranted('view', 'family')]
    public function indexAction(Family $family)
    {
        return $this->render('family/posts.html.twig');
    }
}
