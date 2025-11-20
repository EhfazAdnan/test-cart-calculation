<?php
use PHPUnit\Framework\TestCase;

final class checkoutTest extends TestCase
{
    private $pricingRules;
    protected function setUp(): void
    {
        $this->pricingRules = [
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
    }

    public function testEmptyCartReturnsO(): void
    {
        $cart = '';
        $rules = [];

        $price = calculatePriceOfCart($cart, $rules);
        $this->assertEquals(0, $price);
    }

    public function testSingleItems()
    {
        $this->assertEquals(50, calculatePriceOfCart('A', $this->pricingRules)); // A=50
        $this->assertEquals(30, calculatePriceOfCart('B', $this->pricingRules)); // B=30
        $this->assertEquals(20, calculatePriceOfCart('C', $this->pricingRules)); // C=20
        $this->assertEquals(15, calculatePriceOfCart('D', $this->pricingRules)); // D=15
    }

    public function testMixedItemsSimple()
    {
        // A=50, B=45 (2 for 45), C=20, D=15
        $this->assertEquals(130, calculatePriceOfCart('ABBCD', $this->pricingRules));
    }

    public function testMixedItemsInComplex(): void
    {
        // AABBCABD = A=3, B=3, C=1, D=1
        // A: 3 for 130
        // B: 2 for 45 + 1 for 30 = 75  
        // C: 1 for 20
        // D: 1 for 15
        // Total: 130 + 75 + 20 + 15 = 240
        $price = calculatePriceOfCart('AABBCABD', $this->pricingRules);
        $this->assertEquals(240, $price);
    }
}

?>