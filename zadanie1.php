<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        button, div
        {
            border: 2px solid #333333;
            background-color: #333333;
            color: #fff;
            border-radius: 0.5em;
            font-size: 2em;
            text-align: center;
            padding: 1em 2em;
            transition: 0.3s all ease-in-out;
            cursor: pointer;
        }
        button:hover
        {
            color: #333333;
            background-color: #fff;
        }
    </style>
</head>
<body>
<?php

//definiujemy docelowy URL jako stałą
define("MAIN_PAGE_URL", "http://estoremedia.space/DataIT/");
//podpinamy simpehtmldom
include_once getcwd() . '/simplehtmldom/simple_html_dom.php';
include_once getcwd() . '/functions.php';

//zapisujemy HTML głównej strony do zmiennej
$main_page = file_get_html(MAIN_PAGE_URL);

//tworzymy tablicę, w której będą przechowywane zmienne z HTML-em produktów
$products = [];
/*znajdujemy linki do podstron z kolejnymi produktami - gdyby paginacja była zrobiona w JS, moglibyśmy pominąć ten krok.
Nie ma sensu sprawdzać każdej karty produktu z osobna - wszystkie potrzebne informacje są na stronach kategorii.
Paginacja kończy się na 4, ale wstawienie 4 na sztywno byłoby beznadziejnym pomysłem - struktura strony powinna zostać taka sama,
ale ilość produktów przecież może się zmienić.
Problemem jest to, że w paginacji jest przycisk "next", który dubluje jedną wartość data-page. Nie można po prostu sobie wyrzucić ostatniego elementu paginacji,
bo przycisk "next" nie jest widoczny na ostatniej stronie paginacji - jeśli np. w sklepie będzie nie więcej niż 15 produktów, to nie będzie przycisku "next",
i wyrzucając ostatni element paginacji wyrzucimy jej jedyny element - czyli pierwszą stronę.
Żeby uniknąć dwóch foreachów (jeden do stworzenia tablicy z unikalnymi wartościami 'data-page', drugi do znalezienia produktów z podstron o numerze 'data-page')
lub wstawienia if-a do foreacha (który by sprawdzał, czy innertext elementu nie jest równy "next"),
wyrzucimy ostatni element tylko jeśli liczba elementów jest większa niż 1.*/
$pagination_pages = $main_page->find('a[data-page]');
if(count($pagination_pages) > 1)
{
    unset($pagination_pages[array_key_last($pagination_pages)]);
}

foreach($pagination_pages as $cat_page_link)
{
    //pobieramy produkty z wszystkich podstron kategorii, zapisujemy je do tablicy
    $category_page = file_get_html(MAIN_PAGE_URL . 'index.php?page=' . $cat_page_link->attr['data-page']);
    $prod_list = getProductsFromPage($category_page);
    foreach($prod_list as $single_prod)
    {
        $products[] = getProductData($single_prod);
    }
}
//zapisujemy tablicę produktów do CSV i zapisujemy na dysku
arrToCSV($products, ['name','url','img_src', 'price', 'reviews', 'rating']);
?>
</body>
</html>