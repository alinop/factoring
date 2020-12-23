<?php

namespace App\Controller;

use App\Entity\AdherentDebtor;
use App\Entity\Invoice;
use App\Form\Type\InvoiceType;
use App\Repository\AdherentDebtorRepository;
use App\Repository\InvoiceRepository;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bridge\Doctrine\PropertyInfo\DoctrineExtractor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

/**
 * Class InvoiceController
 * @package App\Controller
 */
class InvoiceController extends AbstractController
{
    private $twig;

    private $entityManager;

    private $em;

    private $paginator;

    public function __construct(Environment $twig, EntityManagerInterface $entityManager, ObjectManager $em, PaginatorInterface $paginator)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
        $this->em = $em;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/invoices", name="invoices")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function show(Request $request)
    {
        $approvedAmount = $request->query->get('approvedAmount');
        $adherent = $request->query->get('adherent');
        $debtor = $request->query->get('debtor');
        $sortField = $request->query->get('sortField');
        $sortType = $request->query->get('sortType');

        /** @var InvoiceRepository $invoicesRepo */
        $invoicesRepo = $this->entityManager->getRepository(Invoice::class);
        $approvedAmountRange = $invoicesRepo->getApprovedRange()[0];

        $minAmount = $approvedAmountRange['min'];
        $maxAmount = $approvedAmountRange['max'];

        if (!empty($approvedAmount)) {
            $betweenAmount = explode(",", $approvedAmount);
            $minAmount = $betweenAmount[0];
            $maxAmount = $betweenAmount[1];
        }

        $filters = [
            'adherent' => $adherent,
            'debtor' => $debtor,
            'invoiceRange' => ['min' => $minAmount, 'max' => $maxAmount],
            'sort' => [
                'sortField' => $sortField,
                'sortType' => $sortType
            ]
        ];

        /** @var AdherentDebtorRepository $adherentDebtorRepo */
        $adherentDebtorRepo = $this->entityManager->getRepository(AdherentDebtor::class);
        $adherentDebtor = $adherentDebtorRepo->findAll();

        $queryBuilder = $invoicesRepo->getFilteredData($minAmount,$maxAmount, $adherent, $debtor, $sortField, $sortType);

        $pagination = $this->paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            Invoice::ITEMS_PER_PAGE/*limit per page*/
        );

        $totalAmounts = $invoicesRepo->getTotalAmounts($queryBuilder)[0];

        return new Response($this->twig->render('show.html.twig', [
            'pagination' => $pagination,
            'adherentDebtors' => $adherentDebtor,
            'totalAmounts' => $totalAmounts,
            'filters' => $filters
        ]));
    }

    /**
     * @Route("/invoice/{id}", name="invoice")
     * @param Invoice $id
     * @param Request $request
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function edit(Invoice $id, Request $request)
    {
        $form = $this->createForm(InvoiceType::class, $id);
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            //var_dump($request->request->get('adherent'));die;
            if ($form->isSubmitted() && $form->isValid()) {
                // perform some action...
                /** @var Invoice $invoice */
                $invoice = $form->getData();
                $this->em->persist($invoice);
                $this->em->flush();

                return $this->redirectToRoute('invoices');
            }
        }
        return new Response($this->twig->render('edit.html.twig', ['form' => $form->createView()]));
    }

}