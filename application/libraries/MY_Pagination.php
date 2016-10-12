<?php
class MY_Pagination extends CI_Pagination 
{
	var $cover_page_open		= '';
	var $cover_page_close		= '';
	var $display_info_page		= false;
	var $route					= '';
	var $routePage				= '';
	var $classTag				= '';
	var $options				= array();
	var $original_link			= '';

	public function __construct()
	{
		parent::__construct(); 
	}
	
	function createConfig($type = null, $suffix = null) {
		if($type == 'admin') {
			$this->first_link = 'Start';
			$this->first_tag_open = '<div class="button2-right"><div class="start">';
			$this->first_tag_close = '</div></div>';
			
			$this->prev_link = 'Prev';
			$this->prev_tag_open = '<div class="button2-right"><div class="prev">';
			$this->prev_tag_close = '</div></div>';
			
			$this->next_link = 'Next';
			$this->next_tag_open = '<div class="button2-left"><div class="next">';
			$this->next_tag_close = '</div></div>';
			
			$this->last_link = 'End';
			$this->last_tag_open = '<div class="button2-left"><div class="end">';
			$this->last_tag_close = '</div></div>';
			
			$this->cover_page_open = '<div class="button2-left"><div class="page">';
			$this->cover_page_close = '</div></div>';
			
			$this->cur_tag_open = '<span>';
			$this->cur_tag_close = '</span>';
			
			$this->display_info_page = true;
		}elseif($type == 'front-end') {

			$this->first_link = FALSE;
			$this->last_link   = FALSE;
	
			$this->prev_link = '<';
			$this->prev_tag_open = '';
			$this->prev_tag_close = '';
				
			$this->next_link = '>';
			$this->next_tag_open = '';
			$this->next_tag_close = '';
				
			$this->cover_page_open = '';
			$this->cover_page_close = '';
				
			$this->cur_tag_open = '<strong>';
			$this->cur_tag_close = '</strong>';
			
			$this->full_tag_open = '<div class="text-center"><div class="pagination hidden-print alternate text-center" id="pagination_bottom">';
			$this->full_tag_close = '</div></div>';
				
			$this->display_info_page = false;
		}
	}
	

	
	function create_ajax()
	{
		// If our item count or per-page total is zero there is no need to continue.
		if ($this->total_rows == 0 OR $this->per_page == 0)
		{
			return '';
		}
		
		// Calculate the total number of pages
		$num_pages = ceil($this->total_rows / $this->per_page);
		
		// Is there only one page? Hm... nothing more to do here then.
		if ($num_pages == 1)
		{
			return '';
		}
		
		// Set the base page index for starting page number
		if ($this->use_page_numbers)
		{
			$base_page = 1;
		}
		else
		{
			$base_page = 0;
		}
		
		// Determine the current page number.
		$CI =& get_instance();
		
		if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
		{
			if ($CI->input->get($this->query_string_segment) != $base_page)
			{
				$this->cur_page = $CI->input->get($this->query_string_segment);
		
				// Prep the current page - no funny business!
				$this->cur_page = (int) $this->cur_page;
			}
		}
		else
		{
			if ($CI->uri->segment($this->uri_segment) != $base_page)
			{
				$this->cur_page = $CI->uri->segment($this->uri_segment);
		
				// Prep the current page - no funny business!
				$this->cur_page = (int) $this->cur_page;
			}
		}

		// Set current page to 1 if using page numbers instead of offset
		if ($this->use_page_numbers AND $this->cur_page == 0)
		{
			$this->cur_page = $base_page;
		}
		
		$this->num_links = (int)$this->num_links;
		
		if ($this->num_links < 1)
		{
			show_error('Your number of links must be a positive number.');
		}
		
		if ( ! is_numeric($this->cur_page))
		{
			$this->cur_page = $base_page;
		}
		
		// Is the page number beyond the result range?
		// If so we show the last page
		if ($this->use_page_numbers)
		{
			if ($this->cur_page > $num_pages)
			{
				$this->cur_page = $num_pages;
			}
		}
		else
		{
			if ($this->cur_page > $this->total_rows)
			{
				$this->cur_page = ($num_pages - 1) * $this->per_page;
			}
		}
		
		$uri_page_number = $this->cur_page;
		
		if ( ! $this->use_page_numbers)
		{
			$this->cur_page = floor(($this->cur_page/$this->per_page) + 1);
		}
		
		// Calculate the start and end numbers. These determine
		// which number to start and end the digit links with
		$start = (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
		$end   = (($this->cur_page + $this->num_links) < $num_pages) ? $this->cur_page + $this->num_links : $num_pages;
		
		// Is pagination being used over GET or POST?  If get, add a per_page query
		// string. If post, add a trailing slash to the base URL if needed
		if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
		{
			$this->base_url = rtrim($this->base_url).'&amp;'.$this->query_string_segment.'=';
		}
		else
		{
			$this->base_url = rtrim($this->base_url, '/') .'/';
		}
		
		// And here we go...
		$output = '';
		
		// Render the "First" link
		if  ($this->first_link !== FALSE AND $this->cur_page > ($this->num_links + 1))
		{
			$first_url = ($this->first_url == '') ? $this->base_url : $this->first_url;
			$output .= $this->first_tag_open.'<a '.$this->anchor_class.'href="'.$first_url.'">'.$this->first_link.'</a>'.$this->first_tag_close;
		}
		
		// Render the "previous" link
		if  ($this->prev_link !== FALSE AND $this->cur_page != 1)
		{
			if ($this->use_page_numbers)
			{
				$i = $uri_page_number - 1;
			}
			else
			{
				$i = $uri_page_number - $this->per_page;
			}
		
			if ($i == 0 && $this->first_url != '')
			{
				
				$output .= $this->prev_tag_open.'<a '.$this->anchor_class.'href="'.$this->first_url.'">'.$this->prev_link.'</a>'.$this->prev_tag_close;
				$result['prev'] = 1;
			}
			else
			{

				$i = ($i == 0) ? 1 : $this->prefix.$i.$this->suffix;
				$output .= $this->prev_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$i.'">'.$this->prev_link.'</a>'.$this->prev_tag_close;
			
				$result['prev'] = $i;
			}
			
	
		}
		
		// Render the pages
		if ($this->display_pages !== FALSE)
		{
			$output .= $this->cover_page_open;
			// Write the digit links
			for ($loop = $start -1; $loop <= $end; $loop++)
			{
				if ($this->use_page_numbers)
				{
					$i = $loop;
				}
				else
				{
					$i = ($loop * $this->per_page) - $this->per_page;
				}
		
				if ($i >= $base_page)
				{
					if ($this->cur_page == $loop)
					{
						$output .= $this->cur_tag_open.$loop.$this->cur_tag_close; // Current page
						$result['current'] = $loop;
					}
					else
					{
						$n = ($i == $base_page) ? '' : $i;
				
						if ($n == '' && $this->first_url != '')
						{
							$output .= $this->num_tag_open.'<a '.$this->anchor_class.'href="'.$this->first_url.'">'.$loop.'</a>'.$this->num_tag_close;
							
							$result['page-1'] = 1;
						}
						else
						{
							$n = ($n == '') ? $this->suffix : $this->prefix.$n.$this->suffix;
				
							if(empty($n))
								$n = 1;
							$output .= $this->num_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$n.'">'.$loop.'</a>'.$this->num_tag_close;
							
							$result['page-'.$n] = $n;
						}
		
					}
				}
		   }
		   $output .= $this->cover_page_close;
	 	}
	 	// Render the "next" link
	 	if ($this->next_link !== FALSE AND $this->cur_page < $num_pages)
	 	{
	 		if ($this->use_page_numbers)
	 		{
	 			$i = $this->cur_page + 1;
	 		}
	 		else
	 		{
	 			$i = ($this->cur_page * $this->per_page);
	 		}
	 	
	 		$output .= $this->next_tag_open.'<a '.$this->anchor_class.'href="javascript:;" data-page="'.$i.'">'.$this->next_link.'</a>'.$this->next_tag_close;
	 		
	 		$result['next'] = $i;
	 	}
	 	
	 	// Render the "Last" link
	 	if ($this->last_link !== FALSE AND ($this->cur_page + $this->num_links) < $num_pages)
	 	{
	 		if ($this->use_page_numbers)
	 		{
	 			$i = $num_pages;
	 		}
	 		else
	 		{
	 			$i = (($num_pages * $this->per_page) - $this->per_page);
	 		}
	 		$output .= $this->last_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$this->prefix.$i.$this->suffix.'">'.$this->last_link.'</a>'.$this->last_tag_close;
	 	}
	 	
	 	if($this->display_info_page == true) {
	 		$output .= '<div class="limit">Page '.$this->cur_page.' Of '.$num_pages.' - Total: <b>'.$this->total_rows.'</b></div>';
	 	}
	 	
	 	// Kill double slashes.  Note: Sometimes we can end up with a double slash
	 	// in the penultimate link so we'll kill all double slashes.
	 	$output = preg_replace("#([^:])//+#", "\\1/", $output);
	 	
	 	// Add the wrapper HTML if exists
	 	$output = $this->full_tag_open.$output.$this->full_tag_close;
	 	
	 	return $result;
	 	
	}
	
	function create_links2()
	{
		// If our item count or per-page total is zero there is no need to continue.
		if ($this->total_rows == 0 OR $this->per_page == 0)
		{
			return '';
		}
	
		// Calculate the total number of pages
		$num_pages = ceil($this->total_rows / $this->per_page);
	
		// Is there only one page? Hm... nothing more to do here then.
		if ($num_pages == 1)
		{
			return '';
		}
	
		// Set the base page index for starting page number
		if ($this->use_page_numbers)
		{
			$base_page = 1;
		}
		else
		{
			$base_page = 0;
		}
	
		// Determine the current page number.
		$CI =& get_instance();
	
		if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
		{
			if ($CI->input->get($this->query_string_segment) != $base_page)
			{
				$this->cur_page = $CI->input->get($this->query_string_segment);
	
				// Prep the current page - no funny business!
				$this->cur_page = (int) $this->cur_page;
			}
		}
		else
		{
			if ($CI->uri->segment($this->uri_segment) != $base_page)
			{
				$this->cur_page = $CI->uri->segment($this->uri_segment);
	
				// Prep the current page - no funny business!
				$this->cur_page = (int) $this->cur_page;
			}
		}
	
		// Set current page to 1 if using page numbers instead of offset
		if ($this->use_page_numbers AND $this->cur_page == 0)
		{
			$this->cur_page = $base_page;
		}
	
		$this->num_links = (int)$this->num_links;
	
		if ($this->num_links < 1)
		{
			show_error('Your number of links must be a positive number.');
		}
	
		if ( ! is_numeric($this->cur_page))
		{
			$this->cur_page = $base_page;
		}
	
		// Is the page number beyond the result range?
		// If so we show the last page
		if ($this->use_page_numbers)
		{
			if ($this->cur_page > $num_pages)
			{
				$this->cur_page = $num_pages;
			}
		}
		else
		{
			if ($this->cur_page > $this->total_rows)
			{
				$this->cur_page = ($num_pages - 1) * $this->per_page;
			}
		}
	
		$uri_page_number = $this->cur_page;
	
		if ( ! $this->use_page_numbers)
		{
			$this->cur_page = floor(($this->cur_page/$this->per_page) + 1);
		}
	
		// Calculate the start and end numbers. These determine
		// which number to start and end the digit links with
		$start = (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
		$end   = (($this->cur_page + $this->num_links) < $num_pages) ? $this->cur_page + $this->num_links : $num_pages;
	
		// Is pagination being used over GET or POST?  If get, add a per_page query
		// string. If post, add a trailing slash to the base URL if needed
		if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
		{
			$this->base_url = rtrim($this->base_url).'&amp;'.$this->query_string_segment.'=';
		}
		else
		{
			$this->base_url = rtrim($this->base_url, '/') .'/';
		}
	
		// And here we go...
		$output = '';
	
		// Render the "First" link
		if  ($this->first_link !== FALSE AND $this->cur_page > ($this->num_links + 1))
		{
			$first_url = ($this->first_url == '') ? $this->base_url : $this->first_url;
			$output .= $this->first_tag_open.'<a '.$this->anchor_class.'href="'.$first_url.'">'.$this->first_link.'</a>'.$this->first_tag_close;
		}
	
		// Render the "previous" link
		if  ($this->prev_link !== FALSE AND $this->cur_page != 1)
		{
			if ($this->use_page_numbers)
			{
				$i = $uri_page_number - 1;
			}
			else
			{
				$i = $uri_page_number - $this->per_page;
			}
	
			if ($i == 0 && $this->first_url != '')
			{
				$output .= $this->prev_tag_open.'<a '.$this->anchor_class.'href="'.$this->first_url.'">'.$this->prev_link.'</a>'.$this->prev_tag_close;
			}
			else
			{
				$i = ($i == 0) ? '' : $this->prefix.$i.$this->suffix;
				$output .= $this->prev_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$i.'">'.$this->prev_link.'</a>'.$this->prev_tag_close;
			}
	
		}
	
		// Render the pages
		if ($this->display_pages !== FALSE)
		{
			$output .= $this->cover_page_open;
			// Write the digit links
			for ($loop = $start -1; $loop <= $end; $loop++)
			{
			if ($this->use_page_numbers)
			{
			$i = $loop;
			}
			else
			{
			$i = ($loop * $this->per_page) - $this->per_page;
			}
	
			if ($i >= $base_page)
			{
			if ($this->cur_page == $loop)
			{
			$output .= $this->cur_tag_open.$loop.$this->cur_tag_close; // Current page
			}
			else
			{
			$n = ($i == $base_page) ? '' : $i;
	
			if ($n == '' && $this->first_url != '')
			{
			$output .= $this->num_tag_open.'<a '.$this->anchor_class.'href="'.$this->first_url.'">'.$loop.'</a>'.$this->num_tag_close;
			}
			else
			{
				$n = ($n == '') ? $this->suffix : $this->prefix.$n.$this->suffix;
	
				$output .= $this->num_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$n.'">'.$loop.'</a>'.$this->num_tag_close;
			}
			}
				}
			}
			$output .= $this->cover_page_close;
			}
			// Render the "next" link
			if ($this->next_link !== FALSE AND $this->cur_page < $num_pages)
			{
			if ($this->use_page_numbers)
			{
			$i = $this->cur_page + 1;
	}
	else
	{
	$i = ($this->cur_page * $this->per_page);
	}
	 
	$output .= $this->next_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$this->prefix.$i.$this->suffix.'">'.$this->next_link.'</a>'.$this->next_tag_close;
	}
	 
	// Render the "Last" link
	if ($this->last_link !== FALSE AND ($this->cur_page + $this->num_links) < $num_pages)
	{
			if ($this->use_page_numbers)
			{
			$i = $num_pages;
	}
	else
	{
	$i = (($num_pages * $this->per_page) - $this->per_page);
	}
	$output .= $this->last_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$this->prefix.$i.$this->suffix.'">'.$this->last_link.'</a>'.$this->last_tag_close;
	}
	 
	if($this->display_info_page == true) {
	$output .= '<div class="limit">Page '.$this->cur_page.' Of '.$num_pages.' - Total: <b>'.$this->total_rows.'</b></div>';
	}
	 
	// Kill double slashes.  Note: Sometimes we can end up with a double slash
	 	// in the penultimate link so we'll kill all double slashes.
		 	$output = preg_replace("#([^:])//+#", "\\1/", $output);
	 
	// Add the wrapper HTML if exists
	$output = $this->full_tag_open.$output.$this->full_tag_close;
	 
	return $output;
	 
	}
}