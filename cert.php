<?php
header("Content-Type: text/html; charset=utf-8");  
$config = array(
	'private_key_bits'=>2048,
	'private_key_type'=>OPENSSL_KEYTYPE_RSA,
	'digest_alg'=>'RSA-SHA256',
	//'config' => dirname($_SERVER['SCRIPT_FILENAME']).'/cert/openssl.cnf'
	'config' => 'C:\phpStudy\PHPTutorial\WWW\plyr\cert\openssl.cnf'
);

$CA_CERT = dirname($_SERVER['SCRIPT_FILENAME']).'/cert/'."ca.crt"; 
$CA_KEY  = dirname($_SERVER['SCRIPT_FILENAME']).'/cert/'."ca.key"; 

$cacert=file_get_contents($CA_CERT); 
$cakey = file_get_contents($CA_KEY);
$id=1;
$uid=1;
$certId=sprintf('%-09s', $id);

$savePath=dirname($_SERVER['SCRIPT_FILENAME']).'/cert/cert/'.$uid;
//$pkcspwd=(string)rand(1000000,9999999); 
//$certpwd=rand(1000000,9999999);//私钥密码
$pkcspwd="1000000";
$certpwd = "9999999";
$p12CertSavePath=$savePath.'/client.p12';
$privateCertSavePath=$savePath.'/client.key';
$csrCertSavePath=$savePath.'/client.csr';
$crtCertSavePath=$savePath.'/client.crt';
if(!is_dir($savePath))mkdir($savePath,0777,true);
$req_key = openssl_pkey_new($config); 

if(openssl_pkey_export_to_file($req_key, $privateCertSavePath, $certpwd, $config)) { 
	$dn = array( 
		 "countryName" => "CN", 
		 "stateOrProvinceName" => "Shanghai", 
		 //"localityName" => "Shanghai", 
		 "organizationName" => "Yuevd HAHA", 
		 "organizationalUnitName" => "plyr.com", 
		 "commonName" => "这是测试用的haha",
		 "emailAddress" => "2397994156@qq.com" 
	);
	$req_csr  = openssl_csr_new ($dn, $req_key, $config); 
	 openssl_csr_export_to_file($req_csr, $csrCertSavePath, false); 
	 $req_cert = openssl_csr_sign($req_csr, $cacert, $cakey, 365, $config,$certId);
	 
	
	 if(openssl_x509_export_to_file($req_cert, $crtCertSavePath,false)) { 
		exec(dirname($_SERVER['SCRIPT_FILENAME'])."/cert/openssl pkcs12 -export -inkey ".$privateCertSavePath." -in ".$crtCertSavePath." -certfile $CA_CERT -out ".$p12CertSavePath." -passin pass:{$certpwd} -passout pass:{$pkcspwd}"); 
		if(file_exists($p12CertSavePath)){
			$certP12=base64_encode(file_get_contents($p12CertSavePath));
			$array=array(
				'cert'=>$certP12,
				'pwd'=>'123'
			);
			$private_key=file_get_contents($privateCertSavePath);
							
			if(1){
				$certdata=array(
					'user_id'=>$uid,
					'private_key'=>$private_key,
					'password'=>$pkcspwd,
					'private_password'=>$certpwd,
					'creattime'=>time(),
					'pks12'=>$certP12,
					'cert_id'=>strlen(dechex($certId))%2==0?dechex($certId):'0'.dechex($certId) //转为16进制
				);
									var_dump("授权成功");
				exit;
			}
		}
	 }else{
		 var_dump('请求证书失败');
	 }
}else{
	var_dump('私钥导出失败');
}
echo "<br>";	
echo $CA_CERT;