function img_error(image){
  image.onerror = "";
  image.src = "images/mtg_card_hq.jpg";
  return true;
}

$(function($) {
  var num_cols = 1;
  container = $('.split_list');
  container.each(function() {
    var items_per_col = new Array();
    items = $(this).find('li');
    if (items.length > 15) {
      num_cols = Math.ceil(items.length / 15);
      if (num_cols > 2) {
        num_cols = 2;
        min_items_per_col = Math.floor(items.length / num_cols);
        difference = items.length - (min_items_per_col * num_cols);
        for (var i = 0; i < num_cols; i++) {
          if (i < difference) {
            items_per_col[i] = min_items_per_col + 1;
          } else {
            items_per_col[i] = min_items_per_col;
          }
        }
        $(this).empty();
        for (var i = 0; i < num_cols; i++) {
          if (i == 0) {
            $(this).append($('<ul ></ul>').addClass('pull-left').attr('id', 'first_list'));
          }
          else {
            $(this).append($('<ul ></ul>').addClass('pull-left'));
          }
          for (var j = 0; j < items_per_col[i]; j++) {
            var pointer = 0;
            for (var k = 0; k < i; k++) {
              pointer += items_per_col[k];
            }
            $(this).find('.' + 'pull-left').last().append(items[j + pointer]);
          }
        }
      }
      else {
        var count = items.length
        for (var i = 0; i < num_cols; i++) {
          if (count > 15) {
            items_per_col[i] = 15;
          }
          else {
            items_per_col[i] = count;
          }
        }
        $(this).empty();
        for (var i = 0; i < num_cols; i++) {
          if (i == 0) {
            $(this).append($('<ul ></ul>').addClass('pull-left').attr('id', 'first_list'));
          }
          else {
            $(this).append($('<ul ></ul>').addClass('pull-left'));
          }
          for (var j = 0; j < items_per_col[i]; j++) {
            var pointer = 0;
            for (var k = 0; k < i; k++) {
              pointer += items_per_col[k];
            }
            $(this).find('.' + 'pull-left').last().append(items[j + pointer]);
          }
        }
      }
    }
  });
});


$(document).ready(function () {
  $deck_info = $('#deck_info');
  $first_list = $('#first_list');
  $deck_dropdown = $('#deck_dropdown');
  $deck_name = $('#deck_name');
  $deck_format = $('#deck_format');
  $deck_selected = 1 ;
          
  // When a card name is hovered over change the default image to the the card image.
  $('.card_link').hover( function () {
    $card_lower = $(this).text().toLowerCase().replace(/\s/g,"%20");
    console.log("http://weblab.cs.uml.edu/~mmammoss/beta/images.php?search=" + $card_lower) ;
    $('#card_img').load("http://weblab.cs.uml.edu/~mmammoss/beta/images.php?search=" + $card_lower) ; 
  });
 
  // Display a different deck when select box is changed.
  $('#' + $deck_selected).toggle();
  $deck_format.html("<h2>Format: </h2><h2><small>" + $formats[$deck_selected] + "</small><h2>") ;
  $deck_dropdown.change(function() {
    // Toggle the current list off and switch to the selected list.
    $('#' + $deck_selected).toggle();
    $deck_name.html($("#deck_dropdown option:selected").text());
    $deck_selected = $("#deck_dropdown").val();
    $('#' + $deck_selected).toggle();
    $deck_format.html("<h4 style='float: left;'>Format: <i>" + $formats[$deck_selected-1] + "</i></h4><h4 style='float: right;'>Color(s): <i>" + $colors[$deck_selected-1] + "</i></h4>") ;
  }).change();    
}); 