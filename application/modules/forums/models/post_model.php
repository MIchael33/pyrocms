<?php
class Post_model extends Model {

	private $fields = array('postID', 'forumID', 'authorID', 'parentID', 'post_title', 'post_text', 'post_type',
						'post_locked', 'post_hidden', 'post_date', 'post_viewcount', 'smileys');
	
	private $post_table = 'forum_posts';
	

	/**
	 * Count Topics in Forum
	 *
	 * How many topics (posts which have no parent / are not a reply to anything) are in a forum.
	 * 
	 * @access       public
	 * @param        int 	[$forum_id] 	Which forum should be counted
	 * @return       int 	Returns a count of how many topics there are
	 * @package      forums
	 */
	public function countTopicsInForum($forum_id)
	{
		$this->db->where(array('parent_id' => 0, 'forum_id' => $forum_id));
		
		return $this->db->from($this->post_table)->count_all_results();		
	}
	

	/**
	 * Count Replies in Forum
	 *
	 * How many replies have been made to topics in a forum.
	 * 
	 * @access       public
	 * @param        int 	[$forum_id] 	Which forum should be counted
	 * @return       int 	Returns a count of how many replies there are
	 * @package      forums
	 */
	public function countRepliesInForum($forum_id)
	{
		$this->db->where('parent_id >', 0);
		$this->db->where('forum_id', $forum_id);
		
		return $this->db->from($this->post_table)->count_all_results();		
	}

	/**
	 * Count Posts in Topic
	 *
	 * How many posts are in a topic.
	 * 
	 * @access       public
	 * @param        int 	[$forum_id] 	Which topic should be counted
	 * @return       int 	Returns a count of how many posts there are
	 * @package      forums
	 */
	public function countPostsInTopic($topic_id)
	{
		$this->db->or_where(array('id' => $topic_id, 'parent_id' => $topic_id));
		
		return $this->db->from($this->post_table)->count_all_results();		
	}

	/**
	 * Get Posts in Topic
	 *
	 * Get all posts in a topic.
	 * 
	 * @access       public
	 * @param        int 	[$forum_id] 	Which topic should be counted
	 * @return       int 	Returns a count of how many posts there are
	 * @package      forums
	 */
	public function getPostsInTopic($topic_id)
	{
		$this->db->or_where(array('id' => $topic_id, 'parent_id' => $topic_id));
		$this->db->order_by('created_on');
		return $this->db->get($this->post_table)->result();		
	}

	/**
	 * Get Posts in Topics
	 *
	 * Return an array of all topics in a forum.
	 * 
	 * @access       public
	 * @param        int 	[$forum_id] 	Which forum should be counted
	 * @return       int 	Returns a count of how many topics there are
	 * @package      forums
	 */
	public function getTopicsInForum($forum_id)
	{
		$this->db->where(array('forum_id' => $forum_id, 'parent_id' => 0));
		$query = $this->db->get($this->post_table);
		return $query->result();		
	}

	/**
	 * Get latest post in Forum
	 *
	 * How many replies have been made to topics in a forum.
	 * 
	 * @access       public
	 * @param        int 	[$forum_id] 	Which forum should be counted
	 * @return       int 	Returns a count of how many replies there are
	 * @package      forums
	 */
	public function getLastPostInForum($forum_id)
	{
		$this->db->where('forum_id', $forum_id);
		$this->db->order_by('created_on DESC');
		$this->db->limit(1);
		return $this->db->get($this->post_table)->row();
	}

	/**
	 * Get latest post in Forum
	 *
	 * How many replies have been made to topics in a forum.
	 * 
	 * @access       public
	 * @param        int 	[$forum_id] 	Which forum should be counted
	 * @return       int 	Returns a count of how many replies there are
	 * @package      forums
	 */
	public function getLastPostInTopic($topic_id)
	{
		$this->db->or_where(array('id' => $topic_id, 'parent_id' => $topic_id));
		$this->db->order_by('created_on DESC');
		$this->db->limit(1);
		return $this->db->get($this->post_table)->row();
	}
	

	/**
	 * Get topic
	 *
	 * Get the basic information about a topic (not the posts within it)
	 * 
	 * @access       public
	 * @param        int 	[$topic_id] 	Which topic to look at
	 * @return       int 	Returns an object containing a topic
	 * @package      forums
	 */
	function getTopic($topic_id = 0)
    {
		$this->db->where(array('id' => $topic_id, 'parent_id' => 0));
		return $this->db->get($this->post_table)->row();
	}
	

	// Each time a user looks at a topic it will add 1
	function increaseViewcount($topic_id = 0)
	{
		$this->db->set('view_count = view_count + 1');
		$this->db->where('id', (int) $topic_id);
		$this->db->update($this->post_table);
	}
	

	
	function newTopic($user_id, $topic, $forum)
	{
		$this->load->helper('date');

		$insert = array(
			'forum_id' 		=> $forum->id,
			'author_id' 	=> $user_id,
			'parent_id' 	=> 0,
			'title' 		=> $this->input->xss_clean($topic->title),
			'text' 			=> $this->input->xss_clean($topic->text),
			'created_on' 	=> now(),
			'view_count' 	=> 0,
        );
		
        $this->db->insert($this->post_table, $insert);
		
        return $this->db->insert_id();
	}
	
	function newReply($user_id, $reply, $topic)
	{
		$this->load->helper('date');

		$insert = array(
			'forum_id' 		=> 0,
			'author_id' 	=> $user_id,
			'parent_id' 	=> $topic->id,
			'title' 		=> '',
			'text' 			=> $this->input->xss_clean($reply->text),
			'created_on' 	=> now(),
			'view_count' 	=> 0,
        );
		
        $this->db->insert($this->post_table, $insert);
		
        return $this->db->insert_id();
	}
	
	function getReply($reply_id = 0)
	{
		$this->db->where('id', $reply_id);
		$this->db->where('parent_id', 0);
		return $this->db->get($this->post_table, 1)->row();
	}
	
	function getPost($post_id = 0)
	{
		$this->db->where('id', $post_id);
		return $this->db->get($this->post_table, 1)->row();
	}
	
/*

	function getTotalPostsInTopic($topicID = 0)
    {
		$this->where = "postID = $topicID OR parentID = $topicID";
		$this->orderby = "post_date ASC";
		return $this->getList();
	}
	


	function getList($limit = 0, $offset = 0)
    {

		$this->db->select('postID');
		$this->db->from($this->post_table);
		if($this->where != '') 		$this->db->where($this->where);
		if($limit > 0)   			$this->db->limit($limit, $offset);
		if($this->orderby != '') 	$this->db->orderby($this->orderby);
		else					 	$this->db->orderby('post_date', 'DESC');
		
		$query = $this->db->get();
		
		$this->where = "";
		$this->orderBy = "";
		
		$data = array();

		foreach($query->result_array() as $row):
			$data[] = $this->get($row['postID']);
		endforeach;

		return $data;

	}
	
	
    function get($postID = 0, $strip = false)
    {
		$this->postID = ($postID > 0) ? $postID : $this->postID;
        		
		// Get the article with this id
		$this->db->select($this->fields);
	
		$this->db->where('postID', $this->postID);
		$query = $this->db->get($this->post_table, 1);
				
		$this->where = "";
		$this->orderBy = "";
		
		$row = array();
		
		// Convert query to array and give all values a default empty string
		foreach($query->row_array() as $key => $value):			
			$row[$key] = ( isset($value) ) ? ($strip ? stripslashes($value) : $value) : '';
		endforeach;
						
		return $row;
    }


	function getLastPost($type, $typeID = 0)
	{
		switch($type):
			case 'user':
				$this->where = "authorID = $typeID";
			break;
			
			case 'forum':
				$this->where = "forumID = $typeID";
			break;
			
			case 'topic':
				$this->where = "postID = $typeID OR parentID = $typeID";
			break;
		endswitch;
					
		$this->orderby = "post_date DESC";
		$post = $this->getList(1);
		
		// Got one? Goody!
		if(count($post) == 1):
			return $post[0];
		endif;
		
		return false;
	}
	
	// Each time a user looks at a topic it will add 1
	function increaseViewcount($topicID = 0)
	{
		$this->db->query('UPDATE '. $this->post_table .' SET post_viewcount = post_viewcount + 1 WHERE postID = '.intval($topicID));
	}
	

	function editReply($postID, $text)
	{
		$update_data = array(
              'post_text' => $this->input->xss_clean($text)
        );
		$this->db->where('postID', $postID);
		$this->db->update($this->post_table, $update_data);
		return ($this->db->affected_rows() > 0);
	}

	function deleteReply($postID)
	{
       	$this->db->where('postID', $postID);
		$this->db->delete($this->post_table);
		return ($this->db->affected_rows() > 0);
	}	

	function postTopic($user_id, $forum_id = 0, $title = "", $text = "", $smileys = 0)
	{
		$forum = $this->forum_model->getForum($forum_id);
		if(!empty($forum))
		{
			$post_type = 1;
			$insert_data = array(
			   'forumID' 		=>	$forum_id,
               'authorID' 		=>	$user_id,
               'parentID' 		=>	0,
               'post_title' 	=>	$this->input->xss_clean($title),
               'post_text' 		=>	$this->input->xss_clean($text),
               'post_type' 		=>	$this->input->xss_clean($post_type),
               'post_locked' 	=>	0,
               'post_hidden' 	=>	0,
               'post_date' 		=>	gmdate('Y-m-d H:i:s'),
               'post_viewcount' =>	0,
			   'smileys'		=>	$this->input->xss_clean($smileys)
            );
			$this->db->insert($this->post_table, $insert_data);
			return ($this->db->affected_rows() > 0);
			
		} else {
			return false;
		}
	}

	function AddNotify($topicID, $user_id)
	{
		// to-do
		// table TopicSubscriptions
		// fields id, topicID, userID
		
		// Check if allready in the list
		$this->db->select('*');
		$this->db->where('topicID', $topicID);
		$this->db->where('userID', $user_id);
		$query = $this->db->get($this->subscriptionsTable);

		if($query->num_rows() == 0)
		{
			$insert_data = array(
	               'topicID' 		=>	$topicID,
	               'userID' 		=>	$user_id
	        );	
			$this->db->insert($this->subscriptionsTable, $insert_data);
			return ($this->db->affected_rows() > 0);
		}
	}

	function NewPostNotify($topicID, $user_id)
	{
		$mail_array = array();

		$this->db->select('*');
		$this->db->where('topicID', $topicID);
		$this->db->where('userID !=', $user_id);
		$query = $this->db->get($this->subscriptionsTable);
		
		$i=0;
		foreach ($query->result_array() as $row)
		{
			$mail_array[$i]['user_id'] = $row['userID'];
			$mail_array[$i]['user_name'] = getUserFullNameFromId($row['userID']);			
			$mail_array[$i]['user_email'] = getUserProperty('email', $row['userID']);
			$i++;
		}

		$this->load->library('email');
		foreach ($mail_array as $user_data)
		{
		    $this->email->clear();
		
		    $this->email->to($user_data['user_email']);
		    $this->email->from($this->config->item('admin_email'));
		    $this->email->subject('New Message in Topic');
		    $this->email->message('Dear '.$user_data['user_name'].'. <br>A new message has been posted in topic: '.site_url('forums/topics/view_topic/'.$topicID).' <br>To unsubscribe please visit: '.site_url('forums/topics/unsubscribe/'.$topicID).' ');
		    $this->email->send();
		}
	}

	function unSubscribe($topicID = 0, $user_id = 0)
	{
		$this->db->where('topicID', $topicID);
		$this->db->where('userID', $user_id);
		$this->db->delete($this->subscriptionsTable);
		return ($this->db->affected_rows() > 0);
	}

	function postParse($text, $smileys = 1)
	{
		$text = parse_bbcode($text);
		if($smileys) $text = parse_smileys($text);
		$text = nl2br(stripslashes($text));
		return $text;
	}
	*/
}
?>