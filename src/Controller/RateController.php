<?php

namespace App\Controller;

use App\Component\DTO\ConverterRequest;
use App\Component\DTO\ConverterResponse;
use App\Entity\Rate;
use App\Exception\CurrencyConverterException;
use App\Form\ConverterRequestType;
use App\Repository\RateRepository;
use App\Service\CurrencyConverter;
use App\Service\DateFactory;
use App\Service\SourceConfiguration;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Rest\Route("/public/rate")
 */
class RateController extends AbstractFOSRestController
{
    private PaginatorInterface $pagination;

    private CurrencyConverter $converter;

    private SourceConfiguration $sourceConfiguration;

    private DateFactory $dateFactory;

    public function __construct(
        PaginatorInterface $pagination,
        CurrencyConverter $converter,
        SourceConfiguration $sourceConfiguration,
        DateFactory $dateFactory
    ) {
        $this->pagination          = $pagination;
        $this->converter           = $converter;
        $this->sourceConfiguration = $sourceConfiguration;
        $this->dateFactory         = $dateFactory;
    }

    /**
     * @Rest\Route("/list")
     * @Rest\View(serializerGroups={"knp_basic", "Default"})
     *
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return PaginationInterface
     */
    public function getListAction(Request $request)
    {
        $page  = $request->get('page', 1);
        $limit = $request->get('limit', 10);

        /** @var RateRepository $repo */
        $repo    = $this->getDoctrine()->getRepository(Rate::class);
        $builder = $repo->getListQB($this->sourceConfiguration->getDefaultSource(), $this->dateFactory->getTodayDate());

        return $this->pagination->paginate($builder, $page, $limit);
    }

    /**
     * @Rest\Route
     * @Rest\View
     *
     * @param Request $request
     *
     * @return array|ConverterResponse
     */
    public function getAction(Request $request)
    {
        $form = $this->createForm(ConverterRequestType::class, null, [
            'method' => Request::METHOD_GET,
        ])->handleRequest($request);

        if (!$form->isSubmitted()) {
            $form->submit([]);
        }

        if (!$form->isValid()) {
            return [
                'form' => $form,
            ];
        }

        /** @var ConverterRequest $converterRequest */
        $converterRequest = $form->getData();

        try {
            $response = $this->converter->convert($converterRequest);
        } catch (CurrencyConverterException $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }

        return $response;
    }
}
