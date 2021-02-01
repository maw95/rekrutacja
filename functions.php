<?php
//przyjmujemy HTML strony kategorii i wyłuskujemy z niego pojedyncze kontenery z produktami
function getProductsFromPage($cat_page)
{
    $cat_prods = $cat_page->find('.card');
    return $cat_prods;
}

//przyjmujemy cały kontener (div) z miniaturką produktu, wyciągamy z niego potrzebne informacje i zwracamy je
function getProductData($single_prod)
{
    $product = [];
    $product['name'] = $single_prod->find('a[data-name]')[0]->attr['data-name'];
    $product['url'] = MAIN_PAGE_URL . $single_prod->find('a[data-name]')[0]->attr['href'];
    $product['img_src'] = $single_prod->find('.card-img-top')[0]->attr['src'];
    $product['price'] = $single_prod->find('h5')[0]->innertext;
    $product['reviews'] = getReviewsCount($single_prod->find('.card-footer small')[0]->innertext);
    $product['rating'] = getRating($single_prod->find('.card-footer small')[0]->innertext);
    return $product;
}

//wyciągamy liczbę ocen znajdując ciąg znaków między otwarciem a zamknięciem nawiasu i zwracamy go
function getReviewsCount($footer_str)
{
    $bracket_op_pos = strpos($footer_str, '(');
    $bracket_en_pos = strpos($footer_str, ')');
    $count = (int)substr($footer_str, $bracket_op_pos + 1, $bracket_en_pos - $bracket_op_pos - 1);
    return $count;
}

//wyciągamy średnią ocenę produktu zliczając entity czarnej gwiazdki w otrzymanym stringu
function getRating($footer_str)
{
    return substr_count($footer_str,'&#9733;' );
}

function arrToCSV($array, $column_names)
{
    //tworzymi plik CSV
    $output = fopen('products.csv','w');
    //dopisujemy do niego pierwszy wiersz (nazwy kolumn)
    fputcsv($output,$column_names);
    //wypełniamy plik wszystkimi rekordami z otrzymanej tablicy
    foreach($array as $row)
    {
        fputcsv($output,$row);
    }
    fclose($output);
    //zapisujemy plik na serwerze
    file_put_contents('products.csv',$output);
    //wyświetlamy komunikat o powodzeniu zapisu
    echo '<div>
            <a class="go-back" onclick="window.history.back();">←Wróć</a>
            <h3>Poszło!</h3>
            <p>Pobrano ' . count($array) . ' produktów do pliku products.csv</p>
          </div>';
}
?>