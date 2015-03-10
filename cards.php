<html ng-app="website">
  <head>
    <meta charset="utf-8">
    <title>Mastering Magic</title>
<!-- 
Name: Gregory James Caldwell Jr.
Email: Gregory_Caldwell@student.uml.edu
File: Card.html
Starting Date: Thursday February 12th, 2014
Last Date: Thursday February 12th, 2014
Course: 91.462 - GUI Programming II
-->
    <!-- Useful Resource http://jsfiddle.net/ -->
    <!-- Useful Resource http://mtgjson.com/ -->

    <!-- --------------------Content Delivery Network Scripts-------------------- --> 
    <!-- JQuery -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.js"></script>

    <!-- -------------------- Stylesheets -------------------- --> 
    <!-- Bootstrap styles -->
    <link rel="stylesheet" href="css/bootstrap.css">

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="css/styles.css">

    <!-- Card.html Stylesheet -->
    <link rel="stylesheet" href="css/card.css">

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

    <div id="mainContainer">
      <?php

        # Card to look up.
        $card = $_GET["search"] ;

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

        if (isset($card)) {

          /*
           * Fetch the row count from `mtg_cards`.
           */
          $card_sql_str = str_replace("'", "\\'", $card) ;
          $sql = "SELECT * FROM `mtg_cards` WHERE `name` = '" . $card_sql_str . "';" ;

          if ( ! $result = $db->query( $sql ) ) {
            die( '<p>There was an error running card query [' . $db->error . ']</p>\n' ) ;
          }

          $card_info = $result->fetch_assoc() ;
          echo "<!-- Name of the Card -->\n<h1 id='cardName'>" . $card_info['name'] . "</h1>\n\n" ;
          echo "<!-- Image of the Card -->\n<div id='cardImage'>\n<img src=" ;
          echo '"' ;
          echo $card_info['image'] ;
          echo '"' ;
          echo " height=310 width=220 />\n</div>\n\n" ;
          echo "<!-- Data on the Card -->\n<div id='cardData'>\n" ;
          echo "<h2>Color: " . $card_info['colors'] . "</h2>\n" ;
          echo "<h2>Type: " . $card_info['type'] . "</h2>\n" ;
          echo "<h2>Mana Cost: " . $card_info['mana_cost'] . "</h2>\n" ;
          echo "<h2>Text: " . $card_info['text'] . "</h2>\n" ;
          echo "<h2>Power: " . $card_info['power'] . "</h2>\n" ;
          echo "<h2>Toughness: " . $card_info['toughness'] . "</h2>\n" ;
          echo "</div>" ;
          echo "<!-- Percentages on the Card -->\n<div id='cardPercentages'>\n" ;
          echo "<!-- Standard Percentages on the Card -->\n<div id='standardPercentage'>\n" ;
          echo "</div>" ;
          echo "<!-- Modern Percentages on the Card -->\n<div id='modernPercentage'>\n" ;
          echo "</div>" ;
          echo "<!-- Legacy Percentages on the Card -->\n<div id='legacyPercentage'>\n" ;
          echo "</div>" ;
          echo "<!-- Vintate Percentages on the Card -->\n<div id='vintagePercentage'>\n" ;
          echo "</div>" ;
          echo "</div>" ;

        }
      ?>
    </div>
  </body>
</html>