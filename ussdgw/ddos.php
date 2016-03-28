<?php

$dirlock='/tmp';


function ddos ($filelock, $sleepsec) {
  global $dirlock;
  $filelockfull=$dirlock.'/'.$filelock;

  if (file_exists($filelockfull)) {
    $dtime=time();
    $filemtime_filelockfull=filemtime($filelockfull);
    if ($filemtime_filelockfull>$dtime-$sleepsec) {
      return $sleepsec-$dtime+$filemtime_filelockfull;
    }
  }


  if ($file = fopen($filelockfull, "w")) {
    fclose($filelockfull);
  } else {
      return -1;
    }

  return 0;
}

?>