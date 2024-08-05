<?php

// Calculate Red offer
function redWidgetOffer($item, $products)
{
    if ($item['quantity'] > 1) {
        return ($products['R01']['price'] / 2);
    }
    return 0;
};

// format Price regarding currency
function formatPrice($amount, $curr = 'usd')
{
    $ret = '';
    $curr = strtolower($curr);
    switch ($curr) {
        case 'usd':
            $ret = '$' . number_format($amount, 2);
            break;

        case 'gbp':
            $ret = '£' . number_format($amount, 2);
            break;
        case 'eur':
            $ret = '€' . number_format($amount, 2);
            break;

        default:
            $ret = '$' . number_format($amount, 2);
            break;
    }

    return $ret;
}
