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

class AWickham_SourceBansSync
{
	public static function getDb()
	{
		if (!XenForo_Application::get('AWickham_SourceBansSync_Db'))
		{
			// get the options
			$options = XenForo_Application::get('options');
			$db = Zend_Db::factory(
				array(
					'host' => $dbConfig->host,
					'port' => $dbConfig->port,
					'username' => $dbConfig->username,
					'password' => $dbConfig->password,
					'dbname' => $dbConfig->dbname,
					'charset' => 'utf8'
				)
			);
			
			XenForo_Application::set('AWickham_SourceBansSync_Db', $db);
		}
		
		return XenForo_Application::get('AWickham_SourceBansSync_Db');
	}
	
	public static function verifyOption(&$database, XenForo_DataWriter $dw, $fieldName)
	{
		$dw->error(new XenForo_Phrase('source_database_connection_details_not_correct_x', array('error' => print_r('asdf', true))), $fieldName);
		return false;
	}
}