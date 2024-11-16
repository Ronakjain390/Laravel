<?php

namespace App\Http\Controllers\WhatsApp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    public function sendMedia(Request $request)
    {
        // $phone = $request->input('phone');
        // $fileUrl = $request->input('fileUrl');
        // $caption = $request->input('caption');
        // $filename = $request->input('filename');
        // $message = $request->input('message');

        $phone = 917042935808;
        $fileUrl = "https://images.unsplash.com/photo-1580273916550-e323be2ae537?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxzZWFyY2h8MXx8Ym13JTIwY2FyfGVufDB8fDB8fA%3D%3D&auto=format&fit=crop&w=500&q=60";
        $caption = "send trial";
        $filename = "car";
        $message = "test succefull";

        $whatsapp = new WhatsappApi;
        $response = $whatsapp->sendMedia($phone, $fileUrl, $caption, $filename, $message);

        if ($response['success']) {
            return response()->json(['status' => 'success', 'message' => 'Media sent successfully']);
        } else {
            return response()->json(['status' => 'error', 'message' => $response['message']]);
        }
    }

    public function sendMessage($phone)
    {
        // $phone = 917042935808;
        // $fileUrl = $request->input('fileUrl');
        // $caption = $request->input('caption');
        // $filename = $request->input('filename');
        // $message = $request->input('message');

        $whatsapp = new WhatsappApi;
        $response = $whatsapp->sendWelcomeMessage($phone, 'signup_welcome_message', 'https://theparchi.com/Vector.png');
        if ($response->successful()) {
            return response()->json(['status' => 'success', 'message' => 'Message sent successfully']);
        } else {
            return response()->json(['status' => 'error', 'message' => $response['message']]);
        }
    }

    public function sendChallanMessage($phone, $doc, $sender, $receiver, $pdf_name, $challan_url)
    {
        // $phone = 917042935808;
        // $fileUrl = $request->input('fileUrl');
        // $caption = $request->input('caption');
        // $filename = $request->input('filename');
        // $message = $request->input('message');
        // dd($phone);
        $customuser_logo = DB::table('customusers_logo')
            ->where('user_id', Auth::guard()->user()->seller_id ?? Auth::guard()->user()->id)
            ->first();

        if ($phone != null) {
            // dd('91' . $phone, 'send_challan', $doc, $sender, $receiver, $pdf_name, $challan_url, $customuser_logo->s_and_r_pdf ?? "Challan");
            $whatsapp = new WhatsappApi;
            $response = $whatsapp->sendChallanOnWhatsApp('91' . $phone, 'send_challan', $doc, $sender, $receiver, $pdf_name, $challan_url, $customuser_logo->s_and_r_pdf ?? "Challan");
            // dd($response->body());

            if ($response->successful()) {
                return response()->json(['status' => 'success', 'message' => 'Challan sent successfully']);
            } else {
                return response()->json(['status' => 'error', 'message' => $response['message']]);
            }
        } else {
            return true;
        }
    }

    public function sendInvoiceMessage($phone, $doc, $sender, $receiver, $pdf_name, $challan_url)
    {
        // $phone = 917042935808;
        // $fileUrl = $request->input('fileUrl');
        // $caption = $request->input('caption');
        // $filename = $request->input('filename');
        // $message = $request->input('message');
        // dd($phone);
        $customuser_logo = DB::table('customusers_logo')
            ->where('user_id', Auth::guard()->user()->seller_id ?? Auth::guard()->user()->id)
            ->first();

        if ($phone != null) {
            // dd('91' . $phone, 'send_challan', $doc, $sender, $receiver, $pdf_name, $challan_url, $customuser_logo->s_and_r_pdf ?? "Challan");
            $whatsapp = new WhatsappApi;
            $response = $whatsapp->sendChallanMessage('91' . $phone, 'send_challan', $doc, $sender, $receiver, $pdf_name, $challan_url, $customuser_logo->s_and_b_pdf ?? "Invoice");
            // dd($response->body());

            if ($response->successful()) {
                return response()->json(['status' => 'success', 'message' => 'Invoice sent successfully']);
            } else {
                return response()->json(['status' => 'error', 'message' => $response['message']]);
            }
        } else {
            return true;
        }
    }
}
