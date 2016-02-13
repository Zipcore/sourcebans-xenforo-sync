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

class AWickham_SourceBansSync_Model_SourceBans_ServerGroup extends AWickham_SourceBansSync_Model_SourceBans
{
	public function getSourceBansServerGroupOptions($selectedGroupId)
	{
		$userGroups = array();
		$userGroups[] = array(
			'label' => '-- Select Server Group --',
			'value' => '',
			'selected' => ''
		);
		foreach ($this->_getSourceBansServerGroups() as $serverGroup)
		{
			$userGroups[] = array(
				'label' => $serverGroup['name'],
				'value' => $serverGroup['gid'],
				'selected' => ($selectedGroupId == $serverGroup['gid'])
			);
		}
		
		return $userGroups;
	}
	
	public function _getSourceBansServerGroups()
	{
		return $this->_getDb()->fetchAll('
		SELECT gid, name 
		FROM ' . $this->_getTablePrefix() . '_groups
		WHERE type = 3
		');
	}	
	

}