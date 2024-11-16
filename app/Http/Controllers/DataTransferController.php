<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Units;
use App\Models\Plans;
use App\Models\TeamUser;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\PlanFeatureUsageRecord;
use App\Models\TeamUserPermission;
use App\Models\PlanAdditionalFeatureUsageRecord;
use Illuminate\Support\Facades\DB;
use App\Models\PanelSeriesNumber;
use Illuminate\Http\Request;
use App\Jobs\TransferDataJob;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\V1\Orders\OrdersController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Http\Controllers\V1\Units\UnitsController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;


class DataTransferController extends Controller
{
    //
    public function transferData()
    {
        try {
            DB::beginTransaction();

            $teamUsers = TeamUser::whereNull('unique_login_id')->get();

            foreach ($teamUsers as $user) {
                do {
                    $uniqueLoginId = strtoupper(Str::random(8));
                } while (TeamUser::where('unique_login_id', $uniqueLoginId)->exists());

                $user->unique_login_id = $uniqueLoginId;
                $user->save();
            }

            DB::commit();
            return response()->json(['message' => 'Unique login IDs added successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error adding unique login IDs: ' . $e->getMessage()], 500);
        }
    }

    // public function addcolumn()
    // {
    //     $users = User::all();

    //     foreach ($users as $user) {
    //         // dd($user->id);
    //         $datas = [[
    //             'default' => '1',
    //             'panel_id' => '1',
    //             'section_id' => '1',
    //             'feature_id' => '1',
    //             'user_id' => $user->id,
    //             'panel_column_display_name' => 'Article',
    //             'panel_column_default_name' => 'column_1',
    //             'status' => 'active',
    //         ],[
    //             'default' => '1',
    //             'panel_id' => '1',
    //             'section_id' => '1',
    //             'feature_id' => '1',
    //             'user_id' => $user->id,
    //             'panel_column_display_name' => 'Hsn',
    //             'panel_column_default_name' => 'column_2',
    //             'status' => 'active',
    //         ],[
    //             'default' => '1',
    //             'panel_id' => '1',
    //             'section_id' => '1',
    //             'feature_id' => '1',
    //             'user_id' => $user->id,
    //             'panel_column_display_name' => 'Details',
    //             'panel_column_default_name' => 'column_3',
    //             'status' => 'active',
    //         ],[
    //             'default' => '0',
    //             'panel_id' => '1',
    //             'section_id' => '1',
    //             'feature_id' => '1',
    //             'user_id' => $user->id,
    //             'panel_column_display_name' => '',
    //             'panel_column_default_name' => 'column_0',
    //             'status' => 'active',
    //         ],[
    //             'default' => '0',
    //             'panel_id' => '1',
    //             'section_id' => '1',
    //             'feature_id' => '1',
    //             'user_id' => $user->id,
    //             'panel_column_display_name' => '',
    //             'panel_column_default_name' => 'column_1',
    //             'status' => 'active',
    //         ],[
    //             'default' => '0',
    //             'panel_id' => '1',
    //             'section_id' => '1',
    //             'feature_id' => '1',
    //             'user_id' => $user->id,
    //             'panel_column_display_name' => '',
    //             'panel_column_default_name' => 'column_2',
    //             'status' => 'active',
    //         ],[
    //             'default' => '0',
    //             'panel_id' => '1',
    //             'section_id' => '1',
    //             'feature_id' => '1',
    //             'user_id' => $user->id,
    //             'panel_column_display_name' => '',
    //             'panel_column_default_name' => 'column_3',
    //             'status' => 'active',
    //         ],
    //         [
    //             'default' => '1',
    //             'panel_id' => '3',
    //             'section_id' => '2',
    //             'feature_id' => '12',
    //             'user_id' => $user->id,
    //             'panel_column_display_name' => 'Article',
    //             'panel_column_default_name' => 'column_1',
    //             'status' => 'active',
    //         ],[
    //             'default' => '1',
    //             'panel_id' => '3',
    //             'section_id' => '2',
    //             'feature_id' => '12',
    //             'user_id' => $user->id,
    //             'panel_column_display_name' => 'Hsn',
    //             'panel_column_default_name' => 'column_2',
    //             'status' => 'active',
    //         ],[
    //             'default' => '1',
    //             'panel_id' => '3',
    //             'section_id' => '2',
    //             'feature_id' => '12',
    //             'user_id' => $user->id,
    //             'panel_column_display_name' => 'Details',
    //             'panel_column_default_name' => 'column_3',
    //             'status' => 'active',
    //         ],[
    //             'default' => '0',
    //             'panel_id' => '3',
    //             'section_id' => '2',
    //             'feature_id' => '12',
    //             'user_id' => $user->id,
    //             'panel_column_display_name' => '',
    //             'panel_column_default_name' => 'column_0',
    //             'status' => 'active',
    //         ],[
    //             'default' => '0',
    //             'panel_id' => '3',
    //             'section_id' => '2',
    //             'feature_id' => '12',
    //             'user_id' => $user->id,
    //             'panel_column_display_name' => '',
    //             'panel_column_default_name' => 'column_1',
    //             'status' => 'active',
    //         ],[
    //             'default' => '0',
    //             'panel_id' => '3',
    //             'section_id' => '2',
    //             'feature_id' => '12',
    //             'user_id' => $user->id,
    //             'panel_column_display_name' => '',
    //             'panel_column_default_name' => 'column_2',
    //             'status' => 'active',
    //         ],[
    //             'default' => '0',
    //             'panel_id' => '3',
    //             'section_id' => '2',
    //             'feature_id' => '12',
    //             'user_id' => $user->id,
    //             'panel_column_display_name' => '',
    //             'panel_column_default_name' => 'column_3',
    //             'status' => 'active',
    //         ],
    //         ];
    //         foreach ($datas as $data) {


    //             $request  = new Request;
    //             // $request->replace([]);
    //             $request->merge($data);
    //             // dd($request);
    //             $newChallanDesign = new PanelColumnsController;
    //             $response = $newChallanDesign->store($request);
    //         }
    //     }
    // }

//     public function addcolumn()
// {
//     // Fetch the user with ID '688'
//     $users = User::all();

//     foreach ($users as $user) {
//     // Create Default Series Number
//     $panelSeriesNumberController = new PanelSeriesNumberController;

//     $currentFinancialYearStart = now()->startOfYear()->month(4)->day(1);
//     $nextFinancialYearEnd = now()->startOfYear()->addYear(1)->month(3)->day(31);

//     // If the current date is before April 1st, consider it as part of the previous financial year
//     if (now() < $currentFinancialYearStart) {
//         $currentFinancialYearStart = $currentFinancialYearStart->subYear();
//         $nextFinancialYearEnd = $nextFinancialYearEnd->subYear();
//     }

//     $financialYear = ($currentFinancialYearStart->year % 100) . '-' . ($nextFinancialYearEnd->year % 100);

//     $combinations = [
//         ['prefix' => 'CH-', 'panel_id' => 1, 'section_id' => 1],
//         ['prefix' => 'CH-', 'panel_id' => 2, 'section_id' => 1],
//         ['prefix' => '', 'panel_id' => 3, 'section_id' => 2],
//         ['prefix' => 'PO-', 'panel_id' => 4, 'section_id' => 2],
//     ];

//     foreach ($combinations as $combination) {
//         $prefix = $combination['prefix'];
//         $panel_id = $combination['panel_id'];
//         $section_id = $combination['section_id'];

//         // Get the previous default series number for this combination
//         $previousDefaultSeriesNumber = PanelSeriesNumber::where('user_id', $user->id)
//             ->where('panel_id', $panel_id)
//             ->where('section_id', $section_id)
//             ->where('default', "1") // Assuming '1' represents default series number
//             ->first();
//         // dd($previousDefaultSeriesNumber);
//         // Update the previous default series number to set default to 0
//         if ($previousDefaultSeriesNumber) {
//             $previousDefaultSeriesNumber->update(['default' => "0"]);
//         }

//         // Generate the series number
//         $series_number = strtoupper($prefix . (strlen($user->company_name) >= 4
//             ? substr($user->company_name, 0, 4)
//             : substr($user->name, 0, 4)) . '-' . $financialYear);

//         // Create a new panel series number directly
//         $panelSeriesNumber = PanelSeriesNumber::create([
//             'series_number' => $series_number,
//             'user_id' => $user->id,
//             'panel_id' => $panel_id,
//             'section_id' => $section_id,
//             'assigned_to_id' => null,
//             'assigned_to_name' => null,
//             'status' => 'active',
//             'valid_from' => $currentFinancialYearStart,
//             'valid_till' => $nextFinancialYearEnd,
//             'default' => '1',
//         ]);
//     }
// }

// return true;
// }



    // public function freePlan()
    // {
    //     // Fetch all users
    //     $users = User::all();

    //     foreach ($users as $user) {
    //         // Create Order
    //         $planCombinations = [
    //             ['plan_ids' => 50],
    //             ['plan_ids' => 11],
    //             ['plan_ids' => 19],
    //             ['plan_ids' => 10],
    //             ['plan_ids' => 58],
    //         ];

    //         foreach ($planCombinations as $planCombination) {
    //             $orderData = [
    //                 'user_id' => $user->id,
    //                 'plan_ids' => $planCombination,
    //             ];

    //             try {
    //                 foreach ($orderData['plan_ids'] as $plan_id) {
    //                     $plan = Plans::where('id', $plan_id)->with('features', 'additionalFeatures')->first();
    //                     if ($plan) {
    //                         $order = new Order();
    //                         $order->user_id = $orderData['user_id'];
    //                         $order->plan_id = $plan_id;
    //                         $order->section_id = $plan->section_id;
    //                         $order->panel_id = $plan->panel_id;
    //                         $order->purchase_date = today()->format('Y-m-d H:i:s');
    //                         $order->expiry_date = today()->addDays($plan->validity_days);
    //                         $order->amount = null; // Assuming amount is nullable
    //                         $order->status = 'active';
    //                         $order->added_by = 'admin'; // Assuming admin is adding the order
    //                         $order->save();

    //                         // Check if plan_id is not in [3, 11, 10, 19]
    //                         if (!in_array($plan_id, [3, 11, 10, 19])) {
    //                             // Update the sender, receiver, seller, and buyer fields in the users table based on panel_id
    //                             switch ($plan->panel_id) {
    //                                 case 1:
    //                                     User::where('id', $orderData['user_id'])->update(['sender' => 1]);
    //                                     break;
    //                                 case 2:
    //                                     User::where('id', $orderData['user_id'])->update(['receiver' => 1]);
    //                                     break;
    //                                 case 3:
    //                                     User::where('id', $orderData['user_id'])->update(['seller' => 1]);
    //                                     break;
    //                                 case 4:
    //                                     User::where('id', $orderData['user_id'])->update(['buyer' => 1]);
    //                                     break;
    //                             }
    //                         }

    //                         foreach ($plan->features as $feature) {
    //                             $planFeatureUsageRecord = new PlanFeatureUsageRecord();
    //                             $planFeatureUsageRecord->order_id = $order->id;
    //                             $planFeatureUsageRecord->plan_feature_id = $feature->id;
    //                             $planFeatureUsageRecord->feature_id = $feature->feature_id;
    //                             $planFeatureUsageRecord->usage_count = 0;
    //                             $planFeatureUsageRecord->usage_limit = $feature->feature_usage_limit;
    //                             $planFeatureUsageRecord->status = 'active';
    //                             $planFeatureUsageRecord->save();
    //                         }

    //                         if (!empty($plan->additionalFeatures)) {
    //                             foreach ($plan->additionalFeatures as $add_feature) {
    //                                 $planAdditionalFeatureUsageRecord = new PlanAdditionalFeatureUsageRecord();
    //                                 $planAdditionalFeatureUsageRecord->order_id = $order->id;
    //                                 $planAdditionalFeatureUsageRecord->plan_additional_feature_id = $add_feature->id;
    //                                 $planAdditionalFeatureUsageRecord->additional_feature_id = $add_feature->additional_feature_id;
    //                                 $planAdditionalFeatureUsageRecord->usage_count = 0;
    //                                 $planAdditionalFeatureUsageRecord->usage_limit = $add_feature->additional_feature_usage_limit;
    //                                 $planAdditionalFeatureUsageRecord->status = 'active';
    //                                 $planAdditionalFeatureUsageRecord->save();
    //                             }
    //                         }

    //                         // Generate the PDF for the Invoice using PDFGenerator class if the order amount is not zero
    //                         if ($order->amount != 0) {
    //                             $pdfGenerator = new PDFGeneratorService();
    //                             $response = $pdfGenerator->planInvoicePDF($order);
    //                             $response = (array) $response->getData();
    //                             $order->pdf_url = $response['pdf_url'];
    //                             $order->save();
    //                         }
    //                     } else {
    //                         \Log::error('Invalid Plan Id: ' . $plan_id);
    //                     }
    //                 }

    //                 \Log::info('Order placed successfully for user ID: ' . $user->id);
    //             } catch (\Exception $e) {
    //                 \Log::error('Error in freePlan method for user ID ' . $user->id . ': ' . $e->getMessage());
    //             }
    //         }
    //     }

    //     return true;
    // }

// public function permissions()
// {
//     $users = User::all(); // Fetch all users

//     // Define the roles, methods, and a mapping of roles to their specific actions
//     $roles = ['sender', 'receiver', 'seller', 'buyer', 'receipt_note'];
//     $methods = ['whatsapp', 'email'];
//     $roleActions = [
//         'sender' => ['Sent Challan', 'Received Return Challan', 'Additional Number', 'Add Comment', "Sfp"],
//         'receiver' => ['Sent Return Challan', 'Received Challan', 'Add Comment', 'Sfp'],
//         'seller' => ['Sent Invoice', 'Received PO', 'Add Comment', 'Sfp'],
//         'buyer' => ['Sent PO', 'Received Invoice', 'Add Comment', 'Sfp'],
//         'receipt_note' => ['Sent Receipt Note', 'Add Comment', 'Sfp']
//     ];

//     foreach ($users as $user) {
//         // Decode the existing permissions
//         $permissions = json_decode($user->permissions, true);

//         // Ensure all roles are initialized for the user
//         foreach ($roles as $role) {
//             if (!isset($permissions[$role])) {
//                 $permissions[$role] = [];
//                 foreach ($methods as $method) {
//                     $permissions[$role][$method] = []; // Initialize method array
//                     // Initialize permissions for the role based on the roleActions mapping
//                     foreach ($roleActions[$role] as $action) {
//                         $permissions[$role][$method][$action] = false; // Default to false
//                     }
//                 }
//             } else {
//                 // Role exists, just update with new actions if they don't exist
//                 foreach ($methods as $method) {
//                     if (!isset($permissions[$role][$method])) {
//                         $permissions[$role][$method] = []; // Initialize method array if not exists
//                     }
//                     foreach ($roleActions[$role] as $action) {
//                         // Add or update the action permission for the role and method
//                         if (!isset($permissions[$role][$method][$action])) {
//                             $permissions[$role][$method][$action] = false; // Default to false
//                         }
//                     }
//                 }
//             }
//         }

//         // Encode the updated permissions back to JSON and save
//         $user->permissions = json_encode($permissions);
//         $user->save();
//     }

//     return true; // Indicate success
// }

    // public function addunit()
    // {
    //     $users = User::all();
    //     $panelTypes = ['sender', 'receiver', 'seller', 'buyer', 'receipt_note']; // Define your panel types here

    //     foreach ($users as $user) {
    //         foreach ($panelTypes as $panelType) {
    //             $unitData = [
    //                 ['unit' => 'bags', 'short_name' => 'bag', 'status' => 'active', 'is_default' => '1', 'user_id' => $user->id, 'panel_type' => $panelType],
    //                 ['unit' => 'dozens', 'short_name' => 'dzn', 'status' => 'active', 'is_default' => '1', 'user_id' => $user->id, 'panel_type' => $panelType],
    //                 ['unit' => 'grammes', 'short_name' => 'gm', 'status' => 'active', 'is_default' => '1', 'user_id' => $user->id, 'panel_type' => $panelType],
    //                 ['unit' => 'kilograms', 'short_name' => 'kg', 'status' => 'active', 'is_default' => '1', 'user_id' => $user->id, 'panel_type' => $panelType],
    //                 ['unit' => 'litre', 'short_name' => 'ltr', 'status' => 'active', 'is_default' => '1', 'user_id' => $user->id, 'panel_type' => $panelType],
    //                 ['unit' => 'meters', 'short_name' => 'mtr', 'status' => 'active', 'is_default' => '1', 'user_id' => $user->id, 'panel_type' => $panelType],
    //                 ['unit' => 'pieces', 'short_name' => 'pcs', 'status' => 'active', 'is_default' => '1', 'user_id' => $user->id, 'panel_type' => $panelType],
    //                 ['unit' => 'cartons', 'short_name' => 'ctn', 'status' => 'active', 'is_default' => '1', 'user_id' => $user->id, 'panel_type' => $panelType],
    //                 ['unit' => 'Militre', 'short_name' => 'ml', 'status' => 'active', 'is_default' => '1', 'user_id' => $user->id, 'panel_type' => $panelType],
    //             ];

    //             foreach ($unitData as $data) {
    //                 $unit = new Units([
    //                     'unit' => $data['unit'],
    //                     'short_name' => $data['short_name'],
    //                     'status' => $data['status'],
    //                     'is_default' => $data['is_default'],
    //                     'user_id' => $data['user_id'],
    //                     'panel_type' => $data['panel_type'],
    //                 ]);
    //                 $unit->save();
    //             }
    //         }
    //     }

    //     return true;
    //     // Optionally, you can return a response or redirect here
    // }

//  public function addColumn()
// {
//     $users = User::all();

//     foreach ($users as $user) {
//         $panelSeriesNumberController = new PanelSeriesNumberController;

//         $currentFinancialYearStart = now()->startOfYear()->month(4)->day(1);
//         $nextFinancialYearEnd = now()->startOfYear()->addYear(1)->month(3)->day(31);

//         // If the current date is before April 1st, consider it as part of the previous financial year
//         if (now() < $currentFinancialYearStart) {
//             $currentFinancialYearStart = $currentFinancialYearStart->subYear();
//             $nextFinancialYearEnd = $nextFinancialYearEnd->subYear();
//         }

//         $financialYear = ($currentFinancialYearStart->year % 100) .  ($nextFinancialYearEnd->year % 100);

//         $combinations = [
//             // ['prefix' => 'CH-', 'panel_id' => 1, 'section_id' => 1],
//             // ['prefix' => 'CH-', 'panel_id' => 2, 'section_id' => 1],
//             // ['prefix' => '', 'panel_id' => 3, 'section_id' => 2],
//             // ['prefix' => 'PO-', 'panel_id' => 4, 'section_id' => 2],
//             ['prefix' => 'GRN-', 'panel_id' => 5, 'section_id' => 1],
//         ];

//         foreach ($combinations as $combination) {
//             $prefix = $combination['prefix'];
//             $panel_id = $combination['panel_id'];
//             $section_id = $combination['section_id'];

//             // Generate the series number
//             $company_name_without_spaces = str_replace(' ', '', $user->company_name);
//             $series_number = strtoupper($prefix . (strlen($user->company_name) >= 4
//             ? substr($company_name_without_spaces, 0, 4)
//             : substr($user->name, 0, 4)) . '-' . $financialYear);

//             // // Check if the series number already exists
//             // $existingSeriesNumber = PanelSeriesNumber::where('series_number', $series_number)->first();

//             // if (!$existingSeriesNumber) {
//                 // Create a new panel series number if it doesn't exist
//                 $panelSeriesNumber = PanelSeriesNumber::create([
//                     'series_number' => $series_number,
//                     'panel_id' => $panel_id,
//                     'section_id' => $section_id,
//                     'status' => 'active',
//                     'valid_from' => $currentFinancialYearStart,
//                     'valid_till' => $nextFinancialYearEnd,
//                     'default' => '1',
//                     'user_id' => $user->id,
//                 ]);
//             // }
//             // dd($panelSeriesNumber);
//         }
//     }

//     return true;
// }


    // public function updatePermissions()
    // {
    //     $users = TeamUserPermission::all();
    //     $updatedCount = 0;

    //     foreach ($users as $teamUserPermission) {
    //         // Get the existing permissions
    //         $existingPermissions = json_decode($teamUserPermission->permission, true);

    //         // New permissions to be added
    //         $newPermissions = [
    //             'seller' => [
    //                 'create_invoice' => 0,
    //                 'create_quotation' => 0,
    //                 'view_quotation_tables' => 0,
    //                 'view_sent_invoice_tables' => 0,
    //                 'view_purchase_orders' => 0,
    //                 'export_invoice' => 0,
    //                 'export_quotation' => 0,
    //                 'view_buyers' => 0,
    //                 'add_buyer' => 0,
    //                 'edit_buyer' => 0,
    //                 'delete_buyer' => 0,
    //                 'invoice_prefix' => 0,
    //                 'add_po_comment' => 0,
    //                 'add_po_tags' => 0,
    //                 'add_quotation_tags' => 0,
    //                 'add_quotation_comment' => 0,
    //                 'add_po_payment_status' => 0,
    //                 'invoice_design' => 0,
    //                 'bulk_sent_invoices' => 0,
    //                 'view_invoice' => 0,
    //                 'send_invoice' => 0,
    //                 'send_quotation' => 0,
    //                 'modify_invoice' => 0,
    //                 'modify_quotation' => 0,
    //                 'delete_sent_invoice' => 0,
    //                 'delete_sent_quotation' => 0,
    //                 'accept_purchase_order' => 0,
    //                 'reject_purchase_order' => 0,
    //                 'bulk_received_purchase_orders' => 0,
    //                 'add_invoice_comment' => 0,
    //                 'add_invoice_tags' => 0,
    //                 'add_invoice_payment_status' => 0,
    //             ],
    //         ];

    //         // Merge the new permissions with the existing ones
    //         foreach ($newPermissions as $role => $permissions) {
    //             if (!isset($existingPermissions[$role])) {
    //                 $existingPermissions[$role] = [];
    //             }
    //             foreach ($permissions as $permission => $value) {
    //                 if (!isset($existingPermissions[$role][$permission])) {
    //                     $existingPermissions[$role][$permission] = $value;
    //                 }
    //             }
    //         }

    //         // Update the TeamUserPermission record
    //         $teamUserPermission->permission = json_encode($existingPermissions);
    //         $teamUserPermission->save();

    //         $updatedCount++;
    //     }

    //     return response()->json([
    //         'message' => 'Permissions updated successfully for all users',
    //         'updated_count' => $updatedCount
    //     ], 200);
    // }


}
