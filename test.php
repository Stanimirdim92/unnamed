<?php

$currDate = date("d");

$day_in_future = abs((int) $currDate);


for ($i=$day_in_future; $i <$day_in_future+8 ; $i++) { 
    echo $i."<br />";
}

?>