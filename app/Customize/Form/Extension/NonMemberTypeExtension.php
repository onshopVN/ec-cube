<?php


namespace Customize\Form\Extension;

use Eccube\Form\Type\Front\NonMemberType;
use Eccube\Form\Type\RepeatedEmailType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\DataTransformer\ValueToDuplicatesTransformer;
use Symfony\Component\Form\FormBuilderInterface;

class NonMemberTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', RepeatedEmailType::class, [
                'first_options' => [
                    'attr' => [
                        'placeholder' => 'common.mail_address_sample',
                    ],
                    'error_bubbling' => false
                ],
                'second_options' => [
                    'attr' => [
                        'placeholder' => 'common.repeated_confirm',
                    ],
                    'required' => false,
                    'error_bubbling' => true,
                    'constraints' => []
                ],
                'error_bubbling' => true
            ])
        ;

        $builder->get('email')->resetViewTransformers();
        $builder->get('email')->addViewTransformer(new ValueToDuplicatesTransformer([
            'first', 'first'
        ]));
    }

    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        // TODO: Implement getExtendedType() method.
        return NonMemberType::class;
    }
}