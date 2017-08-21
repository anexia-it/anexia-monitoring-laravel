<?php
namespace Anexia\Monitoring\Controllers;

use Anexia\Monitoring\Traits\AuthorizationTrait;
use Composer\Semver\VersionParser;
use Illuminate\Routing\Controller;

/**
 * Class VersionMonitoringController
 * @package Anexia\Monitoring\Controllers
 */
class VersionMonitoringController extends Controller
{
    use AuthorizationTrait;

    /**
     * Retrieve runtime and composer package version information
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if (!$this->checkAccessToken(request())) {
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
                'framework_newest_version' => $this->getLatestFrameworkVersion()
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

    /**
     * Get latest (stable) version number of composer laravel package (laravel/framework)
     *
     * @return string
     */
    private function getLatestFrameworkVersion()
    {
        $packageName = 'laravel/framework';
        $lastVersion = '';

        // get version information from packagist
        $packagistUrl = 'https://packagist.org/packages/' . $packageName . '.json';

        try {
            $packagistInfo = json_decode(file_get_contents($packagistUrl));
            $versions = $packagistInfo->package->versions;
        } catch (\Exception $e) {
            $versions = [];
        }

        if (count($versions) > 0) {
            $latestStableNormVersNo = '';
            foreach ($versions as $versionData) {
                $versionNo = $versionData->version;
                $normVersNo = $versionData->version_normalized;
                $stability = VersionParser::normalizeStability(VersionParser::parseStability($versionNo));

                // only use stable version numbers
                if ($stability === 'stable' && version_compare($normVersNo, $latestStableNormVersNo) >= 0) {
                    $lastVersion = $versionNo;
                    $latestStableNormVersNo = $normVersNo;
                }
            }
        }

        return $lastVersion;
    }

    /**
     * Get information for composer installed packages (currently installed version and latest stable version)
     *
     * @return array
     */
    private function getComposerPackageData()
    {
        $moduleVersions = [];

        $installedJsonFile = getcwd() . '/../vendor/composer/installed.json';
        $packages = json_decode(file_get_contents($installedJsonFile));

        if (count($packages) > 0) {
            foreach ($packages as $package) {
                $name = $package->name;
                $latestStableVersNo = '';

                /**
                 * get latest stable version number
                 */
                // get version information from packagist
                $packagistUrl = 'https://packagist.org/packages/' . $name . '.json';

                try {
                    $packagistInfo = json_decode(file_get_contents($packagistUrl));
                    $versions = $packagistInfo->package->versions;
                } catch (\Exception $e) {
                    $versions = [];
                }

                if (count($versions) > 0) {
                    $latestStableNormVersNo = '';
                    foreach ($versions as $versionData) {
                        $versionNo = $versionData->version;
                        $normVersNo = $versionData->version_normalized;
                        $stability = VersionParser::normalizeStability(VersionParser::parseStability($versionNo));

                        // only use stable version numbers
                        if ($stability === 'stable' && version_compare($normVersNo, $latestStableNormVersNo) >= 0) {
                            $latestStableVersNo = $versionNo;
                            $latestStableNormVersNo = $normVersNo;
                        }
                    }
                }

                /**
                 * prepare result
                 */
                $moduleVersions[] = [
                    'name' => $name,
                    'installed_version' => $package->version,
                    'newest_version' => $latestStableVersNo
                ];
            }
        }

        return $moduleVersions;
    }
}