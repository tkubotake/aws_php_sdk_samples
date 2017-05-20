<?php
ini_set('display_errors',1);
//require '/home/kubota/aws/aws-autoloader.php';
require '../vendor/autoload.php';
use Aws\Credentials\CredentialProvider;

//-------------------------------------------------------
$ini = '../credentials.ini';
$profile = 'production';
$iniProvider = CredentialProvider::ini($profile, $ini);
//$iniProvider = CredentialProvider::ini();
$iniProvider = CredentialProvider::memoize($iniProvider);

$client = new Aws\S3\S3Client([
    'region'   => 'ap-northeast-1',
    'version'  => 'latest',
    'credentials' => $iniProvider
]);

//-------------------------------------------------------
//バケットを作成する
function createBucket($client, $bucket_name){
    echo "createBucket!\n";
    //$bucket = "aws-php-test"; # bucket名を設定
    echo "Creating bucket named {$bucket}\n";
    $result = $client->createBucket(array(
     'Bucket' => $bucket_name
    ));
    echo $result;
    return $result;
}

//createBucket($client, "aws-create-bucket-test");

//-------------------------------------------------------
//a.jpgをバケットに入れる。
function storeFile($client,$keyName,$bucketName,$srcFile){
    $client->putObject([
        'Bucket' => $bucketName,
        'Key' => $keyName,
        'SourceFile' => $srcFile,
        'ContentType'=> mime_content_type($srcFile)
    ]);
}

$bucketName = 'pdb-crawled';
$keyName = 'a.txt';
$srcFile = 'a.txt';
storeFile($client,$keyName,$bucketName,$srcFile);

//-------------------------------------------------------
//バケットからa.txtを取り出して、save.txtとして保存
$saveFile = './save.txt';

$client->getObject([
    'Bucket' => $bucketName,
    'Key' => $keyName,
    'SaveAs' => $saveFile
]);

//-------------------------------------------------------
//バケット内のa.txtを削除
/*
$client->deleteObject([
    'Bucket' => $bucketName,
    'Key' => $keyName
]);
*/

//-------------------------------------------------------
// キー一覧を取得する方法
// Use the high-level iterators (returns ALL of your objects).
$objects = $client->getIterator('ListObjects', array('Bucket' => $bucketName));

echo "Keys retrieved!\n";
foreach ($objects as $object) {
    echo $object['Key'] . "\n";
}

// Use the plain API (returns ONLY up to 1000 of your objects).
/*
$result = $client->listObjects(array('Bucket' => $bucketName));

echo "Keys retrieved!\n";
foreach ($result['Contents'] as $object) {
    echo $object['Key'] . "\n";
}
*/
//-------------------------------------------------------