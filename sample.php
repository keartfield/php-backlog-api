<?php

if ($_POST['submit']) {
	// Backlog API
	$spaceId = 'hoge';
	$apiKey  = 'piyo';

	/*
	* 添付ファイル
	*/
	$data = ['file' => new CURLFile($_FILES['file']['tmp_name'], '', $_FILES['file']['name'])];

	$url = 'https://'.$spaceId.'.backlog.jp/api/v2/space/attachment?apiKey='.$apiKey;

	$headers = [
		'Content-Type:multipart/form-data', 
		'Content-Disposition:form-data; name="file";',
		'Content-Type:application/octet-stream',
	];

	$curl = curl_init();

	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HEADER, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

	$response 		= curl_exec($curl);
	$header_size 	= curl_getinfo($curl, CURLINFO_HEADER_SIZE); 
	$header 		= substr($response, 0, $header_size);
	$body 			= substr($response, $header_size);
	$attachment 	= json_decode($body, true); 

	curl_close($curl);

	/*
	* 課題登録
	*/
	$params = [
		'projectId'   	=> 'foo', 								// プロジェクトID
		'issueTypeId' 	=> 'bar', 								// 種別ID
		'priorityId'  	=> 1,    								// 優先度
		'categoryId'  	=> [],									// カテゴリID
		'dueDate'     	=> date('Y-m-d', strtotime('+1 day')),	// 期限日
		'summary'     	=> htmlspecialchars('ほげほげ'),      	// 課題のタイトル
		'attachmentId'	=> [$attachment['id']],					// 添付ファイルID
		'description' 	=> htmlspecialchars('ぴよぴよ'),			// 課題の内容
	];

	$url     = 'https://'.$spaceId.'.backlog.jp/api/v2/issues?apiKey='.$apiKey.'&'.http_build_query($params, '', '&');

	$headers = ['Content-Type: application/x-www-form-urlencoded;'];

	$curl = curl_init();

	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HEADER, true);

	curl_exec($curl);
	curl_close($curl);
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>Backlog API demo</title>
</head>
<body>
	<form method="post" enctype="multipart/form-data">
		<input type="file" name="file" /><br />
		<input type="submit" name="submit" value="送信" />
	</form>
</body>
</html>