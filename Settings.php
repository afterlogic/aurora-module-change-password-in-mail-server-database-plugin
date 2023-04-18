<?php
/**
 * This code is licensed under AGPLv3 license or Afterlogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace Aurora\Modules\ChangePasswordInMailServerDatabasePlugin;

use Aurora\System\SettingsProperty;

/**
 * @property bool $Disabled
 * @property array $SupportedServers
 * @property string $DbUser
 * @property string $DbPass
 * @property string $DbName
 * @property string $DbHost
 */

class Settings extends \Aurora\System\Module\Settings
{
    protected function initDefaults()
    {
        $this->aContainer = [
            "Disabled" => new SettingsProperty(
                false,
                "bool",
                null,
                "Setting to true disables the module",
            ),
            "SupportedServers" => new SettingsProperty(
                [
                    "*"
                ],
                "array",
                null,
                "If IMAP Server value of the mailserver is in this list, password change is enabled for it. * enables it for all the servers.",
            ),
            "DbUser" => new SettingsProperty(
                "",
                "string",
                null,
                "User of database used by mailserver for storing credentials",
            ),
            "DbPass" => new SettingsProperty(
                "",
                "string",
                null,
                "Password of database used by mailserver for storing credentials",
            ),
            "DbName" => new SettingsProperty(
                "",
                "string",
                null,
                "Name of database used by mailserver for storing credentials",
            ),
            "DbHost" => new SettingsProperty(
                "localhost",
                "string",
                null,
                "Hostname of database server used by mailserver for storing credentials",
            ),
        ];
    }
}
