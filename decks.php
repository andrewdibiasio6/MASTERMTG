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
Updated on March 25, 2015.

A simple html/php page that displays Magic the gathering decks.
-->
    <title>Decks</title>
    
    <!-- JQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="css/deck.css">    
  </head> 
  <body>
    <header>
      <img class="logo" src="images/logo.jpg" alt="Mastering Magic">
      <ul class='menu'>
        <li>Home</li>
        <li>Cards</li>
        <li>Decks</li>
      </ul>
    </header>

    <div id="content">
    <?php
      $mana_string = "";

      # Create a string by concatenating the passed variables.
      if(isset($_GET['black'])) {
        $manaString .= "black, ";
      }
      if(isset($_GET['blue'])) {
        $manaString .= "blue, ";
      }
      if(isset($_GET['green'])) {
        $manaString .= "green, ";
      }
      if(isset($_GET['red'])) {
        $manaString .= "red, ";
      }
      if(isset($_GET['white'])) {
        $manaString .= "white, ";
      }
      $manaString = substr($manaString, 0, -2);

      # Database credentials.
      $username = "mmammoss";
      $password = "mm5119";

      # Connection code from:
      # http://php.net/manual/en/mysqli-stmt.bind-param.php
      $mysqli = new mysqli("localhost", $username, $password, $username);

      # Check connection
      if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
      }

      # Prepare and execute statement with bind variables.
      $stmt = $mysqli->prepare("SELECT deck, format, colors FROM `mtg_deck_map` WHERE `colors` = ?");
      $stmt->bind_param('s', $manaString);
      $stmt->execute();
      $stmt->bind_result($deck, $format, $colors);
      
      # Initialize arrays to hold deck info.
      $decks_array = array();
      $formats_array = array();
      $colors_array = array();

      # Fetch results and store them in arrays.
      while($stmt->fetch()) {
        array_push($decks_array, $deck);
        array_push($formats_array, $format);
        array_push($colors_array, $colors);
      }

      # Close statement to prevent issues with following queries.
      $stmt->close();
 
      # Create a list of for the deck select form.
      $deck_list = "";
      $count = 1;
      foreach ($decks_array as &$deck_select) {
        if (strlen($deck_select) > 25) {
          $select_str = strtolower(substr($deck_select, 0, 25));
        }
        $deck_list .= "              <option value='" . $count . "' label='" . $select_str . "'>" . $deck_select . "</option>\n";
        $count += 1;
      }

      # Create the .deck divisions nav bar and header.
      echo "      <div class='deck'>\n";
      echo "        <nav id='deck_nav'>\n";
      echo "          <div id='nav_header'>\n";
      echo "            <h3 id='deck_name'></h3>\n";
      echo "          </div> <!-- .navbar-header -->\n";
      echo "          <div id='deck_select'>\n";
      echo "            <select id='deck_dropdown' class='form-control'>\n";
      echo $deck_list;
      echo "            </select>\n";
      echo "          </div> <!-- #deck_select -->\n";
      echo "        </nav> <!-- #deck_nav -->\n";
      echo "        <div id='deck_info'>\n";

      # For each deck retrieve card names and counts.
      $count = 1;
      foreach ($decks_array as &$deck) {
        # Query deck database for card and counts.
        $sql = "Select * from `mtg_deck` where `deck` = '" . $deck . "';";
        $result = $mysqli->query( $sql );

        # Create a separate division for each deck.
        echo "          <div id='" . $count . "' class='card_list split_list'>\n";
        echo "            <ul>\n";

        # Update the list of cards with names and counts.
        while ($deck_info = $result->fetch_assoc()) {
          echo "              <li>" . $deck_info['count'] . " - "; 
          echo '<a class="card_link" href="cards.php?search=';
          echo $deck_info['card'] ;
          echo '"';
          echo ">" . $deck_info['card'] . "</a></li>\n";
        }

        # Insert a default card image next to the list of cards.
        echo "            </ul>\n";
        echo "          </div> <!-- #card_list -->\n";
        $count += 1;
      }

      # Close sql connection so that the server does not get overloaded.
      $mysqli->close();
    ?>
          <!-- Retrieve format and color information from php -->
          <script>
              $formats = <?php echo json_encode($formats_array); ?>;
              $colors = <?php echo json_encode($colors_array); ?>;
          </script>

          <!-- Division to hold the card image -->
          <div id="card_img">
            <img src='images/mtg_card_hq.jpg' height=310 width=220>
          </div> <!-- #card_img -->
        </div> <!-- #deck_info -->
        <div id='deck_format'></div>
      </div> <!-- #deck -->
    </div> <!-- #content -->
    <script src="js/deck.js"></script>
  </body>
</html>

