<!DOCTYPE html>
<html ng-app="website"> 
  <head>
    <meta charset="UTF-8">
<!--
File: deck.php
91.462 Project: Mastering Magic
Michael Mammosser, Computer Science Major @ UMass Lowell
Contact: michael_mammosser@student.uml.edu
Copyright (c) 2015 by Michael Mammosser.  All rights reserved.
Updated on March 10, 2015.

A simple html/php page that displays mtg decks.
-->
    <title>MTG_CARDS Update</title>
    
    <!-- JQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

    <!-- Bootstrap styles -->
    <link rel="stylesheet" href="css/bootstrap.css">

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="css/styles.css">

    <!-- Card.html Stylesheet -->
    <link rel="stylesheet" href="css/deck.css">

    <!-- Central Navigation Bar Stylesheet -->
    <link rel="stylesheet" href="css/nav.css">

    <!-- Central Search Bar Stylesheet -->
    <link rel="stylesheet" href="css/search.css">

    <!-- Changes the small icon on the page to the image referenced. -->
    <link rel="icon" href="images/mtg_card.jpg">
  </head>
  <body ng-controller="MainCtrl">
    <!-- Header -->
    <header>

      <!-- Header Logo Image -->
      <img id="headerImage" src="images/mtg_card_hq.jpg" alt="Back of a MTG Card" >

      <!-- Header Title -->
      <h3 id="headerTitle"> Mastering MTG </h3> 

      <form action="" method="get" id="searchBar" >
        <!-- This search box allows the user to search for specific people in the table. -->
        <input type="search" name="search" id="search" placeholder="Enter a card..." >
      </form>
      <div id="navbar"> 
        <ul class="fancyNav">
          <li id="Home">
            <p><a href="./index.html"> Home </a></p>
          </li>
          <li id="Decks">
            <p><a href="./decks.php"> Decks </a></p>
          </li>
          <li id="Card_Search">
            <p><a href="./cards.php"> Card Search </a></p>
          </li>
          <li id="Contact">
            <p><a href="#"> Contact </a></p>
          </li>
        </ul>
      </div> 
    </header>

    <?php
      $manaString = "";

      if(isset($_GET['black'])) {
        $manaString .= $_GET['black']  . ", ";
      }

      if(isset($_GET['blue'])) {
        $manaString .= $_GET['blue']  . ", ";
      }

      if(isset($_GET['green'])) {
        $manaString .= $_GET['green']  . ", ";
      }

      if(isset($_GET['red'])) {
        $manaString .= $_GET['Red'] . ", ";
      }

      if(isset($_GET['white'])) {
        $manaString .= $_GET['white']  . ", ";
      }

      $manaString = substr ( $manaString , 0, -2 );

      # Database credentials.
      $username = "mmammoss" ;
      $password = "mm5119" ;

      /*
       * Error report configuration.
       * Code from https://www.teaching.cs.uml.edu/~heines/91.462/91.462-2014-15s/462-lecs/code/showphpsource.php?file=connect-v4i.php&numberlines
       */
      error_reporting( E_STRICT ) ;
      function terminate_missing_variabless( $errno, $errstr, $errfile, $errline ) {
        if (( $errno == E_NOTICE ) and ( strstr( $errstr, "Undefined variable" ) ) ) {
          die ( "$errstr in $errfile line $errline" ) ;
        }
        return false;
      }
      $old_error_handler = set_error_handler( "terminate_missing_variables" ) ;

      /*
       * Create connection with mysqli to the weblab mysql server.
       * Code from https://www.teaching.cs.uml.edu/~heines/91.462/91.462-2014-15s/462-lecs/code/showphpsource.php?file=connect-v4i.php&numberlines
       */
      $db = new mysqli("localhost", $username, $password, $username) ;
      if ( $db->connect_errno > 0 ) {
        die( '<p>Unable to connect to database [' . $db->connect_error . ']</p>\n' ) ;
      }

      /*
       * Fetch the row count from `mtg_cards`.
       */
      $sql = "SELECT * FROM `mtg_deck_map` WHERE `colors` = '" . $manaString . "';" ;
      if ( ! $result = $db->query( $sql ) ) {
        die( '<p>There was an error running card query [' . $db->error . ']</p>\n' ) ;
      }
      $decks = array() ;
      $count = 1 ;
      while ($deck_info = $result->fetch_assoc()) {
         array_push($decks, $deck_info['deck']) ;
         if ($count > 5) {
           break ;
         }
         $count += 1 ;
      }

      foreach ($decks as &$deck) {
        $sql = "Select * from `mtg_deck` where `deck` = '" . $deck . "';" ;
        $result = $db->query( $sql ) ;			
        echo "<div class='deck'>" ;
        echo "<h2>" . $deck . "</h2>\n" ;
        echo "<div class='cards'><ul>\n" ;
        while ($deck_info = $result->fetch_assoc()) {
          echo "<li>" . $deck_info['count'] . " - " ; 
          echo '<a class="card_link" href="cards.php?search=' ;
          echo $deck_info['card'] ;
          echo '"' ;
          echo ">" . $deck_info['card'] . "</a></li>\n" ;
        }
        echo "</ul></div>\n" ;
        echo "<div class='card_image'><img src='images/mtg_card_hq.jpg' height=310 width=220></div>" ;
        echo "</div>\n" ;
      }
    ?>
    <script>
      $('.card_link').hover( function () {
        console.log() ;
        $image_str = "<img src=" + '"http://mtgimage.com/card/' + $(this).text() + '.jpg"' + " height=310 width=220>" ;
        $('.card_image').html($image_str) ; 
      });
    </script>
  </body>
</html>