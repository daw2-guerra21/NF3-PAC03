<?php
// función para obtener el promedio de notas de la película
function obtener_promedio($review_movie_id) {
    global $db;
    $query = 'SELECT avg(review_rating) as AVGRATE FROM reviews WHERE review_movie_id= ' . $_GET['movie_id'];
    $result = mysqli_query($db, $query) or die(mysqli_error($db));
    $row = mysqli_fetch_array($result);
    return round($row['AVGRATE'],1) . ' / 5';
}

// function to generate ratings
function generate_ratings($rating) {
    $movie_rating = '';
    for ($i = 0; $i < $rating; $i++) {
        $movie_rating .= '<img src="star.png" width="20" height="20" alt="star"/>';
    }
    return $movie_rating;
}

// take in the id of a director and return his/her full name
function get_director($director_id) {

    global $db;

    $query = 'SELECT 
            people_fullname 
       FROM
           people
       WHERE
           people_id = ' . $director_id;
    $result = mysqli_query($db, $query) or die(mysqli_error($db));

    $row = mysqli_fetch_assoc($result);
    extract($row);

    return $people_fullname;
}

// take in the id of a lead actor and return his/her full name
function get_leadactor($leadactor_id) {

    global $db;

    $query = 'SELECT
            people_fullname
        FROM
            people 
        WHERE
            people_id = ' . $leadactor_id;
    $result = mysqli_query($db, $query) or die(mysqli_error($db));

    $row = mysqli_fetch_assoc($result);
    extract($row);

    return $people_fullname;
}

// take in the id of a movie type and return the meaningful textual
// description
function get_movietype($type_id) {

    global $db;

    $query = 'SELECT 
            movietype_label
       FROM
           movietype
       WHERE
           movietype_id = ' . $type_id;
    $result = mysqli_query($db, $query) or die(mysqli_error($db));

    $row = mysqli_fetch_assoc($result);
    extract($row);

    return $movietype_label;
}

// function to calculate if a movie made a profit, loss or just broke even
function calculate_differences($takings, $cost) {

    $difference = $takings - $cost;

    if ($difference < 0) {     
        $color = 'red';
        $difference = '$' . abs($difference) . ' million';
    } elseif ($difference > 0) {
        $color ='green';
        $difference = '$' . $difference . ' million';
    } else {
        $color = 'blue';
        $difference = 'broke even';
    }

    return '<span style="color:' . $color . ';">' . $difference . '</span>';
}

//connect to MySQL
$db = mysqli_connect('localhost', 'root', 'root') or
    die ('Unable to connect. Check your connection parameters.');

// make sure you're using the right database
mysqli_select_db($db, 'moviesite') or die(mysqli_error($db));

// retrieve information
$query = 'SELECT
        movie_name, movie_year, movie_director, movie_leadactor,
        movie_type, movie_running_time, movie_cost, movie_takings
    FROM
        movie
    WHERE
        movie_id = ' . $_GET['movie_id'];
$result = mysqli_query($db, $query) or die(mysqli_error($db));

$row = mysqli_fetch_assoc($result);
$movie_name         = $row['movie_name'];
$movie_director     = get_director($row['movie_director']);
$movie_leadactor    = get_leadactor($row['movie_leadactor']);
$movie_year         = $row['movie_year'];
$movie_running_time = $row['movie_running_time'] .' mins';
$movie_takings      = $row['movie_takings'] . ' million';
$movie_cost         = $row['movie_cost'] . ' million';
$movie_health       = calculate_differences($row['movie_takings'],
                          $row['movie_cost']);

// display the information
$promedio_notas = obtener_promedio($_GET['movie_id']);

echo <<<ENDHTML
<html>
 <head>
  <title>Details and Reviews for: $movie_name</title>
 </head>
 <body>
  <div style="text-align: center;">
   <h2>$movie_name</h2>
   <h3><em>Details</em></h3>
   <table cellpadding="2" cellspacing="2"
    style="width: 70%; margin-left: auto; margin-right: auto;">
    <tr>
     <td><strong>Title</strong></strong></td>
     <td>$movie_name</td>
     <td><strong>Release Year</strong></strong></td>
     <td>$movie_year</td>
    </tr><tr>
     <td><strong>Movie Director</strong></td>
     <td>$movie_director</td>
     <td><strong>Cost</strong></td>
     <td>$$movie_cost<td/>
    </tr><tr>
     <td><strong>Lead Actor</strong></td>
     <td>$movie_leadactor</td>
     <td><strong>Takings</strong></td>
     <td>$$movie_takings<td/>
    </tr><tr>
     <td><strong>Running Time</strong></td>
     <td>$movie_running_time</td>
     <td><strong>Health</strong></td>
     <td>$movie_health<td/>
    </tr><tr>
     <! -- y mostrar promedio de notas -->
     <td><strong>Average Rating</strong></td>
     <td>$promedio_notas</td>
    </tr>
   </table>
ENDHTML;

// definiendo order y sort
if(isset($_GET['order'])){
    $order = $_GET['order'];
}else{
    $order = 'review_date';
}

if(isset($_GET['sort'])){
    $sort = $_GET['sort'];
}else{
    $sort = 'ASC';
}

// retrieve reviews for this movie
$query = "SELECT
        review_movie_id, review_date, reviewer_name, review_comment,
        review_rating
    FROM
        reviews
    WHERE
        review_movie_id = " . $_GET['movie_id'] . "
    ORDER BY
        $order " . $sort;

$result = mysqli_query($db, $query) or die(mysqli_error($db));

// cambiar de DESC a ASC y viceversa
if($sort == 'DESC'){
    $sort = 'ASC';
}else{
    $sort = 'DESC';
}

// display the reviews
echo <<< ENDHTML
   <h3><em>Reviews</em></h3>
   <table cellpadding="2" cellspacing="2"
    style="width: 90%; margin-left: auto; margin-right: auto;">
    <a>
     <th  style="..."><a href="N3P308details.php?movie_id=$_GET[movie_id]&order=review_date&sort=$sort">Date</a></th>
     <th style="...;"><a href="N3P308details.php?movie_id=$_GET[movie_id]&order=reviewer_name&sort=$sort">Reviewer</a></th>
     <th style="..."><a href="N3P308details.php?movie_id=$_GET[movie_id]&order=review_comment&sort=$sort">Comments</a></th>
     <th style="..."><a href="N3P308details.php?movie_id=$_GET[movie_id]&order=review_rating&sort=$sort">Rating</a></th>
    </tr>
ENDHTML;

// trabajamos el color de las filas de la tabla
$cont_color = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $date = $row['review_date'];
    $name = $row['reviewer_name'];
    $comment = $row['review_comment'];
    $rating = generate_ratings($row['review_rating']);

    $es_par = $cont_color%2;
    if($es_par==0){
        $color = '#ADD8E6';
    }else{
        $color = '#B19CD9';
    }
    $cont_color++;

    echo <<<ENDHTML
      <td style="...; background-color:$color">$date</td>
      <td style="...; background-color:$color">$name</td>
      <td style="...; background-color:$color" >$comment</td>
      <td style="...; background-color:$color">$rating</td>
    </tr>
ENDHTML;
}

echo <<<ENDHTML
  </div>
 </body>
</html>
ENDHTML;
?>
