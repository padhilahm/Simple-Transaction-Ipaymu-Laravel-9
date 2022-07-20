<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\TransactionDetail;

class TransactionController extends Controller
{
    public function store(StoreTransactionRequest $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required'
        ]);

        $carts = session()->get('cart');

        foreach ($carts as $cart) {
            $productName[] = $cart['name'];
            $productPrice[] = $cart['price'];
            $productQuantity[] = $cart['quantity'];
        }
        $productName[] = 'Fee Admin';
        $productPrice[] = 5000;
        $productQuantity[] = 1;

        $va           = '0000005751648584'; //get on iPaymu dashboard
        $secret       = 'SANDBOXD87D811D-D287-4E51-9545-466EAF7A5019-20220718173413'; //get on iPaymu dashboard

        $url          = 'https://sandbox.ipaymu.com/api/v2/payment'; //url
        $method       = 'POST'; //method

        //Request Body//
        $body['product']    = $productName; //product name
        $body['qty']        = $productQuantity; //product quantity
        $body['price']      = $productPrice; //product price
        $body['returnUrl']  = 'https://kodee.my.id/thankyou';
        $body['cancelUrl']  = 'https://kodee.my.id/cancel';
        $body['notifyUrl']  = 'https://kodee.my.id/notify';
        $body['buyerName']  = $request->name; //buyer name
        $body['buyerEmail'] = $request->email; //buyer email
        $body['buyerPhone'] = $request->phone; //buyer phone
        $body['referenceId'] = date('YmdHis'); //reference id
        $body['expired']    = 2;
        //End Request Body//

        //Generate Signature
        // *Don't change this
        $jsonBody     = json_encode($body, JSON_UNESCAPED_SLASHES);
        $requestBody  = strtolower(hash('sha256', $jsonBody));
        $stringToSign = strtoupper($method) . ':' . $va . ':' . $requestBody . ':' . $secret;
        $signature    = hash_hmac('sha256', $stringToSign, $secret);
        $timestamp    = Date('YmdHis');
        //End Generate Signature


        $ch = curl_init($url);

        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            'va: ' . $va,
            'signature: ' . $signature,
            'timestamp: ' . $timestamp
        );

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_POST, count($body));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $err = curl_error($ch);
        $ret = curl_exec($ch);
        curl_close($ch);
        if ($err) {
            echo "<pre>";
            print_r($ret);
            echo "</pre>";
            return;
        } else {
            //Response
            $ret = json_decode($ret);
            if ($ret->Status == 200) {
                $sessionId  = $ret->Data->SessionID;
                $url        =  $ret->Data->Url;
            } else {
                echo "<pre>";
                print_r($ret);
                echo "</pre>";
                return;
            }
            //End Response
        }

        DB::beginTransaction();
        // cek buyer with email
        $buyer = Buyer::where('email', $request->email)->first();
        if (!$buyer) {
            $buyer = Buyer::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone
            ]);
        }

        try {
            $transaction = Transaction::create([
                'buyer_id' => $buyer->id,
                'reference_id' => $body['referenceId'],
                'status' => '0',
                'session_id' => $sessionId,
                'url' => $url
            ]);
            foreach ($carts as $cart) {
                $product = Product::find($cart['id']);
                $product->update([
                    'quantity' => $product->quantity - $cart['quantity']
                ]);
            }

            foreach ($carts as $cart) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $cart['id'],
                    'product_name' => $cart['name'],
                    'quantity' => $cart['quantity'],
                    'price' => $cart['price']
                ]);
            }

            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'product_id' => '0',
                'product_name' => 'Fee Admin',
                'quantity' => 1,
                'price' => 5000
            ]);

            DB::commit();
            session()->forget('cart');
            return redirect($url);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    public function checkTransaction()
    {
        // get id
        $id = $_GET['id'];

        $va           = '0000005751648584'; //get on iPaymu dashboard
        $secret       = 'SANDBOXD87D811D-D287-4E51-9545-466EAF7A5019-20220718173413'; //get on iPaymu dashboard

        $url          = 'https://sandbox.ipaymu.com/api/v2/transaction'; //url
        $method       = 'POST'; //method

        //Request Body//
        $body['transactionId'] = $id; //session id
        //End Request Body//

        //Generate Signature
        // *Don't change this
        $jsonBody     = json_encode($body, JSON_UNESCAPED_SLASHES);
        $requestBody  = strtolower(hash('sha256', $jsonBody));
        $stringToSign = strtoupper($method) . ':' . $va . ':' . $requestBody . ':' . $secret;
        $signature    = hash_hmac('sha256', $stringToSign, $secret);
        $timestamp    = Date('YmdHis');
        //End Generate Signature

        $ch = curl_init($url);

        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            'va: ' . $va,
            'signature: ' . $signature,
            'timestamp: ' . $timestamp
        );

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_POST, count($body));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $err = curl_error($ch);
        $ret = curl_exec($ch);
        curl_close($ch);
        if ($err) {
            echo "<pre>";
            print_r($ret);
            echo "</pre>";
            return;
        } else {
            //Response
            $ret = json_decode($ret);
            if ($ret->Status == 200) {
                echo "<pre>";
                print_r($ret);
                echo "</pre>";
                return;
            } else {
                echo "<pre>";
                print_r($ret);
                echo "</pre>";
                return;
            }
            //End Response
        }
    }
}
