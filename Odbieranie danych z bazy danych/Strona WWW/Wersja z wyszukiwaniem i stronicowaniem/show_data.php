<?php
    session_start();
    if(isset($_POST['date']))
    {
        $_SESSION['search'] = date('Y-m-d', strtotime($_POST['date']));
    }
    if(isset($_POST['reset']))
    {
        session_destroy();
        header("Location: show_data.php");
    }
?>
<!DOCTYPE html>
<html lang="pl-pl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Wyniki pomiarów temperatury, wilgotności i ciśnienia</title>
        <link rel="stylesheet" href="style_pomiary.css" />
    </head>
    <body>
        <div>
            <h1>Wyniki pomiarów temperatury, wilgotności i ciśnienia z wykorzystaniem platformy mikrokontrolerowej Raspberry Pi Pico WH oraz czujnika BME280:</h1>
        </div>
        <br/><br/>
        <div>
            <form action="show_data.php" method="POST">
                <label><b>WYSZUKAJ WYNIKI POMIARÓW PO DACIE: </b></label>
                <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>">
                <input type="submit" name="submit" class="button" value="WYSZUKAJ">
                <input type="submit" name="reset" class="button" value="RESETUJ WYSZUKIWANIE">
            </form>
        </div>
        <br/><br/>
        <?php
            //$_SESSION["search"] = '';
            /*if($_POST['date'] == true)
            {
                $date = ($_SESSION["search"] = date('Y-m-d', strtotime($_POST['date'])));
                $_POST['date'] = true;
            }*/
            
        
                function display_data($query_sql)
                {
                    include_once 'db.php';
                  
                    //obliczanie danych na potrzeby stronicowania (paginacji)
                    $cur_page = isset($_GET['page']) ? $_GET['page'] : 1;
                    $results_per_page = 25; //liczba wyników na stronę
                    $skip = (($cur_page - 1) * $results_per_page); //liczba pomijanych wierszy na potrzeby stronicowania

                    $query = $query_sql;
                    $answer = mysqli_query($conn, $query);

                    $total = mysqli_num_rows($answer); // liczba wierszy zapisana na potrzeby stronicowania
                    $num_pages = ceil($total / $results_per_page); //określenie liczby stron
                    $query .= " LIMIT $skip, $results_per_page"; //dopisujemy do wcześniejszego zapytania klauzule LIMIT

                    $response = mysqli_query($conn, $query);

                    if($response->num_rows > 0)
                    {
                        echo "<table>";
                        echo "<thead>";
                        echo "<tr>";
                        echo "<th>ID</th>";
                        echo "<th>DATA POMIARU</th>";
                        echo "<th>GODZINA POMIARU</th>";
                        echo "<th>TEMPERATURA [°C]</th>";
                        echo "<th>WILGOTNOŚĆ [%]</th>";
                        echo "<th>CIŚNIENIE ATMOSFERYCZNE [hPa]</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";

                        while($row = $response->fetch_assoc())
                        {   
                            echo "<tr>";

                            echo "<td>" . $row["ID"]            . "</td>";
                            echo "<td>" . $row["date"]          . "</td>";
                            echo "<td>" . $row["time"]          . "</td>";
                            echo "<td>" . $row["temperature"]   . "</td>";
                            echo "<td>" . $row["humidity"]      . "</td>";
                            echo "<td>" . $row["pressure"]      . "</td>";

                            echo "</tr>";
                        }
                        echo "</tbody>";
                        echo "</table>";

                        function generate_page_links($cur_page, $num_pages)
                        {   
                            $page_links = '';

                            //odnośnik do poprzedniej strony (-1)
                            if($cur_page > 1)
                            {
                                $page_links .= '<a href="' . $_SERVER['PHP_SELF'] . '?page=' . ($cur_page - 1) . '">«</a>';
                            }

                            $i = $cur_page - 4;
                            $page = $i + 8;

                            for($i; $i <= $page; $i++)
                            {
                                if($i > 0 && $i <= $num_pages)
                                {
                                    //jeżeli jesteśmy na danej stronie to nie wyświetlamy jej jako link
                                    if($cur_page == $i && $i !=0)
                                    {
                                        $page_links .= '<p>' . $i . '</p>';
                                    }else
                                    {
                                        //wyświetlamy odnośnik do 1 strony
                                        if($i == ($cur_page - 4) && ($cur_page - 5) != 0)
                                        {
                                            $page_links .= '<a href="' . $_SERVER['PHP_SELF'] . '?page=1">1</a>';
                                        }

                                        //wyświetlamy "kropki" jako odnośnik do poprzedniego bloku stron
                                        if($i == ($cur_page - 4) && (($cur_page - 6)) > 0) 
                                        { 
                                            $page_links .= ' <a href="' . $_SERVER['PHP_SELF'] . '?page=' . ($cur_page - 5) . '">...</a> '; 
                                        } 

                                        //wyświetlamy linki do bieżących stron
                                        $page_links .= ' <a href="' . $_SERVER['PHP_SELF'] . '?page=' . $i . '"> ' . $i . '</a> ';

                                        //wyświetlamy "kropki", jako odnośnik do następnego bloku stron
                                        if ($i == $page && (($cur_page + 4) - ($num_pages)) < -1) 
                                        { 
                                            $page_links .= ' <a href="' . $_SERVER['PHP_SELF'] . '?page=' . ($cur_page + 5) . '">...</a>'; 
                                        } 

                                        //wyświetlamy odnośnik do ostatniej strony
                                        if ($i == $page && ($cur_page + 4) != $num_pages) 
                                        { 
                                            $page_links .= ' <a href="' . $_SERVER['PHP_SELF'] . '?page=' . $num_pages . '">' . $num_pages . '</a> '; 
                                        }
                                    }
                                }
                            }

                            //odnośnik do następnej strony (+1)
                            if ($cur_page < $num_pages) 
                            {
                                $page_links .= '<a href="' . $_SERVER['PHP_SELF'] . '?page=' . ($cur_page + 1) . '">»</a>';
                            }

                            return $page_links;
                        }
                    }else
                    {
                        echo "Database is empty!";
                    }

                    //wyświetlanie nawigację przy stronnicowaniu
                    if ($num_pages > 1) 
                    {
                        echo "<div>";
                        echo "</br>";
                        echo generate_page_links($cur_page, $num_pages);
                        echo "</div>";
                    }
                    mysqli_close($conn);
                }

                if(!isset($_SESSION['search']))
                {
                    //echo $date;
                    //echo $_SESSION["search"];
                    $query_sql = "SELECT * FROM measurement";
                    //echo $query_sql;
                    echo display_data($query_sql);
                    
                }else if(isset($_SESSION['search']))
                {
                    //echo $_SESSION["search"];
                    //wyszukiwanie po dacie
                    //$date = date('Y-m-d', strtotime($_POST['date'])); 
                    //$query = "SELECT * FROM measurement WHERE date = " . "'" . $date . "'";
                    $date = $_SESSION['search'];
                    //$date = date('Y-m-d', strtotime($_POST['date']));
                    $query_sql = "SELECT * FROM measurement WHERE date = " . "'" . $date . "'";
                    //echo $query_sql;

                    echo display_data($query_sql); 
                    //session_unset();
                }
        ?>
    </body>
</html>










