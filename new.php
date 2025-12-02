<?php
echo "Hello World";
$txt="VIT";
$x=5;
$y=4;
echo $x+$y;
$b=35;
settype($b,"string");
echo(int)12.5;
if (5 > 3) {
  echo "Have a good day! <br>";
}
$favcolor = "red";

switch ($favcolor) {
  case "red":
    echo "Your favorite color is red!";
    break;
  case "blue":
    echo "Your favorite color is blue!";
    break;
  case "green":
    echo "Your favorite color is green!";
    break;
  default:
    echo "Your favorite color is neither red, blue, nor green!";
}
$i = 1;
while ($i < 6) {
  echo "$i <br>";
  $i++;
}
$colors = array("red", "green", "blue", "yellow");

foreach ($colors as $x) {
  echo "$x <br>";
}
$i = 1;

do {
  echo $i;
  $i++;
} while ($i < 6);
$cars = array("Volvo", "BMW", "Toyota");
?>