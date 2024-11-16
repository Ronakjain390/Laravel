<?php
namespace App\Services\PDFServices;

use finfo;
use CURLFile;
use Exception;
use Illuminate\Support\Facades\Http;

use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Aws\S3\S3Client;
use App\Models\Challan;
use App\Models\Invoice;
use App\Models\CompanyLogo;
use App\Mail\ChallanPDFMail;
use League\Flysystem\Config;
use App\Models\ReturnChallan;
use App\Models\ReturnInvoice;
use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;

class PDFWhatsAppService
{
    private $baseUrl;
    private $token;
    private $version;
    private $phoneId;

    public function __construct()
    {
        $this->baseUrl = config('services.whatsapp.api_base_url');
        $this->token = config('services.whatsapp.api_token');
        $this->version = config('services.whatsapp.api_version');
        $this->phoneId = config('services.whatsapp.phone_number_id');
    }
    // public function sendChallanOnWhatsApp(Challan $challan, $pdfUrl, $recipientPhoneNumber)
    // public function sendChallanOnWhatsApp(Challan $challan, $pdfUrl, $recipientPhoneNumber)
    // {

    //     $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
    //     $pdfData = CompanyLogo::where('user_id', $userId)->first();
    //     $url = "https://graph.facebook.com/v18.0/116126921474107/messages";
    //     $pdf = Storage::disk('s3')->temporaryUrl($challan->pdf_url, now()->addMinutes(5));
    //     $challanId = $challan->id;

    //     $url = 'https://graph.facebook.com/v18.0/116126921474107/messages';
    //     // sender_challan_demo_13april24
    //         // Data for the request
    //         $data = [
    //             "messaging_product" => "whatsapp",
    //             "to" => $challan->receiverUser->phone,
    //             "type" => "template",
    //             "template" => [
    //                 "name" => "sender_sentchallan24",
    //                 "language" => [
    //                     "code" => "en_US"
    //                 ],
    //                 "components" => [
    //                     [
    //                         "type" => "header",
    //                         "parameters" => [
    //                             [
    //                                 "type" => "document",
    //                                 "document" => [
    //                                     "link" => $pdf,
    //                                     "filename" => "Challan-{$challan->challan_series}-{$challan->series_num}.pdf"
    //                                 ]
    //                             ]
    //                         ]
    //                     ],
    //                     [
    //                         "type" => "body",
    //                         "parameters" => [
    //                             [
    //                                 "type" => "text",
    //                                 "text" => ucfirst($challan->receiverUser->name),
    //                             ],
    //                             [
    //                                 "type" => "text",
    //                                 "text" => "Challan",
    //                             ],
    //                             [
    //                                 "type" => "text",
    //                                 "text" =>  ucfirst($challan->senderUser->name),
    //                             ]
    //                         ],
    //                     ],
    //                     [
    //                         "type" => "button",
    //                         "index" => "0",
    //                         "sub_type" => "URL",
    //                         "parameters" => [
    //                             [
    //                                 "type" => "text",
    //                                 "text" => isset($challanId) ? $challanId : 'default value'
    //                             ]
    //                         ]
    //                     ]
    //                 ]
    //             ]
    //         ];

    //     // dd($data) ;       // Make the HTTP request
    //         $response = Http::withHeaders([
    //             'Content-Type' => 'application/json',
    //             'Authorization' => 'Bearer ' . $this->token,
    //         ])->post($url, $data);
    //         //  dd($response->body());

    //         return true; // Indicate that the WhatsApp message was sent successfully
    //     }

    public function sendChallanOnWhatsApp($phoneNumbers, $pdfUrl,$challanNo, $challanId, $receiverUser,$senderUser,   $heading )
    {
        // dd($phoneNumbers, $pdfUrl, $senderUser, $receiverUser, $challanNo, $challanId, $heading);
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $pdfData = CompanyLogo::where('user_id', $userId)->first();
        $url = "https://graph.facebook.com/v18.0/116126921474107/messages";
        $pdf = Storage::disk('s3')->temporaryUrl($pdfUrl, now()->addMinutes(5));
        // $challanId = $challan->id;

        if($heading == 'Challan'){
            $heading = $pdfData->challan_heading ?: "Challan";
        } elseif($heading == 'Invoice') {
            $heading = $pdfData->invoice_heading ?: "Invoice";
        }elseif($heading == 'Goods Receipt') {
            $heading = $pdfData->receipt_note_heading ?: "Goods Receipt";
        }

        foreach ($phoneNumbers as $phoneNumber) {
            $data = [
                "messaging_product" => "whatsapp",
                "to" => $phoneNumber,
                "type" => "template",
                "template" => [
                    // "name" => "sender_sentchallan24",
                    "name" => "send_document_all",
                    "language" => [
                        "code" => "en_US"
                    ],
                    "components" => [
                        [
                            "type" => "header",
                            "parameters" => [
                                [
                                    "type" => "document",
                                    "document" => [
                                        "link" => $pdf,
                                        "filename" => "Challan-{$challanNo}.pdf"
                                    ]
                                ]
                            ]
                        ],
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => ucfirst($receiverUser),
                                ],
                                [
                                    "type" => "text",
                                    "text" => $heading,
                                ],
                                [
                                    "type" => "text",
                                    "text" =>  ucfirst($senderUser),
                                ]
                            ],
                        ],
                        [
                            "type" => "button",
                            "index" => "0",
                            "sub_type" => "URL",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => $this->getDocumentTypeFromHeading($heading) . '/' . $challanId
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ])->post($url, $data);
        }

        return true; // Indicate that the WhatsApp message was sent successfully
    }
    private function getDocumentTypeFromHeading($heading)
    {
        switch (strtolower($heading)) {
            case 'challan':
                return 'challan';
            case 'invoice':
                return 'invoice';
            case 'goods receipt':
                return 'goods-receipt';
            case 'purchase order':
                return 'purchase-order';
            default:
                return 'document'; // fallback
        }
    }

    public function sendCommentOnWhatsApp($phone, $senderUser, $challanNo, $status_comment, $pdfUrl, $heading)
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $pdfData = CompanyLogo::where('user_id', $userId)->first();

        if($heading == 'Challan'){
            $heading = $pdfData->challan_heading ?: "Challan";
        } elseif($heading == 'Invoice') {
            $heading = $pdfData->invoice_heading ?: "Invoice";
        } elseif($heading == 'Return Challan') {
            $heading = $pdfData->return_challan_heading ?: "Return Challan";
        } elseif($heading == 'Purchase Order') {
            $heading = $pdfData->po_heading ?: "Purchase Order";
        }elseif($heading == 'Goods Receipt') {
            $heading = $pdfData->receipt_note_heading ?: "Goods Receipt";
        }

        $url = "https://graph.facebook.com/v18.0/116126921474107/messages";
        $pdf = Storage::disk('s3')->temporaryUrl($pdfUrl, now()->addMinutes(5));

        $cha = $heading . " " . $challanNo;
        // dd($cha);

        $phoneNumbers = [$phone];

        if (!empty($challan->additional_phone_number)) {
            $phoneNumbers[] = $challan->additional_phone_number;
        }

        foreach ($phoneNumbers as $phoneNumber) {
            $data = [
                "messaging_product" => "whatsapp",
                "to" => $phoneNumber,
                "type" => "template",
                "template" => [
                    "name" => "new_comment_added",
                    "language" => [
                        "code" => "en_US"
                    ],
                    "components" => [
                        [
                            "type" => "header",
                            "parameters" => [
                                [
                                    "type" => "document",
                                    "document" => [
                                        "link" => $pdf,
                                        "filename" => "Challan-{$challanNo}.pdf"
                                    ]
                                ]
                            ]
                        ],
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => ucfirst($senderUser),
                                ],
                                [
                                    "type" => "text",
                                    "text" => ($status_comment),
                                ],
                                [
                                    "type" => "text",
                                    "text" => ($cha),
                                ]
                            ],
                        ],
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ])->post($url, $data);
        }
        // dd($response->body());
        return true; // Indicate that the WhatsApp message was sent successfully
    }

    // GRN Send on WhatsApp
    public function sendGrnOnWhatsApp($phoneNumbers, $pdfUrl,$challanNo, $challanId, $receiverUser,$senderUser,   $heading )
    {
        // dd($phoneNumbers, $pdfUrl, $senderUser, $receiverUser, $challanNo, $challanId, $heading);
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $pdfData = CompanyLogo::where('user_id', $userId)->first();
        $url = "https://graph.facebook.com/v18.0/116126921474107/messages";
        $pdf = Storage::disk('s3')->temporaryUrl($pdfUrl, now()->addMinutes(5));
        // $challanId = $challan->id;

        if($heading == 'Challan'){
            $heading = $pdfData->challan_heading ?: "Challan";
        }elseif($heading == 'Goods Receipt') {
            $heading = $pdfData->receipt_note_heading ?: "Goods Receipt";
        }

        foreach ($phoneNumbers as $phoneNumber) {
            $data = [
                "messaging_product" => "whatsapp",
                "to" => $phoneNumber,
                "type" => "template",
                "template" => [
                    "name" => "grn_template",
                    "language" => [
                        "code" => "en"
                    ],
                    "components" => [
                        [
                            "type" => "header",
                            "parameters" => [
                                [
                                    "type" => "document",
                                    "document" => [
                                        "link" => $pdf,
                                        "filename" => "Challan-{$challanNo}.pdf"
                                    ]
                                ]
                            ]
                        ],
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => ucfirst($receiverUser),
                                ],
                                [
                                    "type" => "text",
                                    "text" => $heading,
                                ],
                                [
                                    "type" => "text",
                                    "text" =>  ucfirst($senderUser),
                                ]
                            ],
                        ],
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ])->post($url, $data);
        }

        // dd($response->body());
        return true; // Indicate that the WhatsApp message was sent successfully
    }
    // GRN Comment
    public function sendGrnCommentOnWhatsApp($phone, $senderUser, $challanNo, $status_comment, $pdfUrl, $heading)
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $pdfData = CompanyLogo::where('user_id', $userId)->first();

        if($heading == 'Challan'){
            $heading = $pdfData->challan_heading ?: "Challan";
        } elseif($heading == 'Invoice') {
            $heading = $pdfData->invoice_heading ?: "Invoice";
        } elseif($heading == 'Return Challan') {
            $heading = $pdfData->return_challan_heading ?: "Return Challan";
        } elseif($heading == 'Purchase Order') {
            $heading = $pdfData->po_heading ?: "Purchase Order";
        }elseif($heading == 'Goods Receipt') {
            $heading = $pdfData->receipt_note_heading ?: "Goods Receipt";
        }

        $url = "https://graph.facebook.com/v18.0/116126921474107/messages";
        $pdf = Storage::disk('s3')->temporaryUrl($pdfUrl, now()->addMinutes(5));

        $cha = $heading . " " . $challanNo;
        // dd($cha);

        $phoneNumbers = [$phone];

        if (!empty($challan->additional_phone_number)) {
            $phoneNumbers[] = $challan->additional_phone_number;
        }

        foreach ($phoneNumbers as $phoneNumber) {
            $data = [
                "messaging_product" => "whatsapp",
                "to" => $phoneNumber,
                "type" => "template",
                "template" => [
                    "name" => "grn_comment",
                    "language" => [
                        "code" => "en"
                    ],
                    "components" => [

                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => ucfirst($senderUser),
                                ],
                                [
                                    "type" => "text",
                                    "text" => ($status_comment),
                                ],
                                [
                                    "type" => "text",
                                    "text" => ($cha),
                                ]
                            ],
                        ],
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ])->post($url, $data);
        }
        // dd($response->body());
        return true;
    }

    public function sendSFPOnWhatsApp($phone, $senderUser, $pdfUrl, $receiverUser, $challanNo)
    {
        // dd($phone, $senderUser, $pdfUrl, $receiverUser, $challanNo);
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $pdfData = CompanyLogo::where('user_id', $userId)->first();

        // if($heading == 'Challan'){
        //     $heading = $pdfData->challan_heading ?: "Challan";
        // } elseif($heading == 'Invoice') {
        //     $heading = $pdfData->invoice_heading ?: "Invoice";
        // } elseif($heading == 'Return Challan') {
        //     $heading = $pdfData->return_challan_heading ?: "Return Challan";
        // } elseif($heading == 'Purchase Order') {
        //     $heading = $pdfData->po_heading ?: "Purchase Order";
        // }

        $url = "https://graph.facebook.com/v18.0/116126921474107/messages";
        $pdf = Storage::disk('s3')->temporaryUrl($pdfUrl, now()->addMinutes(5));

        // $cha = $heading . " " . $challanNo;
        // dd($cha);

        $phoneNumbers = [$phone];

        if (!empty($challan->additional_phone_number)) {
            $phoneNumbers[] = $challan->additional_phone_number;
        }

        foreach ($phoneNumbers as $phoneNumber) {
            $data = [
                "messaging_product" => "whatsapp",
                "to" => $phoneNumber,
                "type" => "template",
                "template" => [
                    "name" => "sfp_sender",
                    "language" => [
                        "code" => "en_US"
                    ],
                    "components" => [
                        [
                            "type" => "header",
                            "parameters" => [
                                [
                                    "type" => "document",
                                    "document" => [
                                        "link" => $pdf,
                                        "filename" => "Challan-{$challanNo}.pdf"
                                    ]
                                ]
                            ]
                        ],
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => ucfirst($receiverUser),
                                ],
                                [
                                    "type" => "text",
                                    "text" => ($senderUser),
                                ],
                            ],
                        ],
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ])->post($url, $data);
        }
        // dd($response->body());
        return true; // Indicate that the WhatsApp message was sent successfully
    }


    // public function sendChallanOnWhatsApp(Challan $challan, $pdfUrl, $recipientPhoneNumber)
    // {
    //     // dd($challan->id, $pdfUrl, $recipientPhoneNumber);
    //     // https://graph.facebook.com/v17.0/954252568932433

    //     //         curl -i -X POST `
    //     //   https://graph.facebook.com/v18.0/116126921474107/messages `
    //     //   -H 'Authorization: Bearer EAANj4z1lsFEBO72NZC8Hm1pLJZBc5y3Qj95UfAl7fwTFlPWNdpgNODqPoYmOOXAOxNcDTRGQEXC8hU6DSGaqUZAziv0Pm4tZBv6yS4VQHpCTDR5P5jNHlbo1uFvDvJlyRjcfXaypUYKD09XGEn2a96X1a80fX7ZAZBouZBSPMFsMJiUaNcUzZBMN9NOCvDXbkT372FNF1BhEhJkBIAiMv1wVPFqfwRKDTIOaGlIZD' `
    //     //   -H 'Content-Type: application/json' `
    //     //   -d '{ \"messaging_product\": \"whatsapp\", \"to\": \"919828773809\", \"type\": \"template\", \"template\": { \"name\": \"hello_world\", \"language\": { \"code\": \"en_US\" } } }'
    // $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
    // $pdfData = CompanyLogo::where('user_id', $userId)->first();
    // $url = "https://graph.facebook.com/v18.0/116126921474107/messages";
    // $pdf = Storage::disk('s3')->temporaryUrl($challan->pdf_url, now()->addMinutes(5));
    // $challanId = $challan->id;
    // $response = Http::withHeaders([
    //     'Authorization' => 'Bearer ' . $this->token,
    // ])->post($url, [
    //     'messaging_product' => 'whatsapp',
    //     'recipient_type' => 'individual',
    //     'to' => $challan->receiverUser->phone,
    //     'type' => 'template',
    //     'template' => [
    //         'name' => 'send_challan',
    //         'language' => [
    //             'code' => 'en_GB',
    //         ],
    //         'components' => [
    //             [
    //                 'type' => 'header',
    //                 'parameters' => [
    //                     [
    //                         'type' => 'document',
    //                         'document' => [
    //                             'link' => $pdf,
    //                             'filename' => "Challan-{$challan->challan_series}-{$challan->series_num}.pdf"
    //                         ],
    //                     ],
    //                 ],
    //             ],
    //             [
    //                 'type' => 'body',
    //                 'parameters' => [
    //                     [
    //                         'type' => 'text',
    //                         'text' => ucfirst($challan->receiverUser->name),
    //                     ],
    //                     [
    //                         'type' => 'text',
    //                         'text' => ucfirst($challan->senderUser->name),
    //                     ],
    //                     [
    //                         'type' => 'text',
    //                         'text' => $pdfData->challan_heading ?? "Challan",
    //                     ],
    //                 ],
    //             ],
    //         ],
    //     ],

    // ]);
    //     //  dd($response->body());

    //     return true; // Indicate that the WhatsApp message was sent successfully
    // }
    // url('/accept-challan/'.urlencode($challan->id)),
    public function sendReturnChallanOnWhatsApp(ReturnChallan $returnChallan, $pdfUrl, $recipientPhoneNumber)
    {
        // Use your preferred WhatsApp API to send the PDF via WhatsApp
        // For example, you can use Twilio WhatsApp API, Vonage (Nexmo) WhatsApp API, or any other WhatsApp API provider.

        // Here's a placeholder for sending the PDF on WhatsApp
        // Replace this with your actual WhatsApp API integration code

        // $message = 'Please find the Challan PDF for your reference: ' . $pdfUrl;
        // $whatsAppApi->sendMessage($recipientPhoneNumber, $message);

        return true; // Indicate that the WhatsApp message was sent successfully
    }

    public function sendInvoiceOnWhatsApp(Invoice $challan, $pdfUrl, $recipientPhoneNumber)
    {
        // Use your preferred WhatsApp API to send the PDF via WhatsApp
        // For example, you can use Twilio WhatsApp API, Vonage (Nexmo) WhatsApp API, or any other WhatsApp API provider.

        // Here's a placeholder for sending the PDF on WhatsApp
        // Replace this with your actual WhatsApp API integration code

        // $message = 'Please find the Invoice PDF for your reference: ' . $pdfUrl;
        // $whatsAppApi->sendMessage($recipientPhoneNumber, $message);

        return true; // Indicate that the WhatsApp message was sent successfully
    }

    public function sendReturnInvoiceOnWhatsApp(ReturnInvoice $returnInvoice, $pdfUrl, $recipientPhoneNumber)
    {
        // Use your preferred WhatsApp API to send the PDF via WhatsApp
        // For example, you can use Twilio WhatsApp API, Vonage (Nexmo) WhatsApp API, or any other WhatsApp API provider.

        // Here's a placeholder for sending the PDF on WhatsApp
        // Replace this with your actual WhatsApp API integration code

        // $message = 'Please find the Invoice PDF for your reference: ' . $pdfUrl;
        // $whatsAppApi->sendMessage($recipientPhoneNumber, $message);

        return true; // Indicate that the WhatsApp message was sent successfully
    }
}



// class WhatsappApi
// {
//     private $baseUrl;
//     private $token;
//     private $version;
//     private $phoneId;

//     public function __construct()
//     {
//         $this->baseUrl = env('WHATSAPP_API_BASE_URL');
//         $this->token = env('WHATSAPP_API_TOKEN');
//         $this->version = env('WHATSAPP_API_VERSION');
//         $this->phoneId = env('WHATSAPP_PHONE_NUMBER_ID');
//     }


//     public function sendWelcomeMessage($phoneNumber, $templateName, $imageUrl)
//     {
//         $url = "https://graph.facebook.com/$this->version/$this->phoneId/messages";

//         $response = Http::withHeaders([
//             'Authorization' => 'Bearer ' . $this->token,
//         ])->post($url, [
//             'messaging_product' => 'whatsapp',
//             'recipient_type' => 'individual',
//             'to' => $phoneNumber,
//             'type' => 'template',
//             'template' => [
//                 'name' => $templateName,
//                 'language' => [
//                     'code' => 'en_GB',
//                 ],
//                 'components' => [
//                     [
//                         'type' => 'header',
//                         'parameters' => [
//                             [
//                                 'type' => 'image',
//                                 'image' => [
//                                     'link' => $imageUrl,
//                                 ],
//                             ],
//                         ],
//                     ],
//                 ],
//             ],
//         ]);
//         // dd($response->body());
//         return $response;
//     }
//     public function sendChallanMessage($phoneNumber, $templateName, $imageUrl, $sender, $receiver, $pdf_name, $challan_url,$pdf_type)
//     {
//         // dd($phoneNumber, $templateName, $imageUrl,$sender,$receiver);
//         $url = "https://graph.facebook.com/$this->version/$this->phoneId/messages";

//         $response = Http::withHeaders([
//             'Authorization' => 'Bearer ' . $this->token,
//         ])->post($url, [
//             'messaging_product' => 'whatsapp',
//             'recipient_type' => 'individual',
//             'to' => $phoneNumber,
//             'type' => 'template',
//             'template' => [
//                 'name' => $templateName,
//                 'language' => [
//                     'code' => 'en_GB',
//                 ],
//                 'components' => [
//                     [
//                         'type' => 'header',
//                         'parameters' => [
//                             [
//                                 'type' => 'document',
//                                 'document' => [
//                                     'link' => $imageUrl,
//                                     'filename' => $pdf_name,
//                                 ],
//                             ],
//                         ],
//                     ],
//                     [
//                         'type' => 'body',
//                         'parameters' => [
//                             [
//                                 'type' => 'text',
//                                 'text' => ucfirst($receiver),
//                             ],
//                             [
//                                 'type' => 'text',
//                                 'text' => ucfirst($sender),
//                             ],
//                             [
//                                 'type' => 'text',
//                                 'text' => ucfirst($pdf_type),
//                             ],
//                         ],
//                     ],
//                 ],
//             ],

//         ]);

//         // dd($response->body());
//         return $response;
//     }

//     private function handleResponse($response)
//     {
//         if ($response->ok()) {
//             return [
//                 'success' => true,
//                 'message' =>  $response->json(),
//             ];
//         }

//         $error = $response->json('error') ?? $response->throw()->getMessage();

//         return [
//             'success' => false,
//             'message' => $error,
//         ];
//     }
// }
