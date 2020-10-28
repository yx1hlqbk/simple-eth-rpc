<?php

namespace Eth;

class Rpc
{
    /**
     * 取得最新區塊高度
     * 
     * @return array
     */
    public function eth_blockNumber()
    {
        $params = [
            'jsonrpc' => '2.0',
            'method' => 'eth_blockNumber',
            'params' => [],
            'id' => 0
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            $rpc['status'] = 'success';
            $rpc['result'] = base_convert($rpc['result'], 16, 10);
        }

        return $rpc;
    }

    /**
     * 使用區塊hash取得區塊資訊與交易列表
     * 
     * @param string $hash => 區塊hash
     * @return array
     */
    public function eth_getBlockByHash($hash)
    {
        $params = [
            'jsonrpc' => '2.0',
            'method' => 'eth_getBlockByHash',
            'params' => [$hash, true],
            'id' => 0
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            $rpc['status'] = 'success';
            $rpc['result']['difficulty'] = base_convert($rpc['result']['difficulty'], 16, 10);
            $rpc['result']['number'] = base_convert($rpc['result']['number'], 16, 10); // 如果是pending,回傳null
            $rpc['result']['timestamp'] = base_convert($rpc['result']['timestamp'], 16, 10);
            $rpc['result']['gasLimit'] = base_convert($rpc['result']['gasLimit'], 16, 10);
            $rpc['result']['gasUsed'] = base_convert($rpc['result']['gasUsed'], 16, 10);
            $rpc['result']['size'] = base_convert($rpc['result']['size'], 16, 10); // bytes

            // 交易列表
            foreach ($rpc['result']['transactions'] as &$transactions) {
                $transactions['nonce'] = base_convert($transactions['nonce'], 16, 10);
                $transactions['gas'] = base_convert($transactions['gas'], 16, 10);
                $transactions['gasPrice'] = base_convert($transactions['gasPrice'], 16, 10) / pow(10, 18);
                $transactions['value'] = base_convert($transactions['value'], 16, 10) / pow(10, 18);
                $transactions['transactionIndex'] = base_convert($transactions['transactionIndex'], 16, 10);
            }
        }

        return $rpc;
    }

    /**
     * 使用區塊number取得區塊資訊與交易列表
     * 
     * @param int $number => 區塊高度
     * @param boolean $boolean => true:會顯示交易明細 false:顯示交易hash
     * @return array
     */
    public function eth_getBlockByNumber($number, $boolean = true)
    {
        $params = [
            'jsonrpc' => '2.0',
            'method' => 'eth_getBlockByNumber',
            'params' => ['0x' . base_convert($number, 10, 16), $boolean],
            'id' => 0
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            $rpc['status'] = 'success';
            $rpc['result']['number'] = base_convert($rpc['result']['number'], 16, 10); // 如果是pending,回傳null
            $rpc['result']['timestamp'] = base_convert($rpc['result']['timestamp'], 16, 10);
            $rpc['result']['gasLimit'] = base_convert($rpc['result']['gasLimit'], 16, 10);
            $rpc['result']['gasUsed'] = base_convert($rpc['result']['gasUsed'], 16, 10);

            if ($boolean === true) {
                // 交易列表
                foreach ($rpc['result']['transactions'] as &$transactions) {
                    $transactions['blockNumber'] = base_convert($transactions['blockNumber'], 16, 10);
                    $transactions['transactionIndex'] = base_convert($transactions['transactionIndex'], 16, 10);
                    $transactions['nonce'] = base_convert($transactions['nonce'], 16, 10);
                    $transactions['gas'] = base_convert($transactions['gas'], 16, 10);
                    $transactions['gasPrice'] = base_convert($transactions['gasPrice'], 16, 10) / pow(10, 18);
                    $transactions['value'] = base_convert($transactions['value'], 16, 10) / pow(10, 18);
                }
            }
        }

        return $rpc;
    }
    
    /**
     * 使用交易hash取得明細
     * 
     * @param string $hash => 交易hash
     * @return array
     */
    public function eth_getTransactionByHash($hash)
    {
        $params = [
            'jsonrpc' => '2.0',
            'method' => 'eth_getTransactionByHash',
            'params' => [$hash],
            'id' => 0,
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            $rpc['status'] = 'success';
            $rpc['result']['gasPrice'] = base_convert($rpc['result']['gasPrice'], 16, 10) / pow(10, 18);
            $rpc['result']['gas'] = base_convert($rpc['result']['gas'], 16, 10);
            $rpc['result']['blockNumber'] = base_convert($rpc['result']['blockNumber'], 16, 10); // 如果是pending,回傳null
            $rpc['result']['value'] = base_convert($rpc['result']['value'], 16, 10) / pow(10, 18);
            $rpc['result']['nonce'] = base_convert($rpc['result']['nonce'], 16, 10);
            $rpc['result']['transactionIndex'] = base_convert($rpc['result']['transactionIndex'], 16, 10);
        }

        return $rpc;
    }

    /**
     * 指定地址送出交易次數
     * 
     * @param string $address => 地址
     * @param string|int $status => 區塊高度 或是 "latest", "earliest" or "pending"
     * @return array
     */
    public function eth_getTransactionCount($address, $mix = 'latest')
    {
        if (is_numeric($mix)) {
            $mix = '0x' . base_convert($mix, 10, 16);
        }

        $params = [
            'jsonrpc' => '2.0',
            'method' => 'eth_getTransactionCount',
            'params' => [$address, $mix],
            'id' => 0,
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            $rpc['status'] = 'success';
            $rpc['result'] = base_convert($rpc['result'], 16, 10);
        }

        return $rpc;
    }

    /**
     * 使用交易hash取得明細有logs
     * 
     * @param string $hash => 交易hash
     * @return array
     */
    public function eth_getTransactionReceipt($hash)
    {
        $params = [
            'jsonrpc' => '2.0',
            'method' => 'eth_getTransactionReceipt',
            'params' => [$hash],
            'id' => 0,
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            if (empty($rpc['result'])) {
                $rpc['status'] = 'error';
                $rpc['message'] = 'Transaction Hash is pending!!';
            } else {
                $rpc['status'] = 'success';
                $rpc['result']['blockNumber'] = base_convert($rpc['result']['blockNumber'], 16, 10);
                $rpc['result']['gasUsed'] = base_convert($rpc['result']['gasUsed'], 16, 10);
                $rpc['result']['cumulativeGasUsed'] = base_convert($rpc['result']['cumulativeGasUsed'], 16, 10);
                $rpc['result']['status'] = base_convert($rpc['result']['status'], 16, 10);
                $rpc['result']['transactionIndex'] = base_convert($rpc['result']['transactionIndex'], 16, 10);
    
                // logs
                if (!empty($rpc['result']['logs'])) {
                    foreach ($rpc['result']['logs'] as &$log) {
                        $log['logIndex'] = base_convert($log['logIndex'], 16, 10);
                        $log['transactionIndex'] = base_convert($log['transactionIndex'], 16, 10);
                        $log['blockNumber'] = base_convert($log['blockNumber'], 16, 10);
    
                        if (strlen($log['data']) <= 66 && is_numeric(base_convert($log['data'], 16, 10)) && strlen(base_convert($log['data'], 16, 10)) <= 30) {
                            $log['data'] = base_convert($log['data'], 16, 10);
                        }
    
                        if (count($log['topics']) == 3) {
                            $log['topics'][1] = '0x' . substr($log['topics'][1], 26);
                            $log['topics'][2] = '0x' . substr($log['topics'][2], 26);
                        }
                    }
                }    
            }
        }

        return $rpc;
    }

    /**
     * 使用區塊hash取得交易總數
     * 
     * @param string $hash
     * @return array
     */
    public function eth_getBlockTransactionCountByHash($hash)
    {
        $params = [
            'jsonrpc' => '2.0',
            'method' => 'eth_getBlockTransactionCountByHash',
            'params' => [$hash],
            'id' => 0,
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            $rpc['status'] = 'success';
            $rpc['result'] = base_convert($rpc['result'], 16, 10);
        }

        return $rpc;
    }
    
    /**
     * 使用區塊高度或是狀態取得交易總數
     * 
     * @param string|int $mix => 區塊高度 或是 "latest", "earliest" or "pending"
     * @return array
     */
    public function eth_getBlockTransactionCountByNumber($mix = 'latest')
    {
        if (is_numeric($mix)) {
            $mix = '0x' . base_convert($mix, 10, 16);
        }

        $params = [
            'jsonrpc' => '2.0',
            'method' => 'eth_getBlockTransactionCountByNumber',
            'params' => [$mix],
            'id' => 0,
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            $rpc['status'] = 'success';
            $rpc['result'] = base_convert($rpc['result'], 16, 10);
        }

        return $rpc;
    }

    /**
     * 使用區塊hash & index 取得交易明細
     * 
     * @param string $hash => 區塊hash
     * @param int $index => 交易序號
     * @return array
     */
    public function eth_getTransactionByBlockHashAndIndex($hash, $index = 0)
    {
        $params = [
            'jsonrpc' => '2.0',
            'method' => 'eth_getTransactionByBlockHashAndIndex',
            'params' => [$hash, '0x' . base_convert($index, 10, 16)],
            'id' => 0,
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            $rpc['status'] = 'success';

            if (!empty($rpc['result'])) {
                $rpc['result']['gasPrice'] = base_convert($rpc['result']['gasPrice'], 16, 10) / pow(10, 18);
                $rpc['result']['gas'] = base_convert($rpc['result']['gas'], 16, 10);
                $rpc['result']['blockNumber'] = base_convert($rpc['result']['blockNumber'], 16, 10); // 如果是pending,回傳null
                $rpc['result']['value'] = base_convert($rpc['result']['value'], 16, 10) / pow(10, 18);
                $rpc['result']['nonce'] = base_convert($rpc['result']['nonce'], 16, 10);
                $rpc['result']['transactionIndex'] = base_convert($rpc['result']['transactionIndex'], 16, 10);
            }
        }

        return $rpc;
    }

    /**
     * 使用區塊number & index 取得交易明細
     * 
     * @param string|int $mix => 高度 或是 "latest", "earliest" or "pending"
     * @param int $index => 交易序號
     * @return array
     */
    public function eth_getTransactionByBlockNumberAndIndex($mix = 'latest', $index = 0)
    {
        $params = [
            'jsonrpc' => '2.0',
            'method' => 'eth_getTransactionByBlockNumberAndIndex',
            'params' => [$status, '0x' . base_convert($index, 10, 16)],
            'id' => 0,
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            $rpc['status'] = 'success';
            if (!empty($result['result'])) {
                $rpc['result']['gasPrice'] = base_convert($rpc['result']['gasPrice'], 16, 10) / pow(10, 18);
                $rpc['result']['gas'] = base_convert($rpc['result']['gas'], 16, 10);
                $rpc['result']['blockNumber'] = base_convert($rpc['result']['blockNumber'], 16, 10); // 如果是pending,回傳null
                $rpc['result']['value'] = base_convert($rpc['result']['value'], 16, 10) / pow(10, 18);
                $rpc['result']['nonce'] = base_convert($rpc['result']['nonce'], 16, 10);
                $rpc['result']['transactionIndex'] = base_convert($rpc['result']['transactionIndex'], 16, 10);
            }
        }

        return $rpc;
    }

    /**
     * 地址標記資料
     * 
     * @param string $address => 地址
     * @param string $data => 簽署資料
     * @return array
     */
    public function eth_sign($address, $data)
    {
        $params = [
            'jsonrpc' => '2.0',
            'method' => 'eth_sign',
            'id' => 0,
            'params' => [$address, $data]
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            $rpc['status'] = 'success';
        }

        return $rpc;
    }

    /**
     * 如果資料欄位包含程式碼，則建立新的訊息呼叫事件或建立合約
     * 
     * @param array $params
     * @return array
     */
    public function eth_sendTransaction($params)
    {
        $params = [
            'jsonrpc' => '2.0',
            'method' => 'eth_sendTransaction',
            'id' => 0,
            'params' => $params
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            $rpc['status'] = 'success';
        }

        return $rpc;
    }

    /**
     * 推上交易
     * 
     * @param string $row
     * @return array
     */
    public function eth_sendRawTransaction($row)
    {
        $params = [
            'jsonrpc' => '2.0',
            'method' => 'eth_sendRawTransaction',
            'id' => 0,
            'params' => [$row]
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            $rpc['status'] = 'success';
        }

        return $rpc;
    }

    /**
     * 取得餘額
     * 
     * @param string $address => 地址
     * @param string $stauts => "latest", "earliest" or "pending"
     * @return array
     */
    public function eth_getBalance($address, $stauts = 'latest')
    {
        $params = [
            'jsonrpc' => '2.0',
            'method' => 'eth_getBalance',
            'id' => 0,
            'params' => [$address, $stauts]
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            $rpc['status'] = 'success';
            $rpc['result'] = base_convert($rpc['result'], 16, 10) / pow(10, 18);
        }

        return $rpc;
    }

    /**
     * 取得地址中的代碼 (專門用在合約)
     * 
     * 備註:
     * 如果非合約地址，會回傳0x
     * 
     * @param string $address => 地址
     * @param string|int $mix => 區塊高度 或是 "latest", "earliest" or "pending"
     * @return array
     */
    public function eth_getCode($address, $mix = 'latest')
    {
        if (is_numeric($mix)) {
            $mix = '0x' . base_convert($mix, 10, 16);
        }

        $params = [
            'jsonrpc' => '2.0',
            'method' => 'eth_getCode',
            'id' => 0,
            'params' => [$address, $mix]
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            $rpc['status'] = 'success';
        }

        return $rpc;
    }

    /**
     * 指定地址儲存內容
     * 
     * @param string $address => 地址
     * @param int $position => 索引編號
     * @param string|int $mix => 區塊高度 或是 "latest", "earliest" or "pending"
     * @return array
     */
    public function eth_getStorageAt($address, $position = 0, $mix = 'latest')
    {
        if (is_numeric($mix)) {
            $mix = '0x' . base_convert($mix, 10, 16);
        }

        $params = [
            'jsonrpc' => '2.0',
            'method' => 'eth_getStorageAt',
            'id' => 0,
            'params' => [$address, '0x' . base_convert($position, 10, 16), $mix]
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            $rpc['status'] = 'success';
        }

        return $rpc;
    }

    /**
     * 取得api下擁有的地址
     * 
     * @return array
     */
    public function eth_accounts()
    {
        $params = [
            'jsonrpc' => '2.0',
            'method' => 'eth_accounts',
            'id' => 0,
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            $rpc['status'] = 'success';
        }

        return $rpc;
    }

    /**
     * client錢包地址
     * 
     * @return array
     */
    public function eth_coinbase()
    {
        $params = [
            'jsonrpc' => '2.0',
            'method' => 'eth_coinbase',
            'id' => 0,
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            $rpc['status'] = 'success';
        }

        return $rpc;
    }

    /**
     * call
     * 
     * @param array $params
     * @return array
     */
    public function eth_call($params)
    {
        return $this->connect([
            'jsonrpc' => '2.0',
            'method' => 'eth_call',
            'id' => 0,
            'params' => $params
        ]);
    }

    /**
     * get logs
     * 
     * @param array $params
     * @return array
     */
    public function eth_getLogs($params)
    {
        return $this->connect([
            'jsonrpc' => '2.0',
            'method' => 'eth_getLogs',
            'id' => 0,
            'params' => $params
        ]);
    }

    /**
     * 當前的ethereum協議版本
     * 
     * @return array
     */
    public function eth_protocolVersion()
    {
        $params = [
            'jsonrpc' => '2.0',
            'method' => 'eth_protocolVersion',
            'id' => 0,
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            $rpc['status'] = 'success';
            $rpc['result'] = base_convert($rpc['result'], 16, 10);
        }

        return $rpc;
    }

    /**
     * 取得目前gas price
     * 
     * @return array
     */
    public function eth_gasPrice()
    {
        $params = [
            'jsonrpc' => '2.0',
            'method' => 'eth_gasPrice',
            'id' => 0,
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            $rpc['status'] = 'success';
            $rpc['result'] = base_convert($rpc['result'], 16, 10) / pow(10, 18);
        }

        return $rpc;
    }

    /**
     * 計算gas
     * 
     * @param string $to => 接收地址
     * @return array
     */
    public function eth_estimateGas($to)
    {
        $params = [
            'jsonrpc' => '2.0',
            'method' => 'eth_estimateGas',
            'params' => [
                [
                    'to' => $to,
                    'data' => '0x',
                ],
            ],
            'id' => 0,
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            $rpc['status'] = 'success';
            $rpc['result'] = base_convert($rpc['result'], 16, 10) / pow(10, 18);
        }

        return $rpc;
    }

    /**
     * 返回當前的網路協議版本
     * 
     * 1: Ethereum Mainnet
     * 2: Morden Testnet (deprecated)
     * 3: Ropsten Testnet
     * 4: Rinkeby Testnet
     * 42: Kovan Testnet
     * 
     * @param int $id
     * @return array
     */
    public function net_version($id = 0)
    {
        $params = [
            'jsonrpc' => '2.0',
            'method' => 'net_version',
            'id' => $id,
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            $rpc['status'] = 'success';
        }

        return $rpc;
    }

    /**
     * 客戶端是否正在主動偵聽網路連線
     * 
     * @param int $id
     * @return array
     */
    public function net_listening($id = 0)
    {
        $params = [
            'jsonrpc' => '2.0',
            'method' => 'net_listening',
            'id' => $id,
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            $rpc['status'] = 'success';
        }

        return $rpc;
    }

    /**
     * 當前連線到客戶端的對端的數量
     * 
     * @param int $id
     * @return array
     */
    public function net_peerCount($id = 0)
    {
        $params = [
            'jsonrpc' => '2.0',
            'method' => 'net_peerCount',
            'id' => $id,
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            $rpc['status'] = 'success';
            $rpc['result'] = base_convert($rpc['result'], 16, 10);
        }

        return $rpc;
    }

    /**
     * 當前的客戶端版本
     * 
     * @return array
     */
    public function web3_clientVersion()
    {
        $params = [
            'jsonrpc' => '2.0',
            'method' => 'web3_clientVersion',
            'params' => [],
            'id' => $id,
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            $rpc['status'] = 'success';
        }

        return $rpc;
    }

    /**
     * 返回給定資料的Keccak-256（不是標準化的SHA3-256）
     * 
     * @param string $sha3
     * @return array
     */
    public function web3_sha3($sha3)
    {
        $params = [
            'jsonrpc' => '2.0',
            'method' => 'web3_sha3',
            'params' => [$sha3],
            'id' => $id,
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            $rpc['status'] = 'success';
        }

        return $rpc;
    }

    /**
     * 
     * @return array
     */
    public function eth_syncing()
    {
        $params = [
            'jsonrpc' => '2.0',
            'method' => 'eth_syncing',
            'params' => [],
            'id' => $id,
        ];
        $rpc = $this->connect($params);

        if (isset($rpc['error'])) {
            $rpc['status'] = 'error';
        } else {
            $rpc['status'] = 'success';
        }

        return $rpc;
    }

    /**
     * Rpc 連線
     * 
     * @param array $params
     * @return array
     */
    private function connect($params)
    {
        $timeout = 30;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->rpcUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $file_contents = curl_exec($ch);
        curl_close($ch);

        return json_decode($file_contents, true);
    }
}
