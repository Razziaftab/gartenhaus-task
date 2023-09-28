<?php

namespace App\Controller;

use App\Service\CurrencyConversionServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class CurrencyExchangeController extends AbstractController
{
    private CurrencyConversionServiceInterface $currencyConversionService;

    public function __construct(CurrencyConversionServiceInterface $currencyConversionService)
    {
        $this->currencyConversionService = $currencyConversionService;
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/', name: 'currency_exchange')]
    public function index(Request $request): Response
    {
        $form = $this->CurrencyExchangeFormBuilder();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $fromCurrency = $data['fromCurrency'];
            $toCurrency = $data['toCurrency'];
            $amount = $data['amount'];

            $data = $this->currencyConversionService->convertCurrency($fromCurrency, $toCurrency, $amount);

            return $this->render('currency_exchange/index.html.twig', [
                'result' => $data['result'],
                'updatedAt' => $data['updated_time'],
                'form' => $form->createView(),
            ]);
        }

        return $this->render('currency_exchange/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return FormInterface
     */
    private function CurrencyExchangeFormBuilder(): FormInterface
    {
        return $this->createFormBuilder()
            ->add('fromCurrency', TextType::class, [
                'label' => 'From Currency',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter ISO code',
                ],
                'constraints' =>[
                    new NotBlank(),
                    new Length(3)
                ]
            ])
            ->add('toCurrency', TextType::class, [
                'label' => 'To Currency',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter ISO code',
                ],
                'constraints' =>[
                    new NotBlank(),
                    new Length(3)
                ]
            ])
            ->add('amount', NumberType::class, [
                'label' => 'Amount',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter amount',
                ],
                'constraints' =>[
                    new NotBlank(),
                    new Positive()
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Convert',
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
            ])
            ->getForm();
    }
}
