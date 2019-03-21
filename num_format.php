<?php
function num_to_format($numStr){
    if(intval($numStr) < 0){
        $neg="-";
        $numStr = abs($numStr);
    } else {
        $neg="";
    }
    switch (strlen($numStr)){
        case 1:
            $formatVal = $numStr;
            break;
        case 2:
            $formatVal = $numStr;
            break;
        case 3:
            $formatVal = $numStr;
            break;
        case 4:
            $formatVal = substr($numStr, -4, 1).",".substr($numStr, -3, 3);
            break;
        case 5:
            $formatVal = substr($numStr, -5, 2).",".substr($numStr, -3, 3);
            break;
        case 6:
            $formatVal = substr($numStr, -6, 1).",".substr($numStr, -5, 2).",".substr($numStr, -3, 3);
            break;
        case 7:
            $formatVal = substr($numStr, -7, 2).",".substr($numStr, -5, 2).",".substr($numStr, -3, 3);
            break;
        case 8:
            $formatVal = substr($numStr, -8, 1).",".substr($numStr, -7, 2).",".substr($numStr, -5, 2).",".substr($numStr, -3, 3);
            break;
        case 9:
            $formatVal = substr($numStr, -9, 2).",".substr($numStr, -7, 2).",".substr($numStr, -5, 2).",".substr($numStr, -3, 3);
            break;
        case 10:
            $formatVal = substr($numStr, -10, 3).",".substr($numStr, -7, 2).",".substr($numStr, -5, 2).",".substr($numStr, -3, 3);
            break;
        default:
            echo "0";
    }
    return $neg.$formatVal;     
}
?>
