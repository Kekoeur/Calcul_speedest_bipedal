<?php

function read($csv){
    $file = fopen($csv, 'r');
    if($file){
        while (!feof($file) ) {
            $line[] = fgetcsv($file, 1024);
        }
        fclose($file);
        return $line;
    } else {
        echo "Fichier non trouvé";
    }
}


$dataset1 = 'dataset1.csv';
$dataset1 = read($dataset1);
$dataset2 = 'dataset2.csv';
$dataset2 = read($dataset2);

$dataset = $dataset1;
$id_dataset1 = -1;
$id_dataset2 = -1;

for($i = 0; $i<sizeof($dataset2[0]); $i++){
    for($j = 0; $j<sizeof($dataset1[0]); $j++){
        if($dataset1[0][$j] == $dataset2[0][$i]){
            $id_dataset2 = $i;
            $id_dataset1 = $j;
        }
    }
    if($i != $id_dataset2){
        array_push($dataset[0], $dataset2[0][$i]);
    }
}
for($i =1; $i<sizeof($dataset2); $i++){
    $x = -1;
    for($j = 1; $j<sizeof($dataset1); $j++){
        if($dataset1[$j][$id_dataset1] == $dataset2[$i][$id_dataset2]){
            $x = $j;
        }
    }
    if($x ==-1){
        $x = sizeof($dataset);
        array_push($dataset, [$dataset2[$i][$id_dataset2],'' ,'']);
    }
    for($k= 0; $k<sizeof($dataset2[$i]); $k++){
        if($k != $id_dataset2){
            array_push($dataset[$x], $dataset2[$i][$k]);
        }
    }
}
for($i =0; $i<sizeof($dataset[0]); $i++){
    if($dataset[0][$i] == 'LEG_LENGTH'){
        $id_leg_length = $i;
    }
    if($dataset[0][$i] == 'STRIDE_LENGTH'){
        $id_stride_length = $i;
    }
    if($dataset[0][$i] == 'STANCE'){
        $id_stance = $i;
    }
    if($dataset[0][$i] == 'NAME'){
        $id_name= $i;
    }
}

$g = 9.8; //(en m/s²)
$speed_bipedal = [];
for($i =1; $i<sizeof($dataset); $i++){
    if(sizeof($dataset[$i]) > $id_stride_length){
        $stride_length = $dataset[$i][$id_stride_length] ? $dataset[$i][$id_stride_length] : 0;
        $leg_length = $dataset[$i][$id_leg_length] ? $dataset[$i][$id_leg_length] : 0;

        if($stride_length != 0 && $leg_length != 0){
            $speed = (($stride_length / $leg_length) - 1) * sqrt($leg_length * $g);
            if($dataset[$i][$id_stance] == 'bipedal'){
                $speed_bipedal[$dataset[$i][$id_name]] = $speed;
            }
        } else {
            $speed = "Calcul impossible";
        }
    }
    array_push($dataset[0], 'SPEED');
    array_push($dataset[$i], $speed);
}

arsort($speed_bipedal);
foreach(array_keys($speed_bipedal) as $key){
    echo $key.'<br/>';
}
?>