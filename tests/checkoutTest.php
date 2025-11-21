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

    // Test 1: Empty cart
    public function testEmptyCartReturnsO(): void
    {
        $cart = '';
        $rules = [];

        $price = calculatePriceOfCart($cart, $rules);
        $this->assertEquals(0, $price);
    }

    // Test 2-5: Single items
    public function testSingleItemA(): void
    {
        // A: 50
        $this->assertEquals(50, calculatePriceOfCart('A', $this->pricingRules));
    }

    public function testSingleItemB(): void
    {
        // B: 30
        $this->assertEquals(30, calculatePriceOfCart('B', $this->pricingRules));
    }

    public function testSingleItemC(): void
    {
        // C: 20
        $this->assertEquals(20, calculatePriceOfCart('C', $this->pricingRules));
    }

    public function testSingleItemD(): void
    {
        // D: 15
        $this->assertEquals(15, calculatePriceOfCart('D', $this->pricingRules));
    }

    // Test 6-8: Multiple same items without reaching special offer
    public function testTwoItemsA(): void
    {
        $this->assertEquals(100, calculatePriceOfCart('AA', $this->pricingRules)); // 2*A = 100
    }

    public function testTwoItemsB(): void
    {
        $this->assertEquals(45, calculatePriceOfCart('BB', $this->pricingRules)); // 2*B special = 45
    }

    public function testTwoItemsC(): void
    {
        $this->assertEquals(40, calculatePriceOfCart('CC', $this->pricingRules)); // 2*C = 40
    }

    // Test 9-11: Special offer for item A (3 for 130)
    public function testSpecialOfferAExact(): void
    {
        $this->assertEquals(130, calculatePriceOfCart('AAA', $this->pricingRules)); // 3 for 130
    }

    public function testSpecialOfferAWithExtra(): void
    {
        $this->assertEquals(180, calculatePriceOfCart('AAAA', $this->pricingRules)); // 3 for 130 + 1 for 50
    }

    public function testSpecialOfferBWithExtra(): void
    {
        $this->assertEquals(75, calculatePriceOfCart('BBB', $this->pricingRules)); // 2 for 45 + 1 for 30
    }

    public function testSpecialOfferBDoubleSet(): void
    {
        $this->assertEquals(90, calculatePriceOfCart('BBBB', $this->pricingRules)); // 2 for 45 + 2 for 45
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