<?php
function hsv_to_rgb(array $hsv)
 {
    $R = 0; $G = 0; $B = 0;
    $hsv[0] /= 360;
    $hsv[1] /= 100;
    $hsv[2] /= 100;
    list($H,$S,$V) = $hsv;
    //1
    $H *= 6;
    //2
    $I = floor($H);
    $F = $H - $I;
    //3
    $M = $V * (1 - $S);
    $N = $V * (1 - $S * $F);
    $K = $V * (1 - $S * (1 - $F));
    //4
    switch ($I) {
        case 0:
            list($R,$G,$B) = array($V,$K,$M);
            break;
        case 1:
            list($R,$G,$B) = array($N,$V,$M);
            break;
        case 2:
            list($R,$G,$B) = array($M,$V,$K);
            break;
        case 3:
            list($R,$G,$B) = array($M,$N,$V);
            break;
        case 4:
            list($R,$G,$B) = array($K,$M,$V);
            break;
        case 5:
        case 6: //for when $H=1 is given
            list($R,$G,$B) = array($V,$M,$N);
            break;
    }
    return array((int)($R*255), (int)($G*255), (int)($B*255));
}
function generate_colors($count)
{
    $saturation = 43;
    $value = 85;
    $hue_step = 320 / $count;
    $colors = array();
    $current_hue = 0;
    for($i = 0; $i < $count; $i++)
    {
        $rgb = hsv_to_rgb(array($current_hue,$saturation,$value));
        array_push($colors, $rgb);
        $current_hue += $hue_step;
    }
    return $colors;
}
?>