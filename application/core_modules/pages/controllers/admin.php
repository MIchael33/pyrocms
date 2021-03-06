<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends Admin_Controller
{
	// Validation rules to be used for create and edita
	private $rules = array(
	    'title'			=> 'trim|required|max_length[60]',
	    'slug'				=> 'trim|required|alpha_dash|max_length[60]', // TODO Create new |callback__check_slug',
	    'body'				=> 'trim|required',
	    'meta_title'		=> 'trim|max_length[255]',
	    'meta_keywords'	=> 'trim|max_length[255]',
	    'meta_description'	=> 'trim',
	    'layout_file'		=> 'trim|alphadash|required',
	   // 'access_level'		=> 'trim|alphadash|required'
	);
	
	// Used to pass page id to edit validation callback
	private $page_id;
	
	function __construct()
	{
		parent::Admin_Controller();
		$this->load->model('pages_m');
		$this->load->module_model('navigation', 'navigation_m');
		$this->lang->load('pages');	
		
		$this->load->helper(array('array', 'pages'));
	}

	public function recurse_page_tree($parent_id, $open_parent_pages=array()) {
		if (!in_array($parent_id, $open_parent_pages))
		{
			return $this->pages_m->hasChildren($parent_id) ? '<ul></ul>' : '';
		}
		$pages = $this->pages_m->getChildrenByParentId($parent_id);
		if (count($pages))
		{
			foreach($pages as $page) {
				$page->has_children = $this->pages_m->hasChildren($page->id);
			}
			$this->data->pages =& $pages;
			$this->data->controller =& $this;
			$this->data->open_parent_pages = $open_parent_pages;
			return $this->load->view('admin/child_list', $this->data, true);
		}
		return "";
	}


	// Admin: List all Pages
	function index()
	{
		// get list of open parent pages from cookie
		$open_parent_pages = isset($_COOKIE['page_parent_ids']) ? explode(',', '0,'.$_COOKIE['page_parent_ids']) : array(0);
		// get the page tree
		$this->data->page_tree_html = $this->recurse_page_tree(0, $open_parent_pages);
		$this->layout->create('admin/index', $this->data);
	}
	
	function ajax_fetch_children($parent_id)
	{
		$pages = $this->pages_m->getChildrenByParentId($parent_id);
	
		foreach($pages as &$page)
		{
			$page->has_children = $this->pages_m->hasChildren($page->id);
		}
		$this->data->pages =& $pages;
		$this->load->view('admin/ajax/child_list', $this->data);
		
	}
	
	function ajax_page_details($page_id)
	{
		$page = $this->pages_m->getById($page_id);
		$page->path = $this->pages_m->getPathById($page_id);
		
	$this->load->view('admin/ajax/page_details', array('page' => $page));
	}
    
	// Admin: Create a new Page
	function create($parent_id = 0)
	{
		$this->load->library('validation');
		$this->validation->set_rules($this->rules);
	$this->validation->set_fields();
	
		// Validate the page
	if ($this->validation->run())
	    {
		if ( $this->pages_m->create($_POST) > 0 )
		{
			$this->session->set_flashdata('success', $this->lang->line('pages_create_success'));
		}
	      
		else
		{
			$this->session->set_flashdata('notice', $this->lang->line('pages_create_error'));
		}
		
		redirect('admin/pages/index');
	    }
		
		// Get the data back to the form
	    foreach(array_keys($this->rules) as $field)
	    {
		$page->$field = isset($this->validation->$field) ? $this->validation->$field : '';
	    }

	    // If a parent id was passed, fetch the parent details
	    if($parent_id > 0)
	    {
		$page->parent_id = $parent_id;
		
			$parent_page = $this->pages_m->getById($parent_id);
			$parent_page->path = $this->pages_m->getPathById($parent_id);
	    }
	    
	    // Assign data for display
	    $this->data->page =& $page;
	    $this->data->parent_page =& $parent_page;
	    
		// Get "roles" (like access levels)
		//$this->data->roles = $this->permissions_m->getRoles(array('order' => 'lowest_first'));
		//$this->data->roles_select = array_for_select(arsort($this->data->roles), 'id', 'title');	
	    
	    // Load WYSIWYG editor
		$this->layout->extra_head( $this->load->view('fragments/wysiwyg', $this->data, TRUE) );		
	    $this->layout->create('admin/form', $this->data);
	}

	// Admin: Edit a Page
	function edit($id = 0)
	{
		if (empty($id))
	    {
		redirect('admin/pages/index');
	    }
		
	    // We use this controller property for a validation callback later on
	    $this->page_id = $id;
	    
	    // Set data, if it exists
	    if (!$page = $this->pages_m->getById($id)) 
	    {
			$this->session->set_flashdata('error', $this->lang->line('pages_page_not_found_error'));
			redirect('admin/pages/create');
	    }
			
	    $this->load->library('validation');
	    $this->validation->set_rules($this->rules);
	    $this->validation->set_fields();
		
	    // Auto-set data for the page if a post variable overrides it
	    foreach(array_keys($this->rules) as $field)
	    {
		if($this->input->post($field)) $page->$field = $this->validation->$field;
	    }
	    
	    // Give validation a try, who knows, it just might work!
		if ($this->validation->run())
	    {
			// Run the update code with the POST data	
		$this->pages_m->update($id, $_POST);			
				
			// Wipe cache for this model as the data has changed
			$this->cache->delete_all('pages_m');			
			$this->session->set_flashdata('success', sprintf($this->lang->line('pages_edit_success'), $this->input->post('title')));
			redirect('admin/pages/index');
	    }
	

	    // If a parent id was passed, fetch the parent details
	    if($page->parent_id > 0)
	    {
			$parent_page = $this->pages_m->getById($page->parent_id);
			$parent_page->path = $this->pages_m->getPathById($page->parent_id);
	    }
	    
	    // Assign data for display
	    $this->data->page =& $page;
	    $this->data->parent_page =& $parent_page;
	    
		// Get "roles" (like access levels)
		//$this->data->roles = $this->permissions_m->getRoles();
		//ksort($this->data->roles);
		//$this->data->roles_select = array_for_select($this->data->roles, 'id', 'title');	
		
	    // Load WYSIWYG editor
	    $this->layout->extra_head( $this->load->view('fragments/wysiwyg', $this->data, TRUE) );		
	    $this->layout->create('admin/form', $this->data);
	}
    
	// Admin: Delete Pages
	function delete($id = 0)
	{
		// Attention! Error of no selection not handeled yet.
		$ids = ($id) ? array($id) : $this->input->post('action_to');
		
		// Go through the array of slugs to delete
		foreach ($ids as $id)
		{
			if ($id !== 1)
			{
				$deleted_ids = $this->pages_m->delete($id);
				
				// Wipe cache for this model, the content has changd
				$this->cache->delete_all('pages_m');
				$this->cache->delete_all('navigation_m');			
			}
				
			else
			{
				$this->session->set_flashdata('error', $this->lang->line('pages_delete_home_error'));
			}
		}
		
		// Some pages have been deleted
		if(!empty($deleted_ids))
		{
			// Only deleting one page
			if( count($deleted_ids) == 1 )
			{
				$this->session->set_flashdata('success', sprintf($this->lang->line('pages_delete_success'), $deleted_ids[0]));
			}			
			else // Deleting multiple pages
			{
				$this->session->set_flashdata('success', sprintf($this->lang->line('pages_mass_delete_success'), count($deleted_ids)));
			}
		}
			
		else // For some reason, none of them were deleted
		{
			$this->session->set_flashdata('notice', $this->lang->line('pages_delete_none_notice'));
		}		
		redirect('admin/pages/index');
	}
    
	
	// Callback: From create()
	/*function _check_slug($slug)
	{
		$page = $this->pages_m->getBySlug($slug, $this->input->post('lang'));
	    $languages =& $this->config->item('supported_languages');
		
	    if($page && $page->id != $this->page_id )
			{
		$this->validation->set_message('_check_slug', sprintf($this->lang->line('pages_page_already_exist_error'), $slug, $languages[$this->input->post('lang')]['name']));
	      return FALSE;
	    }
			else
			{
		return TRUE;
	    }
	}*/

}
?>
