<?php

function calculatePriceOfCart(string $cart, array $pricingRules): int
{
    if (empty($cart)) {
        return 0;
    }

    // Count items
    $items = count_chars($cart, 1);
    $total = 0;

    foreach ($items as $ascii => $qty) {
        $sku = chr($ascii);

        if (!isset($pricingRules[$sku])) {
            continue;
        }

        $unitPrice  = $pricingRules[$sku]['unit_price'];
        $promotion  = $pricingRules[$sku]['promotion'] ?? null;

        if ($promotion && isset($promotion['price'])) {
            $promoQty   = $promotion['quantity'];
            $promoPrice = $promotion['price'];

            $promoCount     = intdiv($qty, $promoQty);
            $remainingItems = $qty % $promoQty;

            $total += ($promoCount * $promoPrice) + ($remainingItems * $unitPrice);
        }
        elseif ($promotion && isset($promotion['free'])) {
            $need = $promotion['quantity'];
            $free = $promotion['free'];
            $groupSize = $need + $free;

            $groups = intdiv($qty, $groupSize);

            $paid = $groups * $need;

            $remaining = $qty % $groupSize;

            $paid += $remaining;

            $total += $paid * $unitPrice;
        }else {
            $total += $qty * $unitPrice;
        }
    }

    return $total;
}

$pricingRules = [
    'A' => [
        'unit_price' => 50,
        'promotion'  => [
            'quantity' => 3,
            'price'    => 130
        ]
    ],
    'B' => [
        'unit_price' => 30,
        'promotion'  => [
            'quantity' => 2,
            'price'    => 45
        ]
    ],
    'C' => [
        'unit_price' => 20,
        'promotion'  => [
            'quantity' => 2,
            'free'     => 1
        ]
    ],
    'D' => [
        'unit_price' => 15
    ]
];

echo "Test 1: AABBCABD → ";
echo calculatePriceOfCart("AABBCABD", $pricingRules);
echo "\n";

$aPrice = calculatePriceOfCart('AABBCABD', $pricingRules);
echo $aPrice;
echo "\n";

?>