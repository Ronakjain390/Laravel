<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyLogo extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','section_id', 'invoice_logo_url', 'estimate_logo_url', 'challan_logo_url', 'challan_alignment', 'invoice_alignment', 'challan_heading', 'invoice_heading', 'return_challan_heading', 'po_heading', 'return_challan_logo_url', 'po_logo_url', 'return_challan_alignment', 'po_alignment', 'return_challan_stamp', 'po_stamp','challan_stamp', 'invoice_stamp', 'barcode_accept','signature_sender', 'signature_receiver', 'signature_seller', 'signature_buyer', 'challan_templete', 'receipt_note_heading', 'receipt_note_logo_url', 'receipt_note_alignment', 'receipt_note_stamp',  'receipt_note_templete', 'signature_option_sender', 'signature_option_receiver', 'signature_option_seller', 'signature_option_buyer', 'signature_option_receipt_note', 'signature_receipt_note', 'receipt_note_template', 'estimate_heading'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
