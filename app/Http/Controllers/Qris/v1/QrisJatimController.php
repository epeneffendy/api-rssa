<?php

namespace App\Http\Controllers\Qris\v1;

use App\Http\Controllers\Controller;
use App\Models\Qris\v1\QrisJatimCheckStatusQrisRequest;
use App\Models\Qris\v1\QrisJatimGenerateRequest;
use App\Models\Qris\v1\QrisJatimGenerateResponse;
use App\Models\Qris\v1\QrisJatimPaymentRequest;
use App\Models\Qris\v1\QrisJatimPaymentResponse;
use App\Services\Qris\v1\QrisJatimService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Created by PhpStorm.
 * User: ITKOM-EFFENDY
 * Date: 01/04/2024
 * Time: 14:15
 */
class QrisJatimController extends Controller
{
    protected $settings;

    public function __construct()
    {
        $this->settings = array(
            'apiUrl' => env('BANK_JATIM_URL', 'https://jatimva.bankjatim.co.id/'),
            'merchant' => env('BANK_JATIM_MERCHANT', '9360011400001347721'),
            'hashcode' => env('BANK_JATIM_HASCODE', 'Y1MACZ4B5R'),
            'terminalUser' => env('BANK_JATIM_TERMINAL_USER', 'ID2024310949969'),
            'username' => env('BANK_JATIM_USERNAME', 'RSUDDRSA3206'),
            'password' => env('BANK_JATIM_PASSWORD', '111111'),
        );
    }

    public function GenerateQris(QrisJatimService $qrisJatimService, Request $request)
    {
        $validator = validator($request->all(), [
            'billNumber' => ['required', 'string', 'max:20'],
            'purposetrx' => ['required', 'string', 'max:28'],
            'storelabel' => ['required', 'string', 'max:100'],
            'customerlabel' => ['nullable', 'string', 'max:100'],
            'terminalUser' => ['nullable', 'string', 'max:30'],
            'expiredDate' => ['nullable', 'string', 'max:30'],
            'amount' => ['nullable', 'string'],
        ], [], [
            'billNumber' => 'Bill Number',
            'purposetrx' => 'Purpose Set Trx',
            'storelabel' => 'Store Label',
            'customerlabel' => 'Customer Label',
            'terminalUser' => 'Terminal User',
            'expiredDate' => 'Expired Date',
            'amount' => 'Amount',
        ]);

        try {
            $validator->validate();
            $data = new QrisJatimGenerateRequest($request->all());
            $generateHashCode = $this->generateHashCode($this->settings['merchant'], $data->getbillNumber(), $data->getterminalUser(), $this->settings['hashcode']);

            $data->setmerchantPan($this->settings['merchant']);
            $data->sethashcodeKey('3C569A8C898FD24243CE0FABD4B6B60E30267C7409A5A06F859416C325482964');
//            $data->sethashcodeKey(strtoupper($generateHashCode['hashCode']));

            $response = $qrisJatimService->generateApiQris($data);
            return response()->json($response, 200);
        } catch (ValidationException $e) {
            dd($e);
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function PaymentQris(Request $request){
        $validator = validator($request->all(), [
            'billNumber' => ['required', 'string', 'max:20'],
            'purposetrx' => ['required', 'string', 'max:28'],
            'storelabel' => ['required', 'string', 'max:100'],
            'customerlabel' => ['nullable', 'string', 'max:100'],
            'terminalUser' => ['nullable', 'string', 'max:30'],
            'amount' => ['nullable', 'string'],
            'core_reference'=>['nullable', 'string'],
            'customerPan'=>['nullable', 'string'],
            'merchantPan'=>['nullable', 'string'],
            'pjsp'=>['nullable', 'string'],
            'invoice_number'=>['nullable', 'string'],
            'transactionDate'=>['nullable', 'string'],
        ]);

        try{
            $validator->validate();
            $data = new QrisJatimPaymentRequest($request->all());
            $result = new QrisJatimPaymentResponse($request->all());

            $response = $result->toArray();
            return response()->json($response, 200);
        } catch (ValidationException $e) {
            dd($e);
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function CheckStatusQris(QrisJatimService $qrisJatimService, Request $request)
    {
        $validator = validator($request->all(), [
            'username' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'max:28'],
            'invoice_number' => ['required', 'string', 'max:100'],
        ]);

        try {
            $validator->validate();
            $data = new QrisJatimCheckStatusQrisRequest($request->all());
            $response = $qrisJatimService->checkStatusQrisPayment($data);

            return response()->json($response, 200);
        } catch (ValidationException $e) {

        } catch (\Exception $e) {
        }
    }

    public function generateHashCode($marchant, $billNumber, $terminalUser, $hashCode)
    {
        $stringToSign = $marchant . $billNumber  . $terminalUser . $hashCode;
        return [
            'hashCode' => hash('sha256',$stringToSign)
        ];
    }


}
