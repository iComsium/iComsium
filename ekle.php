<?php 


    // Formumuzdan gelen verileri çekiyoruz.


    $dosyaadi    =    $_POST["dosyaadi"];


    $metin        =    $_POST["metin"];


    // Dosyamızı Oluşturuyoruz


    $dosyaolustur    = touch($dosyaadi);


    // Kontrol Yaptırıyoruz


    if($dosyaolustur){


        echo '<div id="basarili" style="background:lightyellow; margin:10px; padding:10px; border:1px solid #ccc">Başarılı Bir şekilde "'.$dosyaadi.'" oluşturulmuştur.</div>';


        }else {


    //    Bir hata varsa hata mesajımızı verdiriyoruz.


        echo '<div id="hata" style="background:red; color:#fff; margin:10px; padding:10px; border:1px solid #ccc">Sistem durakladi fakat yazdırılmış olabilir.</div>';


        }


    // Oluşan Dosyamızı Açıyoruz.


    $dosyaadi    =    fopen($dosyaadi,"a+");


    // Oluşan Dosyaya Yazıyoruz.


    $yaz            =    fwrite($dosyaadi,$metin);


    // Bağlantıyı kapatıyoruz


    fclose($dosyaadi);


        if($yaz){


        echo '<div id="basarili" style="background:lightyellow; margin:10px; padding:10px; border:1px solid #ccc">Başarılı Bir şekilde aşağıdaki metin dosyaya yazılmıştır.


        <p><b>"'.$metin.'"</b></p></div>';


        }else {


    //    Bir hata varsa hata mesajımızı verdiriyoruz.


        echo '<div id="hata" style="background:red; color:#fff; margin:10px; padding:10px; border:1px solid #ccc">Bir sorun oluştu fakat yazdırılmış olabilir.</div>';


        }


?>