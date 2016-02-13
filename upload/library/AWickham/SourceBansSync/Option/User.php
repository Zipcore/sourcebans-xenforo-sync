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

class AWickham_SourceBansSync_Option_User
{
	public static function verifyUsernameExists(&$username, XenForo_DataWriter $dw = null, $fieldName = null) 
	{
		if ($username != '') {
			$userModel = XenForo_Model::create('XenForo_Model_User');
			$user = $userModel->getUserByName($username);

			if (!$user) {
				$dw->error(new XenForo_Phrase('AWickham_SourceBansSync_InvalidUsername'));
				return false;
			}			
		}

		return true;
	}
}