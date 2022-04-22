<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Google\Cloud\Core\Timestamp;

class PaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payments = new Payment;
        $payments = $payments->getAll();
        return response()->json($payments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $payment = new Payment;

        $validator = Validator::make($request->all(),[
            'company_id' => 'required|string',
            'user_id' => 'required|string',
            'service_code' => 'required|numeric',
            'price' => 'required|numeric',
            'feeds' => 'required|numeric',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'validation failed',
                'data' => $validator->errors()
            ]);
        }
        else{
            $payment = $payment->create($data);
            $receipt = $this->createReceipt($payment , $request->feeds);
            return response()->json([
                'status' => true,
                'message' => 'Payment created successfully',
                'data' => [
                    'id' => $payment->id(),
                    'company_id' => $payment->data()['company_id'],
                    'user_id' => $payment->data()['user_id'],
                    'service_code' => $payment->data()['service_code'],
                    'price' => $payment->data()['price'],
                    'receipt' => [
                        'id' => $receipt->id(),
                        'payment_id' => $receipt->data()['payment_id'],
                        'feeds' => $receipt->data()['feeds'],
                        'total' => $receipt->data()['total'],
                        'date' => $receipt->data()['date']->get()->format('Y-m-d H:i:s'),
                    ]
                ]
            ]);
        }
        
    }

    /**
     * Create receipt for payment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createReceipt($payment , $feeds)
    {
        $receipt = new Receipt();
        $total = (int) $payment['price'] + (int) $feeds;
        $now_date = new Timestamp(new \DateTime('now'));

        $receipt = $receipt->create([
            'payment_id' => $payment->id(),
            'feeds' => $feeds,
            'total' => $total,
            'date' => $now_date
        ]);

        return $receipt;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $payment = new Payment();
        $payment = $payment->find($id);
        if ($payment)
            return $payment;
        else
            return response()->json(['error' => 'Payment not found']);
    }

    /**
     * get all Receipt by payment id.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function paymentReceipt($id)
    {
        $payment = new Receipt();
        $payment = $payment->payment($id);
        if ($payment)
            return $payment;
        else
            return response()->json(['error' => 'Payment not found']);
    }

}
