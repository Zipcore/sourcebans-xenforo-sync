<?php

/*
    SourceBans Sync XenForo Add-on
    Copyright (C) 2011  Andrew Wickham

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class AWickham_SourceBansSync_Model_SourceBans_Admin extends AWickham_SourceBansSync_Model_SourceBans
{
	public function delete($xfUserId, $steamId)
	{
		$adminId = $this->_getDb()->fetchCol('SELECT aid FROM ' . $this->_getTablePrefix() . '_admins WHERE authid = ?', $steamId);
		$this->_getDb()->delete($this->_getTablePrefix() . '_admins_servers_groups', 'admin_id = ' . $this->_getDb()->quote($adminId));
		$this->_getDb()->delete($this->_getTablePrefix() . '_admins', 'aid = ' . $this->_getDb()->quote($adminId));
		
		// check to see if we need to start an account deleted notification
		if (XenForo_Application::get('options')->AWickham_SourceBansSync_StartDeleteConversation) {
			// message the user and tell them their new password
			$conversationDw = XenForo_DataWriter::create('XenForo_DataWriter_ConversationMaster'); 
			
			$botUser = XenForo_Application::get('options')->AWickham_SourceBansSync_BotUser;
			if ($botUser != '') 
			{
				$xfUserModel = XenForo_Model::create('XenForo_Model_User');
				$xfBotUser = $xfUserModel->getUserByName($botUser);
				$conversationDw->set('username', $botUser);
				$conversationDw->set('user_id', $xfBotUser['user_id']);
			} 
			else 
			{
				// get the visitor information, this is who we will send the message from
				$visitor = XenForo_Visitor::getInstance();
				$conversationDw->set('user_id', $visitor['user_id']);
				$conversationDw->set('username', $visitor['username']);
			}
			$conversationDw->set('title', new XenForo_Phrase('AWickham_SourceBansSync_Account')); 
			$conversationDw->addRecipientUserIds(array($xfUserId)); 
			
			// add the conversation message
			$messageDw = $conversationDw->getFirstMessageDw();
			$messageDw->set('message', new XenForo_Phrase('AWickham_SourceBansSync_AccountDeleted')); 
			$conversationDw->preSave();
			if (!$conversationDw->hasErrors()) 
			{ 
				$conversationDw->save(); 
			}			
		}
	}
	
	public function insert($xfUserId, array $insertArray)
	{
		// encrypt the password
		$clearPassword = $insertArray['password'];
		$insertArray['password'] = sha1(sha1(XenForo_Application::get('options')->AWickham_SourceBansSync_PasswordSalt . $insertArray['password']));
		
		$serverGroupId = $insertArray['server_group_id'];
		$srvgroupsId = $insertArray['srvgroups_id'];
		unset($insertArray['server_group_id']);
		
		$this->_getSrvGroup($insertArray);
		
		// insert the data into the admins table
		$this->_getDb()->insert($this->_getTablePrefix() . '_admins', $insertArray);
		$adminId = $this->_getDb()->lastInsertId($this->_getTablePrefix() . '_admins', 'aid');
		
		// build the array for the admins_servers_groups table
		$serverGroupsArray = array(
			'admin_id' => $adminId,
			'group_id' => $srvgroupsId,
			'srv_group_id' => $serverGroupId,
			'server_id' => -1
		);
		
		// insert into the admins_servers_groups table
		$this->_getDb()->insert($this->_getTablePrefix() . '_admins_servers_groups', $serverGroupsArray);
		
		// check to see if we need to start an account created notification
		if (XenForo_Application::get('options')->AWickham_SourceBansSync_StartCreateConversation) {
			// message the user and tell them their new password
			$conversationDw = XenForo_DataWriter::create('XenForo_DataWriter_ConversationMaster'); 
			
			$botUser = XenForo_Application::get('options')->AWickham_SourceBansSync_BotUser;
			if ($botUser != '') 
			{
				$xfUserModel = XenForo_Model::create('XenForo_Model_User');
				$xfBotUser = $xfUserModel->getUserByName($botUser);
				$conversationDw->set('username', $botUser);
				$conversationDw->set('user_id', $xfBotUser['user_id']);
			} 
			else 
			{
				// get the visitor information, this is who we will send the message from
				$visitor = XenForo_Visitor::getInstance();
				$conversationDw->set('user_id', $visitor['user_id']);
				$conversationDw->set('username', $visitor['username']);
			}
			$conversationDw->set('title', new XenForo_Phrase('AWickham_SourceBansSync_Account')); 
			$conversationDw->addRecipientUserIds(array($xfUserId)); 
			
			// add the conversation message
			$messageDw = $conversationDw->getFirstMessageDw();
			$messageDw->set('message', new XenForo_Phrase('AWickham_SourceBansSync_AccountCreated', array('password' => $clearPassword))); 
			$conversationDw->preSave(); 
			if (!$conversationDw->hasErrors()) 
			{ 
				$conversationDw->save(); 
			}			
		}
	}
	
	public function fetchBySteamId($steamId)
	{
		return $this->_getDb()->fetchRow('
		SELECT * FROM 
		' . $this->_getTablePrefix() . '_admins
		WHERE authid = ?
		', $steamId);
	}
	
	public function fetchAll()
	{
		return $this->_getDb()->fetchAll('
		SELECT * FROM
		' . $this->_getTablePRefix() . '_admins
		WHERE authid != \'STEAM_ID_SERVER\'
		');
	}
	
	public function update($steamId, array $updateArray)
	{
		$adminId = $this->_getDb()->fetchCol('SELECT aid FROM ' . $this->_getTablePrefix() . '_admins WHERE authid = ?', $steamId);
		
		$serverGroupsArray = array(
			'srv_group_id' => $updateArray['server_group_id'],
			'group_id' => $updateArray['srvgroups_id'],
			'server_id' => -1
		);
		unset($updateArray['server_group_id']);
		
		$this->_getSrvGroup($updateArray);
		
		$this->_getDb()->update($this->_getTablePrefix() . '_admins_servers_groups', $serverGroupsArray, 'admin_id = ' . $this->_getDb()->quote($adminId));
		$this->_getDb()->update($this->_getTablePrefix() . '_admins', $updateArray, 'aid = ' . $this->_getDb()->quote($adminId));
	}
	
	protected function _getSrvGroup(&$values)
	{
		// get the srv_group column
		$values['srv_group'] = $this->_getDb()->fetchOne('
		SELECT name FROM
		' . $this->_getTablePrefix() . '_srvgroups
		WHERE id = ?', array($values['srvgroups_id']));
		
		// remove the srvgroups_id from the insert array
		unset($values['srvgroups_id']);		
	}
}