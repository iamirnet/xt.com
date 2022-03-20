<?php

namespace iAmirNet\XT;

class Client
{
    protected $base = "https://api.xt.com/", $api_key, $api_secret;

    public function __construct($key = null, $secret = null)
    {
        if ($key) $this->api_key = $key;
        if ($secret) $this->api_secret = $secret;
        $this->header = '';
    }

    public function time()
    {
        return $this->request("trade/api/v1/getServerTime");
    }

    public function marketConfig()
    {
        return $this->request("data/api/v1/getMarketConfig");
    }

    public function buy($symbol, $quantity, $price, $type = "LIMIT", $flags = [])
    {
        return $this->order("BUY", $symbol, $quantity, $price, $type, $flags);
    }

    public function sell($symbol, $quantity, $price, $type = "LIMIT", $flags = [])
    {
        return $this->order("SELL", $symbol, $quantity, $price, $type, $flags);
    }

    public function cancel($symbol, $orderid)
    {
        return $this->signedRequest("trade/api/v1/cancel", ["market" => strtolower($symbol), "id" => $orderid], "POST");
    }

    public function orderInfo($symbol, $orderid)
    {
        return $this->signedRequest("trade/api/v1/getOrder", ["market" => strtolower($symbol), "id" => $orderid]);
    }

    public function openOrders($symbol, $page = false, $pageSize = false)
    {
        $params = ["market" => strtolower($symbol)];
        if ($page) $params['page'] = $page;
        if ($pageSize) $params['pageSize'] = $pageSize;
        return $this->signedRequest("trade/api/v1/getOpenOrders", $params);
    }

    public function bulkOrders($symbol, array $data)
    {
        return $this->signedRequest("trade/api/v1/batchOrder", ["market" => strtolower($symbol), 'data', $data], "POST");
    }

    public function bulkOrdersInfo($symbol, array $ids)
    {
        return $this->signedRequest("trade/api/v1/getBatchOrders", ["market" => strtolower($symbol), 'data', $ids]);
    }

    public function bulkOrdersCancel($symbol, array $ids)
    {
        return $this->signedRequest("trade/api/v1/batchCancel", ["market" => strtolower($symbol), 'data', $ids], "POST");
    }

    public function trades($symbol)
    {
        return $this->request("data/api/v1/getTrades", ["market" => strtolower($symbol)]);
    }

    public function myTrades($symbol, $limit = false, $startTime = false, $endTime = false, $fromId = false)
    {
        $params = ["market" => strtolower($symbol)];
        if ($fromId) $params['fromId'] = $fromId;
        if ($limit) $params['limit'] = $limit;
        if ($startTime) $params['startTime'] = $startTime;
        if ($endTime) $params['endTime'] = $endTime;
        return $this->signedRequest("trade/api/v1/myTrades", $params);
    }

    public function kline($symbol, $type = "1min", $since = 0)
    {
        return $this->request("data/api/v1/getKLine", ["market" => strtolower($symbol), "type" => $type, "since" => $since]);
    }

    public function bookTicker($symbol)
    {
        return $this->request("data/api/v1/getTicker", ["market" => strtolower($symbol)]);
    }

    public function bookPrices()
    {
        return $this->request("data/api/v1/getTickers");
    }

    public function account()
    {
        return $this->signedRequest("trade/api/v1/getAccounts");
    }

    public function specificAccount($accountID)
    {
        return $this->signedRequest("trade/api/v1/getFunds", ["account" => $accountID]);
    }

    public function depth($symbol)
    {
        return $this->request("data/api/v1/getDepth", ["market" => strtolower($symbol)]);
    }

    public function balances()
    {
        return $this->signedRequest("trade/api/v1/getBalance");
    }

    public function order($side, $symbol, $quantity, $price, $type = "LIMIT", $flags = [])
    {
        $opt = $this->orderData($side, $symbol, $quantity, $price, $type, $flags);
        return $this->signedRequest("trade/api/v1/order", $opt, "POST");
    }

    public function orderData($side, $symbol, $quantity, $price, $type = "LIMIT", $flags = [])
    {
        $side = strtoupper($side);
        $type = strtoupper($type);
        if (!in_array($side, ['BUY', 'SELL'])) die("Unsupport side parameters, please check!");
        if (!in_array($type, ['LIMIT', 'MARKET'])) die("Unsupport type parameters, please check!");
        $opt = [
            "market" => strtolower($symbol),
            "type" => $side == "BUY" ? 1 : 0,
            "entrustType" => $type == "MARKET" ? 1 : 0,
            "number" => (string) $quantity,
        ];
        if ($type == "LIMIT") {
            $opt["price"] = "$price";
        }
        return $opt;
    }

    private function request($url, $params = [], $method = "GET")
    {
        $headers = array('User-Agent: Mozilla/4.0 (compatible; PHP XT API - iamir.net)', 'Content-type: application/x-www-form-urlencoded');
        $query = http_build_query($params, '', '&');
        $ret = $this->http_get($this->base . $url . '?' . $query, $headers);
        return $ret;
    }

    private function signedRequest($url, $params = [], $method = "GET")
    {
        if (empty($this->api_key)) die("signedRequest error: API Key not set!");
        if (empty($this->api_secret)) die("signedRequest error: API Secret not set!");

        $timestamp_t = $this->getServerTime();
        if ($timestamp_t < 0) {
            $timestamp_t = number_format(microtime(true) * 1000, 0, '.', '');
        }
        $params = array_merge(['accesskey' => $this->api_key], $params,['nonce' => $timestamp_t]);
        ksort($params);
        $query = http_build_query($params, '', '&');
        $signature = hash_hmac('sha256', $query, $this->api_secret);
        $headers = array("User-Agent: Mozilla/4.0 (compatible; PHP XT API - iamir.net)",
            "Content-type: application/x-www-form-urlencoded");
        if ($method == 'GET') {
            // parameters encoded as query string in URL
            $endpoint = "{$this->base}{$url}?{$query}&signature={$signature}";
            $ret = $this->http_get($endpoint, $headers);
        } else if ($method == 'POST') {
            $endpoint = "{$this->base}{$url}";
            $params['signature'] = $signature;
            $ret = $this->http_post($endpoint, $params, []);
        } else {
            $endpoint = "{$this->base}{$url}?{$query}&signature={$signature}";
            $ret = $this->http_other($method, $endpoint, $headers);
        }
        return $ret;
    }

    private function http_post($url, $data, $headers = [])
    {
        $data = http_build_query($data);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($curl);
        curl_close($curl);
        return $this->output($output, $url);
    }

    private function http_get($url, $headers = [], $data = [])
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($curl);
        curl_close($curl);
        return $this->output($output, $url);
    }

    private function http_other($method, $url, $headers = [])
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($curl);
        curl_close($curl);
        return $this->output($output, $url);
    }

    private function output($output, $url)
    {
        $output = json_decode($output, true);
        if (!isset($output['code'])) return (object)['status' => true, 'code' => 200, 'data' => $output];
        if ($output['code'] == 200) {
            return (object)['status' => true, 'code' => $output['code'], 'data' => $output['data']];
        } else {
            $code = isset($output['code']) ? $output['code'] : "nOK";
            return (object)['status' => false, 'code' => $code, 'message' => (isset($output['msg']) ? $output['msg'] : (isset($output['info']) ? $output['info'] : "Error Code: $code")) . " ($url)"];
        }
    }

    public function getServerTime()
    {
        $t = $this->time();
        if ($t->status && isset($t->data['serverTime'])) {
            return $t->data['serverTime'];
        }
        return -1;
    }
}