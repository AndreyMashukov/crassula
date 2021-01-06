<?php

namespace App\Form;

use App\Component\DTO\ConverterRequest;
use DateTimeImmutable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConverterRequestType extends AbstractType implements DataMapperInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currencyFrom', TextType::class, [
                'empty_data' => '',
            ])
            ->add('currencyTo', TextType::class, [
                'empty_data' => '',
            ])
            ->add('amount', NumberType::class, [
                'empty_data' => '0',
            ])
            ->add('date', DateType::class, [
                'format'     => DateType::HTML5_FORMAT,
                'widget'     => 'single_text',
                'empty_data' => (new DateTimeImmutable('today'))->format('Y-m-d'),
            ])
            ->setDataMapper($this)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'empty_data'         => null,
            'csrf_protection'    => false,
            'data_class'         => ConverterRequest::class,
            'allow_extra_fields' => true,
        ]);
    }

    /**
     * @param null|ConverterRequest $viewData
     * @param iterable|\Traversable $data
     */
    public function mapDataToForms($viewData, iterable $data)
    {
        if (null === $viewData) {
            return;
        }
        if (!$viewData instanceof ConverterRequest) {
            throw new UnexpectedTypeException($viewData, ConverterRequest::class);
        }

        /** @var FormInterface[] $forms */
        $forms = \iterator_to_array($data);
        $forms['currencyFrom']->setData($viewData->getCurrencyFrom());
        $forms['currencyTo']->setData($viewData->getCurrencyTo());
        $forms['amount']->setData($viewData->getAmount());
        $forms['date']->setData($viewData->getDate());
    }

    /**
     * @param iterable|\Traversable $data
     * @param null|ConverterRequest $viewData
     */
    public function mapFormsToData(iterable $data, &$viewData)
    {
        /** @var FormInterface[] $forms */
        $forms    = \iterator_to_array($data);
        $viewData = new ConverterRequest(
            $forms['currencyFrom']->getData(),
            $forms['currencyTo']->getData(),
            $forms['amount']->getData(),
            $forms['date']->getData()
        );
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
