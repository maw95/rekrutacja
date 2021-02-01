<?php
include_once getcwd() . '/simplehtmldom/simple_html_dom.php';
include_once getcwd() . '/functions.php';
if($_GET['product_url'])
{
    //pobieramy HTML strony na podstawie URL otrzymanego w parametrze
    $product_html = file_get_html($_GET['product_url']);
    $data = [];
    $data['name'] = $product_html->find('p.card-text')[0]->innertext;
    //znajdujemy json-a zaszytego w kodzie karty produktu i dekodujemy go - zapisujemy go jako tablicę asocjacyjną, łatwiej robić pętle
    $json = json_decode($product_html->find('script[type="application/json"]')[0]->innertext, true);
    //jeśli produkt ma warianty, zapisujemy je w oddzielnym miejscu tablicy
    if(count($json['products']['variants'])>0)
    {
        $data['variants'] = [];
        $i = 0;
        foreach($json['products']['variants'] as $key => $variant)
        {
            //zapisujemy nazwę, cenę i ew. starą cenę każdego wariantu
            $data['variants'][$i]['name'] = $data['name'] . ' ' . $key;
            $data['variants'][$i]['price'] = $variant['price'];
            if(isset($variant['price_old']))
            {
                $data['variants'][$i]['old_price'] = $variant['price_old'];
            }
            $i++;
        }
    }
    if(count($product_html->find('.price-old'))> 0)
    {
        $data['old_price'] = $product_html->find('.price-old')[0]->innertext;
        //jeśli produkt nie ma wariantów i jest przeceniony, nowa cena ma selektor 'price-promo' zamiast 'price'
        $data['price'] = $product_html->find('.price-promo')[0]->innertext;
    }
    else
    {
        $data['price'] = $product_html->find('.price')[0]->innertext;
    }
    $data['img_url'] = $product_html->find('.card-img-top')[0]->attr['src'];
    $data['code'] = $json['products']['code'];
    //żeby szybko wyciągnąć info o ilości ocen i średniej ocenie, wykorzystujemy funkcje z zadania 1
    $data['reviews'] = getReviewsCount($product_html->find('.card-footer small')[0]->innertext);
    $data['rating'] = getRating($product_html->find('.card-footer small')[0]->innertext);
    //kodujemy tablicę do json-a i wypisujemy ją
    echo json_encode($data);
}
//jeśli ktoś próbuje się dostać na podstronę bez przekazania parametru 'product_url', wraca na stronę główną
else
{
    header('Location: index.php');
}
?>