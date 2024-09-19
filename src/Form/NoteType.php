<?php

namespace App\Form;

use App\Entity\Note;
use App\Entity\User;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

// Ressource: https://symfony.com/doc/current/reference/forms/types.html

class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        // ->add('country')
            ->add('title', TextType::class, [
                'row_attr' => ['class' => 'flex flex-col gap-1'],
                'label' => 'Choose a title',
                'label_attr' => ['class' => 'text-violet-950 font-semibold w-full'],
                'attr'=>[
                    'class' => 'border-2 border-violet-950 rounded-md p-2 w-full focus:border-violet-600',
                    'placeholder' => 'Title of your note',
                ],
                'help' => 'This is the title of your note',
                'help_attr' => ['class' => 'text-sm text-violet-600'],
            ])
            ->add('content', TextareaType::class,[
                'row_attr' => ['class' => 'flex flex-col gap-1'],
                'label' => 'Write your code',
                'label_attr' => ['class' => 'text-violet-950 font-semibold w-full'],
                'attr'=>[
                    'class' => 'border-2 border-violet-950 rounded-md p-2 w-full focus:border-violet-600',
                    'placeholder' => 'What do you want to share on CodeXpress ?',
                ],
                'help' => 'What do you want to share on CodeXpress ?',
                'help_attr' => ['class' => 'text-sm text-violet-600'],

            ])
            ->add('is_public', CheckboxType::class,[
                'label' => 'Public',
                'required' => false,
            ])

            // add('is_public', CheckboxType::class, [
            //     'label' => 'Public',
            //     'required' => false,
            // ]);
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'id',
            ])
            ->add('author', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}
