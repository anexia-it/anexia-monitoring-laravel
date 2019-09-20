<?php
namespace Anexia\Monitoring\Controllers;

use Anexia\Monitoring\Interfaces\UpMonitoringInterface;
use Anexia\Monitoring\Traits\AuthorizationTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Helpers\AnexiaMonitoringUpCheckHelper;

/**
 * Class UpMonitoringController
 * @package Anexia\Monitoring\Controllers
 */
class UpMonitoringController extends Controller
{
    use AuthorizationTrait;

    /** @var string[] */
    protected $errors = [];

    /**
     * Check the database connection and return 'OK' on success
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if (!$this->checkAccessToken($request)) {
            // no valid access_token given as GET parameter
            $response = response()->json([
                'code' => 'Unauthorized',
                'message' => 'You are not authorized to do this'
            ], 401);
        }

        else if (!$this->checkUpStatus()) {
            // up check was not successful
            $response = response($this->printErrors(), 500)
                ->header('Content-Type', 'text/plain');
        }

        else {
            // all fine
            $response = response('OK')
                ->header('Content-Type', 'text/plain');
        }

        return $response
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Credentials', 'true');
    }

    /**
     * Check the db connection and process possible customized checks
     *
     * @return bool
     */
    private function checkUpStatus()
    {
        // Test database connection (if not disabled)
        $deactivateDbCheck = config('monitoring.deactivate_db_check');
        if (!$deactivateDbCheck) {
            try {
                DB::connection()->getPdo();
            } catch (\Exception $e) {
                $this->errors[] = 'Database failure: Could not connect to db (error:' . $e->getMessage() . ')';
            }
        }

        // hook for custom checks
        if (class_exists(AnexiaMonitoringUpCheckHelper::class)) {
            $customDbCheckHelper = new AnexiaMonitoringUpCheckHelper();
            if ($customDbCheckHelper instanceof UpMonitoringInterface) {
                $customErrors = [];
                $customCheck = $customDbCheckHelper->checkUpStatus($customErrors);

                if (!$customCheck || !empty($customErrors)) {
                    // custom db check failed and/or returned errors
                    if (empty($customErrors)) {
                        // default error message, in case custom check failed without adding information to $customErrors
                        $customErrors[] = 'ERROR';
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