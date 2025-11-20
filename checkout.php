<?php

function calculatePriceOfCart(string $cart, array $pricingRules): int
{
    if (empty($cart)) {
        return 0;
    }

    // Count occurrences of each item
    $itemCounts = array_count_values(str_split($cart));
    
    $total = 0;
    
    foreach ($itemCounts as $item => $count) {
        if (!isset($pricingRules[$item])) continue;
        
        $rule = $pricingRules[$item];
        $unitPrice = $rule['unit_price'];
        
        // Apply special offers if they exist
        if (isset($rule['special_price'])) {
            $special = $rule['special_price'];
            
            if (isset($special['type']) && $special['type'] === 'buy_x_get_y') {
                // Handle "Buy X get Y free" 
                $buyX = $special['buy_quantity'];
                $getYFree = $special['free_quantity'];
                
                $bundleSize = $buyX + $getYFree;
                $bundles = floor($count / $bundleSize);
                $remainingItems = $count % $bundleSize;
                
                // Items to charge for in bundles
                $chargedInBundles = $bundles * $buyX;
                
                // Remaining items to charge individually
                $total += ($chargedInBundles + $remainingItems) * $unitPrice;
                
            } else {
                // Handle "N for Y" offers for A and B
                $offerQuantity = $special['quantity'];
                $offerPrice = $special['price'];
                
                $offerSets = floor($count / $offerQuantity);
                $remainingItems = $count % $offerQuantity;
                
                $total += ($offerSets * $offerPrice) + ($remainingItems * $unitPrice);
            }
        } else {
            // No special offer, just unit price
            $total += $count * $unitPrice;
        }
    }
    
    return $total;

}

$pricingRules = [
    'A' => [
        'unit_price' => 50,
        'special_price' => [
            'quantity' => 3,
            'price' => 130
        ]
    ],
    'B' => [
        'unit_price' => 30,
        'special_price' => [
            'quantity' => 2,
            'price' => 45
        ]
    ],
    'C' => [
        'unit_price' => 20,
        'special_price' => [
            'type' => 'buy_x_get_y',
            'buy_quantity' => 2,   // buy 2
            'free_quantity' => 1   // get 1 free
        ]
    ],
    'D' => [
        'unit_price' => 15
    ]
];

/* echo "Test 1: AABBCABD → ";
echo calculatePriceOfCart("AABBCABD", $pricingRules);
echo "\n";

echo "Test 1: BAB → ";
echo calculatePriceOfCart('BAB', $pricingRules);
echo "\n"; */

?>