<?php
namespace Anexia\Monitoring\Interfaces;

/**
 * Interface UpMonitoringInterface
 * @package Anexia\Monitoring\Interfaces
 */
interface UpMonitoringInterface
{
    /**
     * @param array $errors
     * @return mixed
     */
    public function checkUpStatus(&$errors = array());
}