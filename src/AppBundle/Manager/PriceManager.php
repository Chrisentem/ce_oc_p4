<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Purchase;
use AppBundle\Entity\EntryTicket;


class PriceManager {

    const PRICE_BABY = 0;
    const PRICE_CHILDREN = 8;
    const PRICE_REDUCED = 10;
    const PRICE_NORMAL = 16;
    const PRICE_SENIOR = 12;
    
    const LEVEL_BABY = 4;
    const LEVEL_CHILDREN = 12;
    const LEVEL_NORMAL = 60;

    const HALF_DAY_COEFF = 0.5;

    /**
     * @param $age
     * @return int
     */
    private function getPriceFromAge($age)
    {
        if ($age < self::LEVEL_BABY) {
            $price = self::PRICE_BABY;
        }
        elseif ($age < self::LEVEL_CHILDREN) {
            $price = self::PRICE_CHILDREN;
        }
        elseif ($age < self::LEVEL_NORMAL) {
            $price = self::PRICE_NORMAL;
        }
        else {
            $price = self::PRICE_SENIOR;
        }
        return $price;

    }

    /**
     * @param EntryTicket $ticket
     * @return float|int
     */
    public function computeTicketPrice(EntryTicket $ticket)
    {
        $basePrice = $this->getPriceFromAge($ticket->getAge());
        $discount = $ticket->getDiscounted();
        $ticketType = $ticket->getPurchase()->getTicketType();

        if ($discount == true && $basePrice > self::PRICE_BABY) {
            if ($ticketType == Purchase::HALF_DAY_TICKET_TYPE) {
                $price = self::PRICE_REDUCED * self::HALF_DAY_COEFF;
            } else {
                $price = self::PRICE_REDUCED;
            }
        }
        elseif ($ticketType == Purchase::HALF_DAY_TICKET_TYPE) {
            $price = $basePrice * self::HALF_DAY_COEFF;
        }
        else {
            $price = $basePrice;
        }

        $ticket->setPrice($price);
        return $price;
    }

    /**
     * @param Purchase $purchase
     */
    public function computePurchasePrice(Purchase $purchase)
    {
        $tickets = $purchase->getTickets();

        $prices = [];

        foreach ($tickets as $ticket) {
            $this->computeTicketPrice($ticket);
            array_push($prices, $ticket->getPrice());
        }
        $purchase->setTotal(array_sum($prices));
    }

}