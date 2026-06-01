<?php

namespace App\Controller;

use App\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvoiceController extends AbstractController
{
    public function __construct(private InvoiceRepository $invoiceRepository)
    {
    }

    #[Route('/invoice/{id}', name: 'invoice_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $invoice = $this->invoiceRepository->find($id); 

        if(!$invoice){
            throw $this->createNotFoundException("l'id ". $id. " est absent en base");
        }

        

        return $this->render('invoice/show.html.twig', [
            'invoice' => $invoice,
            'total'   => $invoice->getTotal(),
            'items'   => $invoice->getItems(),
        ]);
    }

    #[Route('/invoices', name: 'invoice_list', methods: ['GET'])]
    public function list(): Response
    {
        $invoices = $this->invoiceRepository->findAll();

        return $this->render('invoice/list.html.twig', [
            'invoices' => $invoices,
        ]);
    }
}
