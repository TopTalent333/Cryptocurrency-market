<?php
namespace CoinMarketCapREG;
class CMC_ApiConf{
    const PLUGIN_NAME = 'Coin Market Cap';
    const PLUGIN_VERSION = CMC;
    const PLUGIN_PREFIX = 'cmc';
    const PLUGIN_AUTH_PAGE = 'cmc-settings';
    const PLUGIN_URL = CMC_URL;
}

    require_once 'class.settings-api.php';
    require_once 'CoinsMarketCapBase.php';
    require_once 'api-auth-settings.php';

	new CMC_Settings();