<?php
/**
 * @var array $xmpOptions
 */

require __DIR__ . '/vendor/autoload.php';

use Cake\Utility\Xml;
use XMPieWsdlClient\uProduceFactory;

$ls = "\r\n";

if (is_file("_config.php")) {
    require_once "_config.php";
} else {
    copy("_config.sample.php", "_config.php");
    die("Please enter your XMPie Admin details into the file _config.php" . $ls . $ls);
}

$Factory = new uProduceFactory($xmpOptions);
$RequestFabricator = $Factory->getUProduceRequestFabricator();
$ServiceFabricator = $Factory->getUProduceServiceFabricator();

print_r("Checking Number Of Available Clicks" . $ls);
$Request = $RequestFabricator->Licensing_SSP()->GetAvailableClicks();
$result = $ServiceFabricator->Licensing_SSP()->GetAvailableClicks($Request)->getGetAvailableClicksResult();
print_r($result . $ls . $ls);

print_r("Checking Number Of Active Connectivity Connections" . $ls);
$Request = $RequestFabricator->Licensing_SSP()->GetConnectivityLicenses();
$xmlString = $ServiceFabricator->Licensing_SSP()->GetConnectivityLicenses($Request)->getGetConnectivityLicensesResult()->getAny();
$xml = Xml::build($xmlString);
$a = Xml::toArray($xml);
print_r($a);
print_r($ls . $ls);

if (isset($a['diffgram']['NewDataSet']['Table'][0])) {
    $tables = $a['diffgram']['NewDataSet']['Table'];
} else {
    if (isset($a['diffgram']['NewDataSet']['Table'])) {
        $tables = [$a['diffgram']['NewDataSet']['Table']];
    } else {
        $tables = [];
        print_r("No Machines to disconnect... Bye!" . $ls);
    }
}

foreach ($tables as $table) {
    print_r("Attempting to disconnecting ID: " . $table['clientID'] . " - " . $table['machineName'] . $ls);
    $Request = $RequestFabricator->Licensing_SSP()->DeleteConnectivityLicense()->setInConnectivityLicenseID($table['clientID']);
    $result = $ServiceFabricator->Licensing_SSP()->DeleteConnectivityLicense($Request)->getDeleteConnectivityLicenseResult();
    if ($result == 1) {
        print_r("Succeeded!");
    } else {
        print_r("Failed!");
    }
    print_r($ls . $ls);
}
