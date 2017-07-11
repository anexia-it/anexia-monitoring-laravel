<?php
namespace Anexia\Monitoring\Controllers;

use Anexia\Monitoring\Traits\AuthorizationTrait;
use App\Helpers\AnexiaMonitoringUpCheckHelper;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class UpMonitoringController
 * @package Anexia\Monitoring\Controllers
 */
class UpMonitoringController extends Controller
{
    use AuthorizationTrait;

    /** string */
    const DEFAULT_TABLE_TO_CHECK = 'user';

    /** @var string */
    protected $tableToCheck;

    /** @var string[] */
    protected $errors = array();

    public function __construct()
    {
        $this->tableToCheck = config('monitoring.table_to_check');
    }

    /**
     * Check the database connection and return 'OK' on success
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if (!$this->checkAccessToken(request())) {
            // no valid access_token given as GET parameter
            return response()->json([
                'code' => 'Unauthorized',
                'message' => 'You are not authorized to do this'
            ], 401);
        }

        if (!$this->checkUpStatus()) {
            // up check was not successful
            return response($this->printErrors(), 500);
        }

        return response('OK');
    }

    /**
     * Check the db connection and that a select from a table returns results
     *
     * @return bool
     */
    private function checkUpStatus()
    {
        // Test database connection
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $this->errors[] = 'Database failure: Could not connect to db (error:' . $e->getMessage() . ')';
        }

        // hook for custom db checks
        if (class_exists(AnexiaMonitoringUpCheckHelper::class)) {
            $customDbCheckHelper = new AnexiaMonitoringUpCheckHelper();
            if ($customDbCheckHelper instanceof UpMonitoringInterface) {
                $customErrors = array();
                $customCheck = $customDbCheckHelper->checkUpStatus($customErrors);

                if (!$customCheck || !empty($customErrors)) {
                    // custom db check failed and/or returned errors
                    if (empty($customErrors)) {
                        // default error message, in case custom check failed without adding information to $customErrors
                        $customErrors[] = 'Database failure: custom check was not successful!';
                    }

                    $this->errors = array_merge($this->errors, $customErrors);
                }
            }
        }

        if (!empty($this->errors)) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    private function printErrors()
    {
        $errorString = '';

        if (!empty($this->errors)) {
            foreach ($this->errors as $error) {
                $errorString .= $error . "\n";
            }
        }

        return $errorString;
    }
}