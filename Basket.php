<?php

class Basket
{
    private $products = [];
    private $cart = [];

    public function __construct($products)
    {
        $this->products = $products;
        if (isset($_SESSION['cart'])) {
            $this->cart = $_SESSION['cart'];
        }
    }

    public function add($prcode)
    {
        if (isset($this->products[$prcode])) {
            if (isset($this->cart[$prcode])) {
                $this->cart[$prcode]['quantity']++;
            } else {
                $this->cart[$prcode] = [
                    'name' => $this->products[$prcode]['name'],
                    'price' => $this->products[$prcode]['price'],
                    'quantity' => 1
                ];
            }
            $_SESSION['cart'] = $this->cart; // Update session
        } else {
            throw new Exception("Product code $prcode not found.");
        }
    }

    public function remove($prcode)
    {
        if (isset($this->cart[$prcode]) && $this->cart[$prcode]['quantity'] > 1) {
            $this->cart[$prcode]['quantity']--;
            $_SESSION['cart'] = $this->cart; // Update session
        } else {
            unset($this->cart[$prcode]);
            $_SESSION['cart'] = $this->cart; // Update session
        }
    }

    public function getCart()
    {
        return $this->cart;
    }

    public function calculatePrice()
    {
        $total = 0;
        foreach ($this->cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    public function calculateDuties()
    {
        $duties = 0;
        $total = $this->calculatePrice();
        // caculate delivery charges based on total
        if (count($this->cart) > 0) {
            if ($total < 50) {
                $duties = 4.95;
            } elseif ($total < 90) {
                $duties = 2.95;
            }
        }
        return $duties;
    }
}
