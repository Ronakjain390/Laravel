<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
   
    public function run(): void
    {
        // Templates
        $templates = [
            ['id' => 1, 'name' => 'Create Challan', 'page_name' => 'create_challan'],
            ['id' => 4, 'name' => 'Sent Challan', 'page_name' => 'sent_challan'],
            ['id' => 5, 'name' => 'Received Challan', 'page_name' => 'received_challan'],
            ['id' => 6, 'name' => 'Add Receiver', 'page_name' => 'add_receiver'],
            ['id' => 7, 'name' => 'All Receiver', 'page_name' => 'all_receiver'],
            ['id' => 8, 'name' => 'Challan Prefix', 'page_name' => 'challan_series_no'],
            ['id' => 9, 'name' => 'Create Return Challan', 'page_name' => 'create_return_challan'],
            ['id' => 10, 'name' => 'Sent Return Challan', 'page_name' => 'sent_return_challan'],
            ['id' => 11, 'name' => 'Received Return Challan', 'page_name' => 'received_return_challan'],
            ['id' => 12, 'name' => 'Return Challan Prefix', 'page_name' => 'return_challan_series_no'],
            ['id' => 13, 'name' => 'Create Invoice', 'page_name' => 'create_invoice'],
            ['id' => 14, 'name' => 'Sent Invoice', 'page_name' => 'sent_invoice'],
            ['id' => 15, 'name' => 'Purchase Order', 'page_name' => 'purchase_order_seller'],
            ['id' => 16, 'name' => 'Add Buyer', 'page_name' => 'add_buyer'],
            ['id' => 17, 'name' => 'All Buyer', 'page_name' => 'all_buyer'],
            ['id' => 18, 'name' => 'Invoice Prefix', 'page_name' => 'invoice_series_no'],
            ['id' => 19, 'name' => 'All Invoice', 'page_name' => 'all_invoice'],
            ['id' => 20, 'name' => 'Purchase Order', 'page_name' => 'purchase_order'],
            ['id' => 21, 'name' => 'All Seller', 'page_name' => 'all_seller'],
            ['id' => 22, 'name' => 'Add Seller', 'page_name' => 'add_seller'],
            ['id' => 23, 'name' => 'New Purchase Oder', 'page_name' => 'new_purchase_order'],
        ];

        // Seed the templates table
        foreach ($templates as $template) {
            DB::table('templates')->insert([
                'id' => $template['id'],
                'template_name' => $template['name'],
                'template_page_name' => $template['page_name'],
                'status' => 'active',
                'template_image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Sections
        $sections = [
            ['id' => 1, 'section' => 'Challan', 'status' => 'active'],
            ['id' => 2, 'section' => 'Invoice', 'status' => 'active'],
        ];

        // Seed the sections table
        foreach ($sections as $section) {
            DB::table('sections')->insert([
                'id' => $section['id'],
                'section' => $section['section'],
                'status' => $section['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Creating Panel
        $panels = [
            ['id' => 1, 'panel_name' => 'Sender', 'section_id' => '1', 'status' => 'active'],
            ['id' => 2, 'panel_name' => 'Receiver','section_id' => '1', 'status' => 'active'],
            ['id' => 3, 'panel_name' => 'Seller', 'section_id' => '1', 'status' => 'active'],
            ['id' => 4, 'panel_name' => 'Buyer', 'section_id' => '2', 'status' => 'active'],
        ];

        // Seed the panels table
        foreach ($panels as $panel) {
            DB::table('panels')->insert([
                'id' => $panel['id'],
                'panel_name' => $panel['panel_name'],
                'section_id' => $panel['section_id'],
                'status' => $panel['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Creating Feature type for the panels
        $featuresType = [
            ['id' => 1, 'feature_type_name' => 'create', 'status' => 'active'],
            ['id' => 2, 'feature_type_name' => 'add', 'status' => 'active'],
            ['id' => 3, 'feature_type_name' => 'view', 'status' => 'active'],
            ['id' => 4, 'feature_type_name' => 'modify', 'status' => 'active'],
            ['id' => 5, 'feature_type_name' => 'delete', 'status' => 'active'],
            ['id' => 5, 'feature_type_name' => 'export', 'status' => 'active'],
            ['id' => 5, 'feature_type_name' => 'setting', 'status' => 'active'],
        ];

        // Seed the feature_types table
        foreach ($featuresType as $feature) {
            DB::table('feature_types')->insert([
                'id' => $feature['id'],
                'feature_type_name' => $feature['feature_type_name'],
                'status' => $feature['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Creating Features
        $features =[
            ['id' => 1, 'feature_type_id' => 1, 'panel_id' => 1, 'section_id' => 1, 'feature_name' => 'Create Challan', 'status' => 'active', 'template_id' => 1],
            ['id' => 2, 'feature_type_id' => 3, 'panel_id' => 1, 'section_id' => 1, 'feature_name' => 'Sent Challan', 'status' => 'active', 'template_id' => 4],
            ['id' => 3, 'feature_type_id' => 3, 'panel_id' => 1, 'section_id' => 1, 'feature_name' => 'Received Return Challan', 'status' => 'active', 'template_id' => 5],
            ['id' => 4, 'feature_type_id' => 3, 'panel_id' => 1, 'section_id' => 1, 'feature_name' => 'All Receiver', 'status' => 'active', 'template_id' => 7],
            ['id' => 5, 'feature_type_id' => 2, 'panel_id' => 1, 'section_id' => 1, 'feature_name' => 'Challan Prefix', 'status' => 'active', 'template_id' => 8],
            ['id' => 5, 'feature_type_id' => 2, 'panel_id' => 1, 'section_id' => 1, 'feature_name' => 'Add Receiver', 'status' => 'active', 'template_id' => 6],
            ['id' => 6, 'feature_type_id' => 2, 'panel_id' => 2, 'section_id' => 1, 'feature_name' => 'Create Return Challan', 'status' => 'active', 'template_id' => 9],
            ['id' => 7, 'feature_type_id' => 1, 'panel_id' => 2, 'section_id' => 1, 'feature_name' => 'Sent Return Challan', 'status' => 'active', 'template_id' => 10],
            ['id' => 8, 'feature_type_id' => 3, 'panel_id' => 2, 'section_id' => 1, 'feature_name' => 'Received Challan', 'status' => 'active', 'template_id' => 11],
            ['id' => 9, 'feature_type_id' => 3, 'panel_id' => 2, 'section_id' => 1, 'feature_name' => 'Return Challan Prefix', 'status' => 'active', 'template_id' => 12],
            ['id' => 10, 'feature_type_id' => 2, 'panel_id' => 3, 'section_id' => 2, 'feature_name' => 'Create Invoice', 'status' => 'active', 'template_id' => 13],
            ['id' => 12, 'feature_type_id' => 3, 'panel_id' => 3, 'section_id' => 2, 'feature_name' => 'Sent Invoice', 'status' => 'active', 'template_id' => 14],
            ['id' => 13, 'feature_type_id' => 3, 'panel_id' => 3, 'section_id' => 2, 'feature_name' => 'Purchase Order', 'status' => 'active', 'template_id' => 15],
            ['id' => 14, 'feature_type_id' => 3, 'panel_id' => 3, 'section_id' => 2, 'feature_name' => 'Add Buyer', 'status' => 'active', 'template_id' => 16],
            ['id' => 15, 'feature_type_id' => 3, 'panel_id' => 3, 'section_id' => 2, 'feature_name' => 'View Buyer', 'status' => 'active', 'template_id' => 17],
            ['id' => 16, 'feature_type_id' => 3, 'panel_id' => 3, 'section_id' => 2, 'feature_name' => 'Invoice Prefix', 'status' => 'active', 'template_id' => 18],
            ['id' => 17, 'feature_type_id' => 3, 'panel_id' => 4, 'section_id' => 2, 'feature_name' => 'All Invoice', 'status' => 'active', 'template_id' => 19],
            ['id' => 18, 'feature_type_id' => 3, 'panel_id' => 4, 'section_id' => 2, 'feature_name' => 'Purchase Order', 'status' => 'active', 'template_id' => 20],
            ['id' => 19, 'feature_type_id' => 3, 'panel_id' => 4, 'section_id' => 2, 'feature_name' => 'View Seller', 'status' => 'active', 'template_id' => 21],
            ['id' => 20, 'feature_type_id' => 2, 'panel_id' => 4, 'section_id' => 2, 'feature_name' => 'Add Seller', 'status' => 'active', 'template_id' => 22],
            ['id' => 21, 'feature_type_id' => 1, 'panel_id' => 4, 'section_id' => 2, 'feature_name' => 'New Purchase Oder', 'status' => 'active', 'template_id' => 23],
        ];
         
    }
   
}
