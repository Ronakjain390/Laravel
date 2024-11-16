<?php

namespace App\Http\Controllers\V1\TeamUser;

use App\Models\Team;
use App\Models\TeamUser;
use App\Models\Template;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TeamUserPermission;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TeamUserController extends Controller
{
    public function index()
    {
        try {
            $teamUsers = TeamUser::where('team_owner_user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)->with('team', 'owner')->get();
            return response()->json(['data' => $teamUsers], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => ['Failed to retrieve team users']], 500);
        }
    }

    public function allTeamUsers()
    {
        try {
            $teamUsers = TeamUser::with('team', 'user')->get();
            return response()->json(['data' => $teamUsers], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => ['Failed to retrieve team users']], 500);
        }
    }

    public function show($id)
    {
        try {
            // $teamUser = TeamUser::findOrFail($id)->with('team')->first();
            $teamUser = TeamUser::where('id', $id)->with('team')->first();

            return response()->json(['data' => $teamUser], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => ['Team user not found']], 404);
        }
    }
    public function store(Request $request)
    {
        // Validate the incoming data
        $validator = Validator::make($request->all(), [
            'team_user_name' => 'required|string|max:255',
            'team_name' => 'required|string|max:255',
            'email' => 'email|max:255|unique:team_users,email',
            'password' => 'required|string|min:8',
            'team_user_address' => 'nullable|string',
            'team_user_pincode' => 'nullable|numeric',
            'phone' => 'nullable|string|size:10|unique:team_users,phone',
            'team_user_state' => 'nullable|string',
            'team_user_city' => 'nullable|string',
            'team_id' => 'required|numeric',
            'team_owner_user_id' => 'required|numeric',
        ]);

        // Handle validation failures
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        try {
            // Manually extract and flatten the data
            $data = [
                'team_user_name' => $request->input('team_user_name'),
                'team_name' => $request->input('team_name'),
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'team_user_address' => $request->input('team_user_address'),
                'team_user_pincode' => $request->input('team_user_pincode'),
                'phone' => $request->input('phone'),
                'team_user_state' => $request->input('team_user_state'),
                'team_user_city' => $request->input('team_user_city'),
                'team_id' => $request->input('team_id'),
                'team_owner_user_id' => $request->input('team_owner_user_id'),
            ];

            // Generate a unique 8-digit ID Alpha numeric
            do {
                $uniqueLoginId = strtoupper(Str::random(8));
            } while (TeamUser::where('unique_login_id', $uniqueLoginId)->exists());

            // Insert the data into the TeamUser table and get the ID of the new record
            $data['unique_login_id'] = $uniqueLoginId;

            // Insert the data into the TeamUser table and get the ID of the new record
            $teamUserId = TeamUser::insertGetId($data);

                // dd($teamUserId);
                $permission = [
                    'sender'=>[
                        'create_challan'=>0,
                        'view_sent_challans_tables'=>0,
                        'view_received_return_challans_tables'=>0,
                        'view_receiver'=>0,
                        'add_receiver'=>0,
                        'edit_receiver'=>0,
                        'delete_receiver'=>0,
                        'challan_prefix'=>0,
                        'add_challan_prefix'=>0,
                        'edit_challan_prefix'=>0,
                        'delete_challan_prefix'=>0,
                        'challan_design'=>0,
                        'view_challan'=>0,
                        'send_challan'=>0,
                        'modify_challan'=>0,
                        'export'=>0,
                        'bulk_sent_challans'=>0,
                        'delete_sent_challans'=>0,
                        'self_delivery'=>0,
                        'self_return'=>0,
                        'add_sent_comment'=>0,
                        'add_sent_tags'=>0,
                        'add_sent_payment_status'=>0,
                        'bulk_received_return_challans'=>0,
                        'accept_return_challan'=>0,
                        'reject_return_challan'=>0,
                        'add_received_comment'=>0,
                        'add_received_tags'=>0,
                        'add_received_payment_status'=>0,
                    ],
                    'receiver'=>[
                        'create_return_challan'=>0,
                        'view_sent_challans_tables'=>0,
                        'view_received_challans_tables'=>0,
                        'return_challan_prefix'=>0,
                        'add_return_challan_prefix'=>0,
                        'edit_return_challan_prefix'=>0,
                        'delete_return_challan_prefix'=>0,
                        'send_challan'=>0,
                        'export'=>0,
                        'delete_sent_challans'=>0,
                        'modify_challan'=>0,
                        'bulk_sent_challans'=>0,
                        'add_sent_comment'=>0,
                        'add_sent_tags'=>0,
                        'add_sent_payment_status'=>0,
                        'accept_received_challan'=>0,
                        'reject_received_challan'=>0,
                        'bulk_received_challans'=>0,
                        'add_received_comment'=>0,
                        'add_received_tags'=>0,
                        'add_received_payment_status'=>0,
                    ],
                    'receipt_note'=>[
                        'create_receipt_note'=>0,
                        'view_sent_receipt_notes_tables'=>0,
                        'send_receipt_notes'=>0,
                        'bulk_sent_receipt_notes'=>0,
                        'export'=>0,
                        'modify_receipt_note'=>0,
                        'delete_sent_receipt_notes'=>0,
                        'add_receipt_note_prefix'=>0,
                        'view_receiver'=>0,
                        'add_receiver'=>0,
                        'edit_receiver'=>0,
                        'delete_receiver'=>0,
                        'receipt_note_design'=>0,
                        'add_sent_comment'=>0,
                        'add_sent_tags'=>0,
                        'add_sent_payment_status'=>0,
                    ],
                    'seller'=>[
                        'create_invoice'=>0,
                        'create_quotation'=>0,
                        'view_quotation_tables'=>0,
                        'view_sent_invoice_tables'=>0,
                        'view_purchase_orders'=>0,
                        'export_invoice'=>0,
                        'export_quotation'=>0,
                        'view_buyers'=>0,
                        'add_buyer'=>0,
                        'edit_buyer'=>0,
                        'delete_buyer'=>0,
                        'invoice_prefix'=>0,
                        'add_po_comment'=>0,
                        'add_po_tags'=>0,
                        'add_quotation_tags'=>0,
                        'add_quotation_comment'=>0,
                        'add_po_payment_status'=>0,
                        'invoice_design'=>0,
                        'bulk_sent_invoices'=>0,
                        'view_invoice'=>0,
                        'send_invoice'=>0,
                        'send_quotation'=>0,
                        'modify_invoice'=>0,
                        'modify_quotation'=>0,
                        'delete_sent_invoice'=>0,
                        'delete_sent_quotation'=>0,
                        'accept_purchase_order'=>0,
                        'reject_purchase_order'=>0,
                        'bulk_received_purchase_orders'=>0,
                        'add_invoice_comment'=>0,
                        'add_invoice_tags'=>0,
                        'add_invoice_payment_status'=>0,
                    ],
                    'buyer'=>[
                        'new_purchase_order'=>0,
                        'view_purchase_order_tables'=>0,
                        'view_invoices' => 0,
                        'export'=>0,
                        'view_seller'=>0,
                        'add_seller'=>0,
                        'PO_design' => 0,
                        'add_po_comment'=>0,
                        'add_po_tags'=>0,
                        'add_po_payment_status'=>0,
                        'bulk_sent_purchase_orders'=>0,
                        'send_purchase_order'=>0,
                        'view_purchase_order'=>0,
                        'modify_purchase_order'=>0,
                        'delete_sent_purchase_order'=>0,
                        'self_delivery_purchase_order'=>0,
                        'bulk_received_invoices'=>0,
                        'accept_invoice'=>0,
                        'reject_invoice'=>0,
                        'add_invoice_comment'=>0,
                        'add_invoice_tags'=>0,
                        'add_invoice_payment_status'=>0,
                    ],
                    'stock'=>[
                        'add_stock' => 0,
                        'update_stock' => 0,
                        'edit_stock' => 0,
                        'delete_stock' => 0,
                    ],
                    'pages'=>[
                        'how_theparchi_works'=>0,
                        'pricing'=>0,
                        'all_features'=>0,
                        'help'=>0,
                    ],
                ];




                // Create and save a new TeamUserPermission record
                $teamUserPermission = new TeamUserPermission([
                    'team_user_id' => $teamUserId, // Use the ID from insertGetId
                    'team_id' => $request->team_id,
                    'team_owner_user_id' => $request->team_owner_user_id,
                    'permission' => json_encode($permission),
                    'status' => 'active',
                ]);
                $teamUserPermission->save();
                // dd($teamUserPermission);
                // return response()->json(['message' => ['TeamUserPermission created successfully']], 200);

                return response()->json(['message' => 'Team user created', 'data' => $teamUser], 200);
            } catch (Exception $e) {
                return response()->json(['message' => 'Validation error', 'errors' => $e->errors()], 422);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Team user creation failed','errors' => $e], 500);
            }
        }

        public function update(Request $request, $id)
        {
            try {
                $teamUser = TeamUser::findOrFail($id);
                $data = $request->validated();
                $teamUser->update($data);
                return response()->json(['message' => 'Team user updated', 'data' => $teamUser], 200);
            } catch (ValidationException $e) {
                return response()->json(['message' => ['Validation error'], 'errors' => $e->errors()], 422);
            } catch (\Exception $e) {
                return response()->json(['message' => ['Team user update failed']], 500);
            }
        }
        // TeamUserController

        public function destroy($id)
        {
            try {
                $teamUser = TeamUser::findOrFail($id);
                $teamUser->delete();
                return response()->json(['message' => 'Team user deleted'], 200);
            } catch (\Exception $e) {
                return response()->json(['errors' => ['Team user deletion failed']], 500);
            }
        }
        public function changePassword(Request $request){
            $credentials = $request->only('id', 'password', 'password_confirmation' );
            // dd($request->id);
        // Retrieve the user by phone number
        $user = TeamUser::where('id', $request->id)->first();
            // dd( $user );
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        // Update the user's password
        $user->forceFill([
            'password' => Hash::make($credentials['password']),
        ])->save();

        // If needed, you can update other fields here as well
        // dd($user);
        return response()->json([
            'success' => true,
            'message' => 'Password has been changes successfully',
        ], 200);
        }
    }
