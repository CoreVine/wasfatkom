<?php

namespace Modules\Gateways\Http\Controllers;


use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Modules\Gateways\Traits\Processor;
use Modules\Gateways\Entities\PaymentRequest;
use App\Utils\CartManager;
use GuzzleHttp\Client as Client;
use App\Models\Cart;
use App\Models\Order;

class PaymobController extends Controller
{
    use Processor;

    private mixed $config_values;

    private PaymentRequest $payment;
    private User $user;

    public function __construct(PaymentRequest $payment, User $user)
    {
        $config = $this->payment_config('paymob_accept', 'payment_config');
        if (!is_null($config) && $config->mode == 'live') {
            $this->config_values = json_decode($config->live_values);
        } elseif (!is_null($config) && $config->mode == 'test') {
            $this->config_values = json_decode($config->test_values);
        }
        $this->payment = $payment;
        $this->user = $user;
    }

    protected function cURL($url, $json)
    {
        $ch = curl_init($url);

        $headers = array();
        $headers[] = 'Content-Type: application/json';

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $output = curl_exec($ch);

        curl_close($ch);
        return json_decode($output);
    }

    public function credits(Request $request): JsonResponse|RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_400, null, $this->error_processor($validator)), 400);
        }
        $data = $this->payment::where(['id' => $request['payment_id']])->where(['is_paid' => 0])->first();
        $orders = Order::count();
        $order_id = $orders+1;
        //dd($data->payment_amount);        
        $mid = "MID-25253-674"; //your merchant id
        $amount = $data->payment_amount; //eg: 100
        $currency = "EGP"; //eg: "EGP"
        $orderId = $order_id; //eg: 99, your system order ID
        $secret = "3b97bc23-fac2-4fa1-9aa0-8cb99a6b87fa";
        $path = "/?payment=".$mid.".".$orderId.".".$amount.".".$currency;
        $hash = hash_hmac( 'sha256' , $path , $secret ,false);


        $url = "https://checkout.kashier.io/?merchantId=".$mid."&orderId=".$orderId."&metaData=".json_encode($data)."&amount=".$data->payment_amount."&currency=EGP&hash=".$hash."&mode=test&merchantRedirect=".urlencode('https://zshop-eg.com/payment/paymob/callback?orderNumber='.$data->id.'')."&paymentRequestId=".$orderId."&display=ar";
        return Redirect::away($url);
    }
    public function credit(Request $request): JsonResponse|RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_400, null, $this->error_processor($validator)), 400);
        }
        $data = $this->payment::where(['id' => $request['payment_id']])->where(['is_paid' => 0])->first();
        dd($data);
        
        if (!isset($data)) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
        }

        session()->put('payment_id', $data->id);

        if ($data['additional_data'] != null) {
            $business = json_decode($data['additional_data']);
            $business_name = $business->business_name ?? "my_business";
        } else {
            $business_name = "my_business";
        }

        $payer = json_decode($data['payer_information']);
        try {
            $token = $this->getToken();
            $order = $this->createPaymentLink($token, $data, $business_name,$payer);
            //$paymentToken = $this->getPaymentToken($order, $token, $data, $payer);
        } catch (\Exception $exception) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_404), 200);
        }
        return Redirect::away($order);
        if(str_starts_with($payer->phone, '+200')){
            $phone = str_replace('+20', '', $payer->phone);
        }
        if($data->payment_method == "paymob_accept_mobile"){
            $data = [
                "source"        => ["identifier"=> $phone, "subtype"=>"WALLET"],
                "payment_token" => $paymentToken,
            ];
            $request = $this->cURL("https://accept.paymob.com/api/acceptance/payments/pay", $data);
            return Redirect::away($request->redirect_url);
        }
        
        return Redirect::away('https://accept.paymobsolutions.com/api/acceptance/iframes/' . $this->config_values->iframe_id . '?payment_token=' . $paymentToken);
    }

    public function getToken()
    {
        $response = $this->cURL(
            'https://accept.paymob.com/api/auth/tokens',
            ['api_key' => $this->config_values->api_key]
        );
        dd($response);
        return $response->token;
    }

    public function createPaymentLink($token, $payment_data, $business_name,$payer)
    {

        $items[] = [
            'name' => $business_name,
            'amount_cents' => round($payment_data->payment_amount * 100),
            'description' => 'payment ID :' . $payment_data->id,
            'quantity' => 1
        ];

        $data = [
            "amount_cents" => round($payment_data->payment_amount * 100),
            "currency" => $payment_data->currency_code,
            "full_name" => $payer->name,
            "email" => $payer->email,
            "phone_number" => $payer->phone,
            "is_live" => true,
            "payment_methods" => ['4165751','3376682'],

        ];

        $ch = curl_init('https://accept.paymob.com/api/ecommerce/payment-links');

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer '.$token;

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $output = curl_exec($ch);

        curl_close($ch);
        $response = json_decode($output);
        return $response->client_url; 

    }
    public function createOrder($token, $payment_data, $business_name)
    {
        $items[] = [
            'name' => $business_name,
            'amount_cents' => round($payment_data->payment_amount * 100),
            'description' => 'payment ID :' . $payment_data->id,
            'quantity' => 1
        ];

        $data = [
            "auth_token" => $token,
            "delivery_needed" => "false",
            "amount_cents" => round($payment_data->payment_amount * 100),
            "currency" => $payment_data->currency_code,
            "items" => $items,

        ];
        $response = $this->cURL(
            'https://accept.paymob.com/api/ecommerce/orders',
            $data
        );

        return $response;
    }

    public function getPaymentToken($order, $token, $payment_data, $payer)
    {
        $value = $payment_data->payment_amount;
        $billingData = [
            "apartment" => "N/A",
            "email" => $payer->email,
            "floor" => "N/A",
            "first_name" => $payer->name,
            "street" => "N/A",
            "building" => "N/A",
            "phone_number" => $payer->phone ?? "N/A",
            "shipping_method" => "PKG",
            "postal_code" => "N/A",
            "city" => "N/A",
            "country" => "N/A",
            "last_name" => $payer->name,
            "state" => "N/A",
        ];
        if($payment_data->payment_method == "paymob_accept_mobile"){
            $config = $this->payment_config('paymob_accept_mobile', 'payment_config');
            if (!is_null($config) && $config->mode == 'live') {
                $this->config_values = json_decode($config->live_values);
            } elseif (!is_null($config) && $config->mode == 'test') {
                $this->config_values = json_decode($config->test_values);
            }            
        } 
        $data = [
            "auth_token" => $token,
            "amount_cents" => round($value * 100),
            "expiration" => 3600,
            "order_id" => $order->id,
            "billing_data" => $billingData,
            "currency" => $payment_data->currency_code,
            "integration_id" => $this->config_values->integration_id
            // dd($config);
        ];
        $response = $this->cURL(
            'https://accept.paymob.com/api/acceptance/payment_keys',
            $data
        );
        return $response->token;
    }

    public function callbacks(Request $request): Application|JsonResponse|Redirector|\Illuminate\Contracts\Foundation\Application|RedirectResponse
    {
        $data = $request->all();
        dd($data);
        ksort($data);
        $hmac = $data['hmac'];
        $array = [
            'amount_cents',
            'created_at',
            'currency',
            'error_occured',
            'has_parent_transaction',
            'id',
            'integration_id',
            'is_3d_secure',
            'is_auth',
            'is_capture',
            'is_refunded',
            'is_standalone_payment',
            'is_voided',
            'order',
            'owner',
            'pending',
            'source_data_pan',
            'source_data_sub_type',
            'source_data_type',
            'success',
        ];
        $connectedString = '';
        foreach ($data as $key => $element) {
            if (in_array($key, $array)) {
                $connectedString .= $element;
            }
        }
        $secret = $this->config_values->hmac;
        $hased = hash_hmac('sha512', $connectedString, $secret);

        if ($hased == $hmac && $data['success'] === "true") {

            $this->payment::where(['id' => session('payment_id')])->update([
                'payment_method' => 'paymob_accept',
                'is_paid' => 1,
                'transaction_id' => session('payment_id'),
            ]);

            $payment_data = $this->payment::where(['id' => session('payment_id')])->first();

            if (isset($payment_data) && function_exists($payment_data->success_hook)) {
                call_user_func($payment_data->success_hook, $payment_data);
            }
            return $this->payment_response($payment_data, 'success');
        }
        $payment_data = $this->payment::where(['id' => session('payment_id')])->first();
        if (isset($payment_data) && function_exists($payment_data->failure_hook)) {
            call_user_func($payment_data->failure_hook, $payment_data);
        }
        return $this->payment_response($payment_data, 'fail');
    }
    public function callback(Request $request): Application|JsonResponse|Redirector|\Illuminate\Contracts\Foundation\Application|RedirectResponse
    {
        $data = $request->all();
        // dd($data);
        ksort($data);
 
        if($data['paymentStatus'] == "SUCCESS"){
            $this->payment::where(['id' => $data['orderNumber']])->update([
                'payment_method' => 'paymob_accept',
                'is_paid' => 1,
                'transaction_id' => $data['orderNumber'],
            ]);

            $payment_data = $this->payment::where(['id' => $data['orderNumber']])->first();

            if (isset($payment_data) && function_exists($payment_data->success_hook)) {
                call_user_func($payment_data->success_hook, $payment_data);
            }
            return $this->payment_response($payment_data, 'success');            
        }else{
            $payment_data = $this->payment::where(['id' => session('payment_id')])->first();
            if (isset($payment_data) && function_exists($payment_data->failure_hook)) {
                call_user_func($payment_data->failure_hook, $payment_data);
            }
            return $this->payment_response($payment_data, 'fail');
        }
    }
}
