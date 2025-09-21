<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

/**
 * Expenses/Income.
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

final class PdfUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'pdf',
                FileType::class,
                [
                    'label'       => 'PDF файл',
                    'mapped'      => false,
                    'required'    => true,
                    'constraints' => [
                        new File(
                            maxSize: '10M',
                            mimeTypes: [
                                'application/pdf',
                                'application/x-pdf',
                            ],
                            mimeTypesMessage: 'Пожалуйста, загрузите PDF-документ',
                        ),
                    ],
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Filter',
                ]
            );
    }//end buildForm()

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }//end configureOptions()
}//end class
