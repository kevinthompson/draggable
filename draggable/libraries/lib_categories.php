<?php

class Lib_categories {

	var $catrefs   = array();
	var $catlist   = array();
	var $plainlist = array();
	var $config    = array();

	function __construct()
	{
	    $this->EE =& get_instance();
	}


	// -------------------------------------------------
	
	function fetch_catlist()
	{
		$sql = 'SELECT cat_id, parent_id, cat_name, cat_url_title, cat_order FROM exp_categories WHERE group_id='.$this->config['group_id'].' ORDER BY group_id, cat_order';
		$query = $this->EE->db->query($sql);
	
		foreach($query->result_array() as $data)
		{
			$thisref = &$this->catrefs[ $data['cat_id'] ];

			$thisref['cat_id']    = $data['cat_id'];
			$thisref['cat_name']  = $data['cat_name'];
			$thisref['parent_id'] = $data['parent_id'];
			$thisref['cat_order'] = $data['cat_order'];
			$thisref['cat_url_title'] = $data['cat_url_title'];
		
			if ($data['parent_id'] == 0) {
				$this->catlist[ $data['cat_id'] ] = &$thisref;
			} else {
				$this->catrefs[ $data['parent_id'] ]['children'][ $data['cat_id'] ] = &$thisref;
			}
		}

		return $query->num_rows();
	}

	// -------------------------------------------------
	
	function nested_list()
	{
		return $this->_nested_list($this->catlist);
	}

	// -------------------------------------------------
	
	function _nested_list($arr, $level=0)
	{
		$html = PHP_EOL . ($level==0?'<ol class="ns_cats">':'<ol>');
		foreach ($arr as $v)
		{
			$editlink  = '<a href="index.php?S='.$this->config['session_id'].'&D=cp&C=admin_content&M=category_edit&cat_id='.$v['cat_id'].'&group_id='.$this->config['group_id'].'">Edit</a>';
			$deletelink = '<a href="index.php?S='.$this->config['session_id'].'&D=cp&C=admin_content&M=category_delete_conf&cat_id='.$v['cat_id'].'&group_id='.$this->config['group_id'].'">Delete</a>';

			$html .= PHP_EOL.'<li id="list_'.$v['cat_id'].'"><div>';
			$html .= '<span class="cat_name">'.$v['cat_name'].' <em class="cat_id">&nbsp; '.'ID:'.$v['cat_id'].' '.$v['cat_url_title'].'</em></span>';
			$html .= '<span class="cat_delete">'.$deletelink.'</span>';
			$html .= '<span class="cat_edit">'.$editlink.'</span>';
			$html .= '</div>';
			
			if (array_key_exists('children', $v))
			{
				$html .= $this->_nested_list($v['children'], $level+1);
			}
			$html .= '</li>'.PHP_EOL;
		}
		
		$html .= PHP_EOL . '</ol>';
		return $html;
	}



	// -------------------------------------------------
	
	function reorder($list)
	{
		$cats_compare = array();		
	
		// All categories in POST should be in our reference-list as well
		foreach($this->catrefs as $cat)
		{
			if(!isset($list[$cat['cat_id']])) return 'Category missing: '.$cat['cat_id'];
			$cats_compare[$cat['cat_id']] = $list[$cat['cat_id']];
		
		}		
		// More posted then in DB? (Equivalent vs identical: php.net/operators.comparison )
		if($cats_compare != $list) return 'Number of categories changed!';

		$cat_order   = array();
		$where       = array();
		$sql1        = "";
		$sql2        = "";
		
		foreach($list as $cat_id => $parent_id)
		{

			$parent_id = ( $parent_id=='root' ? 0 : $parent_id ); // JS returns 'root' we want 0

			if($parent_id != 0 && !isset($list[$parent_id])) return 'Orphaned category found!';

			@$cat_order[$parent_id]++;
			
			// Only update rows that have actually moved
			if($this->catrefs[$cat_id]['parent_id'] != $parent_id || $this->catrefs[$cat_id]['cat_order'] != $cat_order[$parent_id])
			{
				$sql1      .= " WHEN cat_id = $cat_id THEN $parent_id \n";
				$sql2      .= " WHEN cat_id = $cat_id THEN ".$cat_order[$parent_id]." \n";
				$where[]    = $cat_id;
			}
			
		}

		// DB->Query only when needed
		if( empty($where) )
		{
			return 'Update was sent, but there were no changes';
		}
		else
		{
			$sql  = "UPDATE exp_categories SET \n";
			$sql .= "parent_id = CASE \n" . $sql1;
			$sql .= "END \n";
			$sql .= ",cat_order = CASE \n" . $sql2;
			$sql .= "END \n";
			$sql .= "WHERE cat_id IN( ". implode(', ',$where)." )";

			$result = mysql_query($sql);
			
			// return '<pre>'.$sql  .PHP_EOL . PHP_EOL . mysql_affected_rows().' rows </pre>';
			
			return 'updated';

		}

	}

} // end class