#!/bin/bash

src=$1
dest=$2
size=$3
ratio=30
let i=1

/home/vagun/bin/facedetect -o "tmp/test.jpg" "$src" | while read x y w h; do
  let x1=$x-$w*$ratio/100
  let y1=$y-$h*$ratio/100
  let w1=$w+$w*$ratio*2/100
  let h1=$h+$h*$ratio*2/100
  destFile=$dest-$i.jpg
  convert "$src" -quality 100 -crop ${w1}x${h1}+${x1}+${y1} -resize $sizex$size "$destFile"
  echo DEST FILE: $destFile
  let i+=1
done
