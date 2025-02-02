<?php

namespace RachidLaasri\LaravelInstaller\Controllers;

use Illuminate\Routing\Controller;
use RachidLaasri\LaravelInstaller\Helpers\EnvironmentManager;
use RachidLaasri\LaravelInstaller\Helpers\FinalInstallManager;
use RachidLaasri\LaravelInstaller\Helpers\InstalledFileManager;
use RachidLaasri\LaravelInstaller\Events\LaravelInstallerFinished;

class FinalController extends Controller
{
    /**
     * Update installed file and display finished view.
     *
     * @param InstalledFileManager $fileManager
     * @return \Illuminate\View\View
     */
    public function finish(InstalledFileManager $fileManager, FinalInstallManager $finalInstall, EnvironmentManager $environment)
    {
        $finalMessages = $finalInstall->runFinal();
        $finalStatusMessage = $fileManager->update();
        $finalEnvFile = $environment->getEnvContent();

        // Buat symlink untuk public_html/storage di cPanel
        if (!file_exists(public_path('storage')))
            symlink(storage_path('app/public'), public_path('storage'));

        event(new LaravelInstallerFinished);

        return view('vendor.installer.finished', compact('finalMessages', 'finalStatusMessage', 'finalEnvFile'));
    }
}
