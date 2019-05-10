<?php

namespace App\Services;

use Illuminate\Support\Facades\Cookie;

class ShoppingCart
{
    /**
     * @param array $Data
     */
    public function addToCart( array $Data ): void
    {
        Cookie::queue(Cookie::make('cart_items', serialize($Data), time() + (365 * 24 * 60 * 60) ));
    }

    /**
     * @param array $Data
     */
    public function updateItem( array $Data )
    {
        $CartItmes = $this->getCartItems();

        foreach ($CartItmes as &$Item)
        {
            if($Data['product_id'] == $Item['product_id'])
            {
                $Item['quantity'] = $Data['quantity'];
            }
        }

        $this->addToCart( $CartItmes );
    }

    /**
     * @param int $ProductId
     */
    public function removeFromCart( int $ProductId ): void
    {
        $CartItmes = $this->getCartItems();

        foreach ($CartItmes as $Key => $Value)
        {
            if($ProductId == $Value['product_id'])
            {
                unset($CartItmes[$Key]);
            }
        }

        $this->addToCart($CartItmes);
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        if( isset($_COOKIE['cart_items']) )
        {
            Cookie::queue(Cookie::make('cart_items', null ));
        }
    }

    /**
     * @param int $ProductId
     * @return array|null
     */
    public function getById( int $ProductId ): ?array
    {
        $Items = $this->getCartItems();

        if( !empty( $Items ) )
        {
            foreach ($Items as $Item)
            {
                if( $Item['product_id'] == $ProductId )
                {
                    return $Item;
                }
            }
        }

        return null;
    }

    /**
     * @return array|null
     */
    public function getCartItems(): array
    {
        $CartItems = [];

        $CartItemsCookie = $_COOKIE['cart_items'] ?? null;

        if( $CartItemsCookie )
        {
            $CartItems = unserialize( $CartItemsCookie );
        }

        return $CartItems;
    }

    /**
     * @param array$CartItems
     * @return int
     */
    public function getSubtotal( array $CartItems = null ): string
    {
        if( !$CartItems )
        {
            $CartItems = $this->getCartItems();
        }

        $Price = 0;

        foreach ($CartItems as $CartItem)
        {
            $Price += $CartItem['price'];
        }

        return number_format( $Price, 2, '.', ',' );
    }
}