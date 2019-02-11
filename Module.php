<?php
/**
 * This code is licensed under AGPLv3 license or AfterLogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace Aurora\Modules\ChangePasswordInMailServerDatabasePlugin;

/**
 *
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0
 * @license https://afterlogic.com/products/common-licensing AfterLogic Software License
 * @copyright Copyright (c) 2019, Afterlogic Corp.
 *
 * @package Modules
 */
class Module extends \Aurora\System\Module\AbstractModule
{
	protected $oMailModule = null;
	
	/**
	 * @param CApiPluginManager $oPluginManager
	 */
	
	public function init() 
	{
		$this->subscribeEvent('Mail::ChangePassword::before', array($this, 'onBeforeChangePassword'));
	}
	
	/**
	 * 
	 * @param array $aArguments
	 * @param mixed $mResult
	 */
	public function onBeforeChangePassword($aArguments, &$mResult)
	{
		$mResult = true;
		
		$oAccount = $this->getMailModule()->GetAccount($aArguments['AccountId']);

		if ($oAccount && $this->checkCanChangePassword($oAccount) && $oAccount->getPassword() === $aArguments['CurrentPassword'])
		{
			$mResult = $this->сhangePassword($oAccount, $aArguments['NewPassword']);
		}
	}

	/**
	 * @param CAccount $oAccount
	 * @return bool
	 */
	protected function checkCanChangePassword($oAccount)
	{
		$bFound = in_array("*", $this->getConfig('SupportedServers', array()));
		
		if (!$bFound)
		{
			$oServer = $this->getMailModule()->GetServer($oAccount->ServerId);
			if ($oServer && in_array($oServer->Name, $this->getConfig('SupportedServers')))
			{
				$bFound = true;
			}
		}

		return $bFound;
	}
	
	/**
	 * @param CAccount $oAccount
	 */
	protected function сhangePassword($oAccount, $sPassword)
	{
	    $bResult = false;
	    if (0 < strlen($oAccount->getPassword()) && $oAccount->getPassword() !== $sPassword )
	    {
			$config_dbuser = $this->getConfig('DbUser','');
			$config_dbpass = $this->getConfig('DbPass','');
			$config_dbname = $this->getConfig('DbName','');
			$config_dbhost = $this->getConfig('DbHost','localhost');

			$mysqlcon=mysqli_connect($config_dbhost, $config_dbuser, $config_dbpass, $config_dbname);
			if($mysqlcon){
				$sPasshash = exec("doveadm pw -s 'ssha512' -p '".$sPassword."'");
				$sql = "UPDATE mailbox SET password='".$sPasshash."' WHERE username='".$oAccount->IncomingLogin."'";
				$bResult = mysqli_query($mysqlcon,$sql);
				if (!$bResult){
					throw new \Aurora\System\Exceptions\ApiException(\Aurora\System\Exceptions\Errs::UserManager_AccountNewPasswordUpdateError);
				}
				mysqli_close($mysqlcon);
			}else{
				throw new \Aurora\System\Exceptions\ApiException(\Aurora\System\Exceptions\Errs::UserManager_AccountNewPasswordUpdateError);
			}
	    }
	    return $bResult;
	}
	
	public function GetSettings()
	{
		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::Anonymous);
		
		$sSupportedServers = implode("\n", $this->getConfig('SupportedServers', array()));
		
		$aAppData = array(
			'SupportedServers' => $sSupportedServers,
			'DbUser' => $this->getConfig('DbUser', ''),
			'DbPass' => $this->getConfig('DbPass', ''),
			'DbName' => $this->getConfig('DbName', ''),
			'DbHost' => $this->getConfig('DbHost', ''),
		);

		return $aAppData;
	}
	
	public function UpdateSettings($SupportedServers, $DbUser, $DbPass, $DbName, $DbHost)
	{
		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::TenantAdmin);
		
		$aSupportedServers = preg_split('/\r\n|[\r\n]/', $SupportedServers);
		
		$this->setConfig('SupportedServers', $aSupportedServers);
		$this->setConfig('DbUser', $DbUser);
		$this->setConfig('DbPass', $DbPass);
		$this->setConfig('DbName', $DbName);
		$this->setConfig('DbHost', $DbHost);
		$this->saveModuleConfig();
		return true;
	}

	protected function getMailModule()
	{
		if (!$this->oMailModule)
		{
			$this->oMailModule = \Aurora\System\Api::GetModule('Mail');
		}
		
		return $this->oMailModule;
	}	
}
