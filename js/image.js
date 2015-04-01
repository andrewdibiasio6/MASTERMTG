function findIndex(values, target) {
  return binarySearch(values, target, 0, values.length - 1);
};

function binarySearch(values, target, start, end) {
  if (start > end) { return -1; } //does not exist

  var middle = Math.floor((start + end) / 2);
  var value = values[middle];

  if (value > target) { return binarySearch(values, target, start, middle-1); }
  if (value < target) { return binarySearch(values, target, middle+1, end); }
  return middle;
}

$image = $('#image');

$.getJSON( "http://weblab.cs.uml.edu/~mmammoss/beta/backend/data/image_map.json", function( json ) {
  console.log( "JSON Data: " + json[0][0]);
  console.log(findIndex(json, $request));
});
