<?php
require '../vendor/autoload.php';
use Aws\Credentials\CredentialProvider;
use Aws\Ec2\Ec2Client;

$ini = '../credentials.ini';
$profile = 'production';
$iniProvider = CredentialProvider::ini($profile, $ini);
//$iniProvider = CredentialProvider::ini();
$iniProvider = CredentialProvider::memoize($iniProvider);

$client = new Ec2Client([
  'region' => 'ap-northeast-1',
  'version' => 'latest',
  'credentials' => $iniProvider
]);
$result = $client->describeInstances([
  'Filters' => [
        [
            'Name' => 'tag:Name',
            //'Values' => ['test1a'],
        ],
  ]
]);
print $result['Reservations'][0]['Instances'][0]['PrivateIpAddress'];
