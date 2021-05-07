<?php

namespace App\Controller;

use App\Entity\Customers;
use App\Repository\CustomersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

/**
 * Class CustomerSiteController
 * @package App\Controller
 *
 * @Route(path="/api")
 */
class CustomersController extends AbstractController
{
    private $customersRepository;

    public function __construct(CustomersRepository $customersRepository)
    {
        $this->customersRepository = $customersRepository;
    }

    /**
     * @Route("/add", name="add", methods={"POST"})
     */
    public function addCustomer(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $form = $this->createFormBuilder(new Customers())
                     ->add('firstName', TextType::class)
                     ->add('lastName', TextType::class)
                     ->add('email', EmailType::class)
                     ->add('phoneNumber', TextType::class)
                     ->getForm();

        $form->handleRequest($request);       
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->customersRepository->addCustomer($data['firstName'], $data['lastName'], $data['email'], $data['phoneNumber']);
            return new JsonResponse(['status' => 'Customer added!'], Response::HTTP_CREATED);
        }

        return new JsonResponse(['error' => 'Is not valid!'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/all", name="all", methods={"GET"})
     */
    public function getAllCustomers(): JsonResponse
    {
        $customers = $this->customersRepository->findAll();
        $data = [];

        foreach ($customers as $customer) {
            $data[] = [
                'id' => $customer->getId(),
                'firstName' => $customer->getFirstName(),
                'lastName' => $customer->getLastName(),
                'email' => $customer->getEmail(),
                'phoneNumber' => $customer->getPhoneNumber(),
            ];
        }

        return new JsonResponse(['customers' => $data], Response::HTTP_OK);
    }    

}