<?php

defined("INIT") or (header("Location: /wisata_kaliurang/index.php") xor exit);

/**
 * @param string    $file
 * @param int       $w
 * @param int       $h
 * @param bool      $crop
 */
function resize_image($file, $w, $h, $crop = false) {

    list($width, $height) = getimagesize($file);
    $r = $width / $height;

    if ($crop) {
        if ($width > $height) {
            $width = ceil($width-($width*abs($r-$w/$h)));
        } else {
            $height = ceil($height-($height*abs($r-$w/$h)));
        }
        $newwidth = $w;
        $newheight = $h;
    } else {
        if ($w/$h > $r) {
            $newwidth = $h*$r;
            $newheight = $h;
        } else {
            $newheight = $w/$r;
            $newwidth = $w;
        }
    }
    $src = imagecreatefromjpeg($file);
    $dst = imagecreatetruecolor($newwidth, $newheight);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

    return $dst;
}
