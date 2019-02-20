<?php
class Pagination {

	static public function generate($page = 0, $total_pages = 0, $style= "AMS", $pagination_per_page = PAGINATION_PER_PAGE) {
		//how many pages appear to the left and right of your current page
		$adjacents = 1;
	
		if (empty($page)) {
			$page = 1;
		}
		
		$start = ($page - 1) * $pagination_per_page;
	
		// Limits
		$return = array(
		'start' => $start,
		'end'   => $pagination_per_page);
	
		// Pagination
		$last_page = ceil($total_pages / $pagination_per_page);
		
		$lpm1 = $last_page - 1;
		$prev = $page - 1;
		$next = $page + 1;        
	
		// Get the path
		$parts = parse_url($_SERVER['REQUEST_URI']);
		$target_page  = $parts['path'] . '?';
		$query_parts = array();
	
		// Break apart the query string
		if (!empty($parts['query'])) {
				parse_str($parts['query'], $query_parts);
			
			// Remove the old page
			if (!empty($query_parts['page'])) {
				unset($query_parts['page']);
			}
		}
		
		// Set the last element, so when it gets compiled it looks like 
		$query_parts['page'] = '';
		$target_page .= urldecode(http_build_query($query_parts));
		$pagination = '';
		

		if ($last_page > 1) {
			
			if($style=="AMS") : 
				$pagination .= "<div class='pagination-centered'><ul>";
			else :
				$pagination .= "<ul class='pagination pagination-sm'>";
			endif; 
			
			
			// previous button
			if ($page > 1)
				$pagination.= "<li><a href='$target_page$prev'>&laquo; "._('previous')."</a></li>";
			else
				$pagination.= "<li  class='disabled'><a>&laquo; "._('previous')."</a></li>";
				
				
			// pages
			if ($last_page < 5 + ($adjacents * 2))    {
				
				for ($counter = 1; $counter <= $last_page; $counter++)   {
					if ($counter == $page)
						$pagination.= "<li class='active'><a>$counter</a></li>";
					else
						$pagination.= "<li><a href='$target_page$counter'>$counter</a></li>";
				}
			}
			
			//enough pages to hide some
			elseif($last_page > 5 + ($adjacents * 2))     {
			
				//close to beginning; only hide later pages
				if($page < 3 + ($adjacents * 2))    {
					for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
						if ($counter == $page)
							$pagination.= "<li  class='active'><a>$counter</a></li>";
						else
							$pagination.= "<li><a href='$target_page$counter'>$counter</a></li>";
					}
				
					$pagination.= "<li class='disabled'><a>...</a></li>";
					$pagination.= "<li><a href='$target_page$lpm1'>$lpm1</a></li>";
					$pagination.= "<li><a href='$target_page$last_page'>$last_page</a></li>";
				}
		
			
				//in middle; hide some front and some back
				elseif($last_page - 1 - ($adjacents * 2) > $page && $page > ($adjacents * 2))  {
 	
					$pagination.= "<li><a href='{$target_page}1'>1</a></li>";
					$pagination.= "<li><a href='{$target_page}2'>2</a></li>";
					$pagination.= "<li class='disabled'><a>...</a></li>";
					
					for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
						if ($counter == $page)
							$pagination.= "<li class='active'><a>$counter</a></li>";
						else
							$pagination.= "<li><a href='$target_page$counter'>$counter</a></li>";
					}
	
					$pagination.= "<li class='disabled'><a>...</a></li>";
					$pagination.= "<li><a href='$target_page$lpm1'>$lpm1</a></li>";
					$pagination.= "<li><a href='$target_page$last_page'>$last_page</a></li>";
				}
				
				//close to end; only hide early pages
				else {
					
					$pagination.= "<li><a href='{$target_page}1'>1</a></li>";
					$pagination.= "<li><a href='{$target_page}2'>2</a></li>";
					$pagination.= "<li class='disabled'><a>...</a></li>";
					
					for ($counter = $last_page - (2 + ($adjacents * 2)); $counter <= $last_page; $counter++) {
						if ($counter == $page)
							$pagination.= "<li class='active'><a >$counter</a></li>";
						else
							$pagination.= "<li><a href='$target_page$counter'>$counter</a></li>";
					}
				}
		}  else {
			
			// Display all pages
			for ($counter = 1; $counter <= $last_page; $counter++) {
				if ($counter == $page)
							$pagination.= "<li class='active'><a >$counter</a></li>";
				else
							$pagination.= "<li><a href='$target_page$counter'>$counter</a></li>";
			}	
		}
		
		
		//next button
		if ($page < $counter - 1)
			$pagination.= "<li><a href='$target_page$next'>"._('next')." &raquo;</a></li>";
		else
			$pagination.= "<li class='disabled'><a>"._('next')." &raquo;</a></li>";
		
		
		if($style=="AMS") : 
				$pagination .= "</ul><div>";
		else :
				$pagination .= "</ul>";
		endif; 
		
		
		}
		
		$return['last_page']   =  $last_page;
		$return['pagination']  = $pagination;
		return $return;
	}
}
