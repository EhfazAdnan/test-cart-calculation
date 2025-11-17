<<?php 
use PHPUnit\Framework\TestCase; 

final class checkoutTest extends TestCase
{
    public function testEmptyCartReturnsO(): void
    {
        $cart = '';
        $rules = [];

        $price = calculatePriceOfCart($cart, $rules);

        $this->assertEquals(0, $price);
    }

    private $pricingRules;

    protected function setUp(): void
    {
        $this->pricingRules = [
            'A' => [
                'unit_price' => 50,
                'promotion' => ['quantity' => 3, 'price' => 130]
            ],
            'B' => [
                'unit_price' => 30,
                'promotion' => ['quantity' => 2, 'price' => 45]
            ],
            'C' => [
                'unit_price' => 20,
                'promotion' => ['quantity' => 2, 'price' => 20, 'free' => 1]
            ],
            'D' => [
                'unit_price' => 15
            ]
        ];
    }

    public function testSingleItemA(): void
    {
        $price = calculatePriceOfCart('A', $this->pricingRules);
        $this->assertEquals(50, $price);
    }

    public function testMixedItemsInOrder(): void
    {
        $price = calculatePriceOfCart('AABBCABD', $this->pricingRules);
        $this->assertEquals(230, $price);
    }
}

?>