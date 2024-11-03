<?php

namespace App\Controller;

use App\Entity\Family;
use App\Entity\Timepoints\Post;
use App\Form\PostType;
use App\Post\PostImageManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
            $image = $postForm->get('image')->getData();

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
    public function indexAction(Request $request, Family $family, string $pageParam): Response
    {
        $children = $family->getChildren();
        if (count($children) === 0) {
            return $this->redirectToRoute('create_child', ['id' => $family->getId()]);
        }
        return $this->render(
            'family/posts.html.twig',
            [
                'family' => $family,
                'page' => $request->query->getInt($pageParam, 1)
            ]
        );
    }

    #[Route(name: 'post_image', path: '/post-image/{hash}/{filename}')]
    public function getImageAction(string $filename, string $hash, EntityManagerInterface $em, PostImageManagerInterface $imageManager)
    {
        $repo = $em->getRepository(Post::class);
        $post = $repo->findOneBy(['imagePath' => $filename, 'hash' => $hash]);

        if (empty($post)) {
            throw $this->createNotFoundException();
        }

        if (false === $this->isGranted('view', $post->getFamily())) {
            throw $this->createAccessDeniedException();
        }

        $imagePath = $imageManager->getImageFolder($filename).'/'.$filename;

        return new BinaryFileResponse($imagePath);
    }
}
