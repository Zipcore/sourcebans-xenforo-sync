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

class AWickham_SourceBansSync_Model_SourceBans
{
	protected $db;
		
	protected function _getDb()
	{
		if (!isset($this->_db))
		{
			$options = XenForo_Application::get('options');
			$this->_db = Zend_Db::factory($options->AWickham_SourceBansSync_Database['adapter'], $options->AWickham_SourceBansSync_Database);
		} 
		
		return $this->_db;
	}
	
	protected function _getTablePrefix()
	{
		return XenForo_Application::get('options')->AWickham_SourceBansSync_Database['table_prefix'];
	}
}