<?php

namespace App\Utils;

// Ensure App\Service\ApiClient can be autoloaded.
// This should be handled by composer's autoloader,
// which is expected to be included by files calling ConfigLoader methods (e.g., via php/info.php).
use App\Service\ApiClient;

class ConfigLoader
{
    private static ?\Twig\Environment $twig = null;
    private static ?ApiClient $apiClient = null;

    /**
     * Initializes and/or returns the Twig Environment instance.
     * Relies on global $twig being set by config/config.php (included via php/info.php).
     * Includes a fallback initialization if global $twig is not found.
     *
     * @return \Twig\Environment|null
     */
    public static function getTwig(): ?\Twig\Environment
    {
        if (self::$twig !== null) {
            return self::$twig;
        }

        global $twig; // $twig should be initialized in config/config.php
        if (isset($twig) && $twig instanceof \Twig\Environment) {
            self::$twig = $twig;
            return self::$twig;
        }

        error_log("ConfigLoader: Global Twig environment not found or not an instance of Twig\Environment. Attempting local fallback initialization for Twig.");

        $vendorAutoload = __DIR__ . '/../../vendor/autoload.php';
        if (!file_exists($vendorAutoload)) {
            error_log("ConfigLoader: CRITICAL - Vendor autoload for Twig fallback not found at " . $vendorAutoload);
            return null;
        }
        require_once $vendorAutoload; // Ensures Twig classes are available for fallback

        $templatesDir = __DIR__ . '/../../templates';
        if (!is_dir($templatesDir)) {
            error_log("ConfigLoader: CRITICAL - Templates directory for Twig fallback not found at " . $templatesDir);
            return null;
        }

        try {
            $loader = new \Twig\Loader\FilesystemLoader($templatesDir);
            self::$twig = new \Twig\Environment($loader, ['debug' => true]); // Assign to static property
            return self::$twig;
        } catch (\Throwable $e) {
            error_log("ConfigLoader: CRITICAL - Failed to initialize Twig via fallback: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Initializes and/or returns the ApiClient instance.
     * Relies on AAPXYED_API_URL constant being defined in config/config.php.
     *
     * @return ApiClient|null
     */
    public static function getApiClient(): ?ApiClient
    {
        if (self::$apiClient !== null) {
            return self::$apiClient;
        }

        // Ensure AAPXYED_API_URL is defined (should be by config.php via phpinfo.php)
        // And ensure ApiClient class is autoloadable (via composer's autoload in phpinfo.php)
        if (!defined('AAPXYED_API_URL')) {
            error_log("ConfigLoader: CRITICAL - AAPXYED_API_URL constant is not defined. ApiClient cannot be initialized.");
            // Optionally, trigger a user error or throw an exception in a real application
            // For now, returning null or a default/mock client might be an option if the app can handle it.
            return null;
        }

        // Autoloading should handle App\Service\ApiClient if composer's autoloader is included.
        // No need for explicit require_once for ApiClient here if that's the case.

        try {
            self::$apiClient = new ApiClient(AAPXYED_API_URL);
            return self::$apiClient;
        } catch (\Throwable $e) {
            error_log("ConfigLoader: CRITICAL - Failed to initialize ApiClient: " . $e->getMessage());
            return null;
        }
    }
}
