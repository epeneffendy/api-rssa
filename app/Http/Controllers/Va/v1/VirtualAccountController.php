<?php

namespace App\Http\Controllers\Va\v1;

use App\Http\Controllers\Controller;
use App\Models\Va\v1\VirtualAccountJatimRequest;
use App\Models\Va\v1\VirtualAccountJatimResponse;
use App\Services\Va\v1\VirtualAccountJatimService;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

/**
 * Created by PhpStorm.
 * User: USER
 * Date: 14/05/2024
 * Time: 9:36
 */
class VirtualAccountController extends Controller
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

    public function CreateVirtualAccountFull( Request $request)
    {
        $validator = validator($request->all(), [
            'VirtualAccount' => ['required', 'string', 'max:16'],
            'Nama' => ['required', 'string', 'max:100'],
            'TotalTagihan' => ['required', 'string'],
            'TanggalExp' => ['required', 'date_format:YYYYMMDD'],
            'Berita1' => ['nullable', 'string', 'max:50'],
            'Berita2' => ['nullable', 'string', 'max:50'],
            'Berita3' => ['nullable', 'string', 'max:50'],
            'Berita4' => ['nullable', 'string', 'max:50'],
            'Berita5' => ['nullable', 'string', 'max:50'],
            'FlagProses' => ['nullable', 'string', 'max:1'],
        ], [], [
            'VirtualAccount' => 'Virtual Account',
            'Nama' => 'Nama',
            'TotalTagihan' => 'Store Label',
            'TanggalExp' => 'Tanggal Exp',
            'Berita1' => 'Berita 1',
            'Berita2' => 'Berita 2',
            'Berita3' => 'Berita 3',
            'Berita4' => 'Berita 4',
            'Berita5' => 'Berita 5',
        ]);
    }

    public function CallbackVa(VirtualAccountJatimService $virtualAccountJatimService, Request $request)
    {
        $validator = validator($request->all(), [
            'VirtualAccount' => ['required', 'string', 'max:16'],
            'Nama' => ['required', 'string', 'max:100'],
            'Amount' => ['required', 'numeric'],
            'References' => ['required', 'string','max:50'],
            'Tanggal' => ['required', 'date_format:Y-m-d H:i:s'],
        ], [], [
            'VirtualAccount' => 'Virtual Account',
            'Nama' => 'Nama',
            'Amount' => 'Amount',
            'References' => 'References',
            'Tanggal' => 'Tanggal',
        ]);

        try{
//            $validator->validate();
            $data = new VirtualAccountJatimRequest($request->all());
            $result = new VirtualAccountJatimResponse($request->all());

//            $proses = $virtualAccountJatimService->updatePayment($data, $result);

//            $response = $proses->toArray();
            $response = $result->toArray();
            return response()->json($response, 200);
        } catch (ValidationException $e) {
            dd($e);
        } catch (\Exception $e) {
            dd($e);
        }
    }

}