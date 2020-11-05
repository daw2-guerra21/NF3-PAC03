<?php

$db = mysqli_connect('localhost', 'root', 'root') or
die ('Unable to connect. Check your connection parameters.');
mysqli_select_db($db, 'moviesite') or die(mysqli_error($db));

//insert new data into the reviews table
$query = <<<ENDSQL
INSERT INTO reviews
    (review_movie_id, review_date, reviewer_name, review_comment,
        review_rating)
VALUES 
    (2, "2017-11-13", "Yago García", "Técnicamente casi perfecta, 
    pero forzada y solemne en exceso: suspende el test de empatía (...)", 3.5),
        
    (2, "2017-12-15", "Desirée de Fez", "No es una película impecable 
    (algunas decisiones de guión, por ejemplo, son insólitamente caprichosas), 
    pero sí es una película extraordinaria (...)", 4),
    
    (3, "2019-06-23", "Johhn DeFore", "Parece una simple transición hacia 
    el anunciado y definitivo 'King King vs. Godzilla'. Una transición 
    atibulada en la que los personajes humanos apenas están definidos (...)", 2)
ENDSQL;
mysqli_query($db, $query) or die(mysqli_error($db));

echo '¡Tres nuevas reseñas añadidas!';

?>