<?php


//tworzymy tablicę na produkty
$products = [];
//otwieramy plik CSV z produktami
$lines = file('products.csv');
foreach($lines as $key=>$value)
{
    //każdy wiersz z CSV przypisujemy do osobnego indeksu tablicy
    $products[$key] = str_getcsv($value);
}
?>
<html>
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div>
    <a class="go-back btn btn-secondary" onclick="window.history.back();">←Wróć</a>
    <table>
        <thead>
        <?php foreach($products[0] as $heading)
        {
            echo '<th>' . $heading . '</th>';
        }
        ?>
        </thead>
        <tbody>

        <?php
        //uzupełniamy tabelę informacjami o produktach
        for($i = 1; $i<count($products);$i++)
        {
            echo '<tr>';
            foreach($products[$i] as $cell)
            {
                echo '<td data-link="' . $products[$i][1] . '">' . $cell . '</td>';
            }
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>
</div>
<!--przygotowujemy modal, który będzie uzupełniany danymi produktu (i ew. jego wariantów) po kliknięciu na pozycję z tabeli-->
<div id="prod_modal" class="modal fade " tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img class="img-fluid" src="#"/>
                <p>Cena: <span id="prod_price"></span></p>
                <p class="old-price-p" style="display:none">Stara cena: <span id="old_price"></span></p>
                <p>Kod: <span id="prod_code"></span></p>
                <p>Liczba ocen: <span id="prod_reviews"></span></p>
                <p>Średnia ocena: <span id="prod_rating">☆☆☆☆☆</span></p>
                <div id="variants" style="display:none">
                    <h3>Warianty:</h3>
                </div>
            </div>
        </div>
    </div>
</div>
<script
    src="https://code.jquery.com/jquery-3.5.1.min.js"
    integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
    crossorigin="anonymous">
</script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
<script>
    //zapisujemy elementy z modalu do zmiennych, aby nie zmuszać jQuery do skanowania DOM po każdym kliknięciu na wiersz z tabeli
    var prod_modal = $('#prod_modal');
    var prod_title = $('#prod_modal .modal-title');
    var prod_img = $('#prod_modal img');
    var prod_price = $('#prod_modal #prod_price');
    var prod_old_price = $('#prod_modal #old_price');
    var prod_old_price_p = $('#prod_modal .old-price-p');
    var prod_code =  $('#prod_modal #prod_code');
    var prod_reviews = $('#prod_modal #prod_reviews');
    var prod_rating = $('#prod_modal #prod_rating');
    var prod_variants = $('#variants');
    /*po kliknięciu wiersza z tabeli pobieramy ajax-em dane o produkcie,
     przekazując jako parametr URL karty produktu najwygodniejszy sposób na obejście CORS to stworzenie pomocniczego pliku, tutaj pod nazwą 'cors_proxy.php'*/
    $('td').click(function()
    {
       var ajax_url = $(this).attr('data-link');
       $.ajax(
       {
           url: './cors_proxy.php?product_url=' + ajax_url
       }).done(function(data)
       {
           //parsujemy dane otrzymane z cors_proxy.php
           var product = $.parseJSON(data);
           //wypełniamy elementy modalu treścią z pobranych danych
           prod_title.html(product.name);
           prod_img.attr('src',product.img_url);
           prod_price.html(product.price);
           if(product.old_price)
           {
               prod_old_price.html(product.old_price);
               prod_old_price_p.css('display','block');
           }
           else
           {
               prod_old_price_p.css('display','none');
           }
           prod_code.html(product.code);
           prod_reviews.html(product.reviews);
           prod_rating.html('☆☆☆☆☆');
           $(product.rating).each(function()
           {
               prod_rating.prepend('★');
               prod_rating.html(prod_rating.html().slice(0,-1));
           });
           prod_variants.html('<h3>Warianty:</h3>');
           if(product.variants)
           {
               prod_variants.css('display','block');
               $(product.variants).each(function()
               {
                   prod_variants.append('<p>' + $(this)[0].name + '</p>');
                   prod_variants.append('<p>Cena: ' + $(this)[0].price + '</p>');
                   if($(this)[0].old_price)
                   {
                       prod_variants.append('<p>Stara cena: ' + $(this)[0].old_price + '</p>')
                   }
               });
           }
           else
           {
               prod_variants.css('display','none');
           }
           //włączamy widoczność modalu
           prod_modal.modal('show');
       });
    });
</script>
</body>
</html>
