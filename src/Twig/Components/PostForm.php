<?php

namespace App\Twig\Components;

use App\Entity\Family;
use App\Entity\Timepoints\Post;
use App\Form\PostType;
use App\Post\PostImageManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;

#[AsLiveComponent]
class PostForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ValidatableComponentTrait;

    #[LiveProp(writable: true)]
    public string $caption = '';

    #[Assert\File(maxSize: '10M', mimeTypes: 'image/*')]
    private ?UploadedFile $image = null;
    #[LiveProp]
    public ?int $familyId = null;


    #[LiveAction]
    public function save(
        EntityManagerInterface $entityManager,
        PostImageManagerInterface $imageManager,
        Request $request
    )
    {
        $post = new Post();
        $this->image = $request->files->get('image');

        $this->validate();

        if ($this->image) {
            $hash = $imageManager->getHash($this->image);
            $filename = $imageManager->moveImage($this->image);
        }

        $family = $entityManager->getRepository(Family::class)
            ->find($this->familyId);

        $post->setFamily($family)
            ->setAuthor($this->getUser())
            ->setImagePath($filename)
            ->setHash($hash);

        $entityManager->persist($post);
        $entityManager->flush();
        $this->addFlash('feed:success', 'Post caricato correttamente');

        $this->emit('timepointAdded');
        $this->dispatchBrowserEvent('modal:close');
        $this->image = null;
        $this->caption = '';
    }
}
