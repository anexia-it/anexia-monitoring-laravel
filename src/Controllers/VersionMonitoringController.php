<?php
namespace Anexia\Monitoring\Controllers;

use Anexia\ComposerTools\Traits\ComposerPackagistTrait;
use Anexia\Monitoring\Traits\AuthorizationTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class VersionMonitoringController
 * @package Anexia\Monitoring\Controllers
 */
class VersionMonitoringController extends Controller
{
    use AuthorizationTrait, ComposerPackagistTrait;

    /**
     * Retrieve runtime and composer package version information
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

        else {
            // all fine
            $runtime = [
                'platform' => 'php',
                'platform_version' => phpversion(),
                'framework' => 'laravel',
                'framework_installed_version' => $this->getCurrentFrameworkVersion(),
                'framework_newest_version' => $this->getLatestFrameworkVersion('laravel/framework')
            ];

            $modules = $this->getComposerPackageData();

            $response = response()->json([
                'runtime' => $runtime,
                'modules' => $modules
            ]);
        }

        return $response
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Credentials', 'true');
    }

    /**
     * Get version number of the currently installed laravel package
     *
     * @return string
     */
    private function getCurrentFrameworkVersion()
    {
        $laravel = app();
        $version = (string)$laravel::VERSION;

        if ($version[0] !== 'v') {
            $version = 'v' . $version;
        }

        return $version;
    }
}