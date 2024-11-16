namespace Tests\Unit\Controllers\V1\Challan;

use Tests\TestCase;
use App\Models\Challan;
use App\Models\ChallanStatus;
use App\Models\ReceiverDetails;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChallanControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_method_returns_success_response()
    {
        $response = $this->get('/challans');

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Success',
            'status_code' => 200,
        ]);
    }

    public function test_index_method_returns_filtered_challans()
    {
        // Create test data
        $challan1 = Challan::factory()->create(['sender_id' => 1]);
        $challan2 = Challan::factory()->create(['sender_id' => 2]);
        $challan3 = Challan::factory()->create(['receiver_id' => 1]);
        $challan4 = Challan::factory()->create(['receiver_id' => 2]);

        // Make a request with filters
        $response = $this->get('/challans?sender_id=1&receiver_id=2');

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Success',
            'status_code' => 200,
            'data' => [
                [
                    'id' => $challan3->id,
                    'sender_id' => $challan3->sender_id,
                    'receiver_id' => $challan3->receiver_id,
                ],
            ],
        ]);
    }

    // Add more test cases for different scenarios

    // ...

}namespace Tests\Unit\Controllers\V1\Challan;

use Tests\TestCase;
use App\Models\Challan;
use App\Models\ChallanStatus;
use App\Models\ReceiverDetails;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChallanControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_indexCheckBalance_method_returns_success_response()
    {
        $response = $this->get('/challans/check-balance');

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Success',
            'status_code' => 200,
        ]);
    }

    // Add more test cases for different scenarios

    // ...
}