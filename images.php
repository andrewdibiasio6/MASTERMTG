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
    $sql = "SELECT image FROM `mtg_cards` WHERE `name_simple` = '" . $card_sql_str . "';" ;

    if ( ! $result = $db->query( $sql ) ) {
      die( '<p>There was an error running card query [' . $db->error . ']</p>\n' ) ;
    }

    $card_info = $result->fetch_assoc() ;
    if ($card_info['image'] == '') {
      echo "<img src='images/mtg_card_hq.jpg' height=310 width=220>" ;
    }
    else {
      echo "<img src=" ;
      echo '"' ;
      echo $card_info['image'] ;
      echo '"' ;
      echo " height=310 width=220 />" ;
    }
  }
  else {
    echo "<img src='images/mtg_card_hq.jpg' height=310 width=220>" ;
  }
?>