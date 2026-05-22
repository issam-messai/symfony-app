<?php

namespace App\DataFixtures;

use App\Entity\Invoice;
use App\Entity\InvoiceItem;
use App\Entity\Order;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Invoice with items (id=1 will work, id=999 will trigger the bug)
        $invoice = new Invoice();
        $invoice->setTotal(250.0)->setStatus('paid');

        $item1 = new InvoiceItem();
        $item1->setLabel('Consulting — 2h')->setPrice(150.0)->setInvoice($invoice);

        $item2 = new InvoiceItem();
        $item2->setLabel('Setup fee')->setPrice(100.0)->setInvoice($invoice);

        $invoice->addItem($item1)->addItem($item2);
        $manager->persist($invoice);

        // Orders for customer 1 — mixed statuses
        // Only 1 has status 'active' — bug will return only 1 instead of 5
        $statuses = ['active', 'pending', 'completed', 'cancelled', 'pending'];
        foreach ($statuses as $i => $status) {
            $order = new Order();
            $order->setCustomerId(1)
                ->setStatus($status)
                ->setReference('ORD-' . str_pad((string)($i + 1), 4, '0', STR_PAD_LEFT));
            $manager->persist($order);
        }

        // Orders for customer 2
        $order = new Order();
        $order->setCustomerId(2)->setStatus('active')->setReference('ORD-0010');
        $manager->persist($order);

        $manager->flush();
    }
}
