<?php

namespace App\Http\Helpers\PurchaseBill;

class PurchaseBillOperations {

    public function calculateTotal($quantity, $sellprice, $tax, $discount) {
        $total = (int)$quantity * (float)$sellprice;
        $tax = ((float)$tax / 100) * $total;
        $discount = ((float)$discount / 100) * $total;
        $newItemTotal = $total + $tax - $discount;
        return $newItemTotal;
    }

    public function calculatePurchasePricePerItem($billTotal, $quantity) {
        if((int)$quantity === 0)
            return 0;
        return $billTotal / (int)$quantity;
    }
}