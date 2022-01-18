<?php

function read($csv){ // Fonction de lecture du fichier CSV
    $file = fopen($csv, 'r'); // on essaye d'ouvrir le fichier CSV
    if($file){//si il s'ouvre bien
        while (!feof($file) ) {// Tant qu'on atteint pas la fin du fichier
            $line[] = fgetcsv($file, 1024); // on récupère la ligne dans un tableau
        }
        fclose($file);// on ferme le fichier
        return $line;// on retourne le tableau des lignes
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

for($i = 0; $i<sizeof($dataset2[0]); $i++){//Pour chaque valeur du premier élément du tableau dataset2 -> correspond au titre des colonnes en CSV
    for($j = 0; $j<sizeof($dataset1[0]); $j++){//Pour chaque valeur du premier élément du tableau dataset1 -> correspond au titre des colonnes en CSV
        if($dataset1[0][$j] == $dataset2[0][$i]){// si les 2 valeur sont équivalents 
            $id_dataset2 = $i;// on récupère l'id pour savoir sur quel élément lié les tableaux -< ici c'est le NAME
            $id_dataset1 = $j;
        }
    }
    if($i != $id_dataset2){
        array_push($dataset[0], $dataset2[0][$i]); // si la valeur parcourru correspond pas a l'id on l'ajoute dans le premier élément du tableau global -> permet de rajouter les titres pas présent dans dataset1 mais présent dans dataset2
    }
}
for($i =1; $i<sizeof($dataset2); $i++){
    $x = -1;
    for($j = 1; $j<sizeof($dataset1); $j++){
        if($dataset1[$j][$id_dataset1] == $dataset2[$i][$id_dataset2]){// on verifie si le NAME d'un élément du tableau dataset1 correspond au NAME d'un élément du tableau dataset2
            $x = $j;
        }
    }
    if($x ==-1){// si aucune correspondance on ajoute un élément dans dataset -> gere si il y a des nouveaux dinosaure dans dataset2
        $x = sizeof($dataset);
        array_push($dataset, [$dataset2[$i][$id_dataset2],'' ,'']);// nouvelle élément ajouté avec le NAME de dataset2 et le reste vide
    }
    for($k= 0; $k<sizeof($dataset2[$i]); $k++){
        if($k != $id_dataset2){
            array_push($dataset[$x], $dataset2[$i][$k]);// ensuite dans tout les cas on ajoute les on ajoute les valeurs qui restent de dataset2, hors NAME, dans dataset
        }
    }
}
for($i =0; $i<sizeof($dataset[0]); $i++){// on récupère l'id de certaines valeurs, permet de savoir qu'elle élément regarder pour avoir le leg_length, le stride_length,...
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
    if(sizeof($dataset[$i]) > $id_stride_length){// si l'id de stride_length est plus peties que la taille du tableau dans lequel on regarde -> si il y a un élément dans dataset1 mais pas dans dataset 2 alors cette élément n'aura pas de STR_LENGTH et de STANCE
        $stride_length = $dataset[$i][$id_stride_length] ? $dataset[$i][$id_stride_length] : 0; // on essaye de récupérer le stride_length -> au cas ou il y est une erreur et que stride n'exite pas alors que l'élémént est bien dans datset2
        $leg_length = $dataset[$i][$id_leg_length] ? $dataset[$i][$id_leg_length] : 0; // on essaye de récupérer le leg_length -> pour les cas ou l'élément est présent dans dataset2 mais pas dans dataset1 donc a une valeur vide pour LEG_LENGTH

        if($stride_length != 0 && $leg_length != 0){// si on récupère bien les 2 et que c'est pas égale a 0
            $speed = (($stride_length / $leg_length) - 1) * sqrt($leg_length * $g); // on fait le calcule de la vitesse
            if($dataset[$i][$id_stance] == 'bipedal'){// on regarde si c'est un bipède
                $speed_bipedal[$dataset[$i][$id_name]] = $speed; // si c'est un bipède on l'ajoute dans le tableau assosiatif avec pour key son nom et pour value sa vitesse
            }
        } else {
            $speed = "Calcul impossible";
        }
    }
    array_push($dataset[0], 'SPEED');
    array_push($dataset[$i], $speed);
}

arsort($speed_bipedal);// on trie le tableau des vitesses des bipède, par ordre decroissant des vitesses
foreach(array_keys($speed_bipedal) as $key){// pour chaque key
    echo $key.'<br/>';// on affiche les valeurs de key
}
?>