<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BillplzService
{
    private string $apiKey;
    private string $collectionId;
    private string $baseUrl;
    private string $xSignatureKey;

    public function __construct()
    {
        $this->apiKey = config('services.billplz.api_key');
        $this->collectionId = config('services.billplz.collection_id');
        $this->xSignatureKey = config('services.billplz.x_signature_key');

        // Gunakan sandbox atau production URL
        $this->baseUrl = config('services.billplz.sandbox', true)
            ? 'https://www.billplz-sandbox.com/api'
            : 'https://www.billplz.com/api';
    }

    /**
     * Create Direct Payment Bill dengan bank code
     * 
     * @param \App\Models\Order $order
     * @param string|null $bankCode
     * @return array ['bill_url' => '...', 'bill_id' => '...']
     */
    public function createDirectPaymentBill($order, $bankCode = null)
    {
        try {
            // Convert amount to sen (smallest currency unit)
            $amountInSen = (int) ($order->total_amount * 100);

            $data = [
                'collection_id' => $this->collectionId,
                'email' => $order->customer_email,
                'mobile' => $this->formatMobileNumber($order->customer_phone),
                'name' => $order->customer_name,
                'amount' => $amountInSen,
                'description' => "Order #{$order->order_number}",
                'callback_url' => config('services.billplz.callback_url'),
                'redirect_url' => config('services.billplz.redirect_url') . '?order_id=' . $order->id,

                // Direct Payment Gateway fields
                'reference_1_label' => 'Bank Code',
                'reference_1' => $bankCode ?? 'BP-BILLPLZ1', // Default to Visa/Mastercard

                // Additional references
                'reference_2_label' => 'Order ID',
                'reference_2' => $order->order_number,
            ];

            $response = $this->makeRequest('POST', '/v3/bills', $data);

            if (!isset($response['id']) || !isset($response['url'])) {
                throw new \Exception('Invalid response from Billplz API');
            }

            // Add auto_submit parameter for direct payment
            $billUrl = $response['url'] . '?auto_submit=true';

            return [
                'bill_url' => $billUrl,
                'bill_id' => $response['id'],
                'collection_id' => $response['collection_id'],
                'full_response' => $response,
            ];
        } catch (\Exception $e) {
            Log::error('Billplz Create Bill Error: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);
            throw $e;
        }
    }

    /**
     * Get bill status
     * 
     * @param string $billId
     * @return array
     */
    public function getBill($billId)
    {
        try {
            return $this->makeRequest('GET', "/v3/bills/{$billId}");
        } catch (\Exception $e) {
            Log::error('Billplz Get Bill Error: ' . $e->getMessage(), [
                'bill_id' => $billId,
            ]);
            throw $e;
        }
    }

    /**
     * Verify webhook X-Signature
     * 
     * @param array $data
     * @param string $signature
     * @return bool
     */
    public function verifyWebhookSignature($data, $signature)
    {
        // Billplz X-Signature computation
        // Sort data by key and concatenate values
        ksort($data);

        $concatenatedString = '';
        foreach (['amount', 'collection_id', 'id', 'paid', 'paid_at', 'state', 'x_signature'] as $key) {
            if (isset($data[$key])) {
                $concatenatedString .= $key . $data[$key];
            }
        }

        // Remove x_signature from concatenated string
        $concatenatedString = str_replace('x_signature' . $data['x_signature'], '', $concatenatedString);

        // Compute HMAC SHA256
        $computedSignature = hash_hmac('sha256', $concatenatedString, $this->xSignatureKey);

        return hash_equals($computedSignature, $signature);
    }

    /**
     * Parse webhook callback data
     * 
     * @param array $data
     * @return array
     */
    public function parseWebhookData($data)
    {
        return [
            'bill_id' => $data['id'] ?? null,
            'collection_id' => $data['collection_id'] ?? null,
            'paid' => filter_var($data['paid'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'state' => $data['state'] ?? 'due',
            'amount' => isset($data['amount']) ? (float) $data['amount'] / 100 : 0, // Convert sen to RM
            'paid_amount' => isset($data['paid_amount']) ? (float) $data['paid_amount'] / 100 : 0,
            'paid_at' => $data['paid_at'] ?? null,
            'transaction_id' => $data['transaction_id'] ?? null,
            'transaction_status' => $data['transaction_status'] ?? null,
            'order_number' => $data['reference_2'] ?? null,
        ];
    }

    /**
     * Make HTTP request to Billplz API
     * 
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    private function makeRequest($method, $endpoint, $data = [])
    {
        $url = $this->baseUrl . $endpoint;

        $response = Http::withBasicAuth($this->apiKey, '')
            ->accept('application/json')
            ->$method($url, $data);

        if (!$response->successful()) {
            Log::error('Billplz API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'endpoint' => $endpoint,
            ]);

            throw new \Exception('Billplz API request failed: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Format mobile number untuk Billplz (Malaysian format)
     * 
     * @param string $phone
     * @return string
     */
    private function formatMobileNumber($phone)
    {
        // Remove spaces, dashes, and plus signs
        $phone = preg_replace('/[\s\-\+]/', '', $phone);

        // If starts with 60, remove it
        if (substr($phone, 0, 2) === '60') {
            $phone = '0' . substr($phone, 2);
        }

        // If doesn't start with 0, add it
        if (substr($phone, 0, 1) !== '0') {
            $phone = '0' . $phone;
        }

        return $phone;
    }

    /**
     * Get available payment gateways
     * 
     * @return array
     */
    public function getPaymentGateways()
    {
        return [
            // FPX Banks
            'fpx' => [
                'MB2U0227' => 'Maybank2u',
                'BCBB0235' => 'CIMB Clicks',
                'PBB0233' => 'Public Bank (PBe)',
                'RHB0218' => 'RHB Now',
                'HLB0224' => 'HLB Connect',
                'ABMB0212' => 'Alliance Online',
                'AMBB0209' => 'AmOnline',
                'BIMB0340' => 'Bank Islam',
                'BMMB0341' => 'Bank Muamalat',
                'BKRM0602' => 'Bank Rakyat',
            ],
            // E-Wallets
            'ewallet' => [
                'BP-BST01' => 'Boost',
                'BP-TNG01' => 'TouchNGo',
                'BP-2C2PGRB' => 'Grab',
                'BP-2C2PSHPE' => 'Shopee Pay',
            ],
            // Cards
            'card' => [
                'BP-BILLPLZ1' => 'Visa/Mastercard (Billplz)',
                'BP-PPL01' => 'PayPal',
            ],
        ];
    }
}
