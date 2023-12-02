<?php

/**
 * AbraFlexi Tools  - AbraFlexi backup
 *
 * @author     Vítězslav Dvořák <vitex@vitexsoftware.com>
 * @copyright  2023 Vitex Software
 */

namespace VitexSoftware\AbraflexiBackup;

require_once '../vendor/autoload.php';

define('EASE_APPNAME', 'AbraFlexi Backup');

if ($argc == 0 && empty(\Ease\Shared::cfg('ABRAFLEXI_COMPANY'))) {
    echo "abraflexi-backup [company_code] [backup/directory] [path/to/.env]\n";
} else {
    \Ease\Shared::init(['ABRAFLEXI_URL', 'ABRAFLEXI_LOGIN', 'ABRAFLEXI_PASSWORD'], (array_key_exists(1, $argv) && pathinfo($argv[1], PATHINFO_EXTENSION) == 'env') ? $argv['1'] : ((array_key_exists(3, $argv) && pathinfo($argv[3], PATHINFO_EXTENSION) == 'env') ? $argv[3] : ''));
    $srcOptions = ['company' => (array_key_exists(1, $argv) && pathinfo($argv[1], PATHINFO_EXTENSION) != 'env') ?  $argv[1] : \Ease\Shared::cfg('ABRAFLEXI_COMPANY')];
    $source = new \AbraFlexi\Company($srcOptions['company'], $srcOptions);
    if (\Ease\Shared::cfg('APP_DEBUG')) {
        $source->logBanner();
    }
    $originalName = null;
    if ($source->lastResponseCode == 200) {
        $backupFile = (array_key_exists(2, $argv) && pathinfo($argv[1], PATHINFO_EXTENSION) != 'env' ? $argv[2] : \Ease\Functions::cfg('ABRAFLEXI_BACKUP_DIRECTORY', getcwd())) . DIRECTORY_SEPARATOR . $srcOptions['company'] . date('Y-m-d_h:m:s') . '.winstorm-backup';
        if ($source->saveBackupTo($backupFile)) {
            $source->addStatusMessage(sprintf(_('backup %s %s saved'), $backupFile, \Ease\Functions::humanFilesize($source->curlInfo['download_content_length'])), 'success');
        } else {
            $source->addStatusMessage(_('error saving backup'), 'error');
        }
    }
}
