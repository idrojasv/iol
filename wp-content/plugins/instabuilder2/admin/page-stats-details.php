<?php
if ( !isset($_GET['post_id']) )
	wp_die("ERROR: Cannot found Post ID.");

if ( !isset($_GET['variant']) )
	wp_die("ERROR: Cannot found page variation ID.");

$post_id = (int) $_GET['post_id'];
$post = get_post($post_id);
if ( !$post )
	wp_die("ERROR: Post/Page Not Found.");

$variant = $_GET['variant'];
$search_mode = ( isset($_GET['search_mode']) ) ? urldecode($_GET['search_mode']) : 'today';
$q = $search_mode;
if ( $search_mode == 'range' && isset($_GET['date_from']) && isset($_GET['date_to']) ) {
	$search_mode = array(urldecode($_GET['date_from']), urldecode($_GET['date_to']));
}

$range = $search_mode;
$stats = ib2_get_pagestats($post_id, $search_mode);
$sub_title = ( !is_array($search_mode) ) ? ucwords($search_mode) : '';
if ( $search_mode == '7 days' ) {
	$sub_title = 'Last 7 Days';
} else if ( $search_mode == '14 days' ) {
	$sub_title = 'Last 2 Weeks';
} else if ( $search_mode == '30 days' ) {
	$sub_title = 'Last 30 Days';
} else if ( $search_mode == 'range' && is_array($search_mode) ) {
	$sub_title = date_i18n("F d, Y", strtotime(urldecode($_GET['date_from']))) . ' - ' . date_i18n("F d, Y", strtotime(urldecode($_GET['date_to'])));
}

$reports = false;
$vw = array('v' => 'a', 'c' => 0);
if ( $stats ) {
	$a_cr = 0;
	$i = 0;
	$reports = array();
	$control = array();
	foreach ( $stats as $stat ) {
		if ( $stat->variant != 'a' && $stat->variant != $variant ) continue;
		
		$visitors = $stat->visitors;
		$conversions = $stat->conversions;
		$cr = @round(($conversions / $visitors ) * 100, 2);
		$diff = ( $stat->variant == 'a' ) ? "-" : ($cr - $a_cr) . '%';
		if ( $stat->variant == 'a' ) {
			$a_cr = $cr;
			$control = array($visitors, $conversions);
		}
		
		$p_value = ( $stat->variant == 'a' ) ? 0 : ib2_cumnormdist(ib2_zscore($control, array($visitors, $conversions)));
		$confidence = ( $stat->variant == 'a' ) ? 0 : @round($p_value*100, 2);
	
		$reports[$i]->weight = ( !empty($stat->weight) ? $stat->weight : 100);
		$reports[$i]->variant = $stat->variant;
		$reports[$i]->visitors = $visitors;
		$reports[$i]->conversions = $conversions;
		$reports[$i]->conversion_rate = $cr;
		$reports[$i]->improvement = $diff;
		$reports[$i]->p_value = round($p_value, 2);
		$reports[$i]->confidence = ( $stat->variant == 'a' ) ? '-' : $confidence . '%';
		
		if ( $confidence > $vw['c'] )
			$vw = array('v' => $stat->variant, 'c' => $confidence);
			
		$i++;
	}
}
?>
<div class="page-header">
	<?php
	echo '<h2>Stats: ' . esc_attr($post->post_title) . ' :: Variation ' . strtoupper($variant) . ' <small><a href="' . admin_url('admin.php?page=ib2-dashboard&post_id=' . $post_id . '&mode=stats') . '">&laquo; Back to Main Stats</a></small></h2>'; 
	?>
</div>

<div class="search-form" style="text-align:right; margin-bottom:18px">
	<form class="form-inline" id="stats-form" role="form" method="get" action="<?php echo admin_url('admin.php'); ?>">
		<input type="hidden" name="page" value="ib2-dashboard">
		<input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
		<input type="hidden" name="mode" value="stats">
		<input type="hidden" name="variant" value="<?php echo $variant; ?>">
		<div class="form-group">
			<label for="result_mode">Display Results From</label>
			<select name="search_mode" id="search-mode" class="form-control">
				<option value="today"<?php echo ( $q == 'today' ? ' selected="selected"' : ''); ?>>Today</option>
				<option value="yesterday"<?php echo ( $q == 'yesterday' ? ' selected="selected"' : ''); ?>>Yesterday</option>
				<option value="7 days"<?php echo ( $q == '7 days' ? ' selected="selected"' : ''); ?>>Last 7 Days</option>
				<option value="14 days"<?php echo ( $q == '14 days' ? ' selected="selected"' : ''); ?>>Last 2 Weeks</option>
				<option value="30 days"<?php echo ( $q == '30 days' ? ' selected="selected"' : ''); ?>>Last 30 Days</option>
				<option value="all"<?php echo ( $q == 'all' ? ' selected="selected"' : ''); ?>>All Time</option>
				<option value="range"<?php echo ( $q == 'range' ? ' selected="selected"' : ''); ?>>Date Range</option>
			</select>
  		</div>
  		<span class="range-search-field">
	  		<div class="form-group">
	    		<div class="input-group">
	      			<div class="input-group-addon">From</div>
	      			<input class="form-control" name="date_from" value="<?php echo ( isset($_GET['date_from']) ? urldecode($_GET['date_from']) : ''); ?>" id="date-from" type="text" placeholder="Start Date">
	    		</div>
	  		</div>
	  		<div class="form-group">
	    		<div class="input-group">
	      			<div class="input-group-addon">To</div>
	      			<input class="form-control" name="date_to" value="<?php echo ( isset($_GET['date_to']) ? urldecode($_GET['date_to']) : ''); ?>" id="date-to" type="text" placeholder="End Date">
	    		</div>
	  		</div>
	  	</span>
	  	<button type="submit" class="btn btn-default">Go</button>
</form>
</div>

<div class="ib2-admin-box">
	<div class="ib2-admin-box-title">Variation <?php echo strtoupper($variant); ?> Summary (<?php echo $sub_title; ?>)</div>
	<div class="ib2-admin-box-inside">
		<table class="table table-hover table-bordered">
			<thead>
		    	<tr>
			    	<th>Variant</th>
			    	<th style="width:100px; text-align:center">Weight</th>
			    	<!--<th>Page Views</th>-->
			    	<th style="width:100px; text-align:center">Visitors</th>
			    	<th style="width:100px; text-align:center">Conversions</th>
			    	<th style="width:100px; text-align:center">Conv. Rate</th>
			    	<th style="width:120px; text-align:center">Improvement <i class="fa fa-question stats-tooltip" data-toggle="tooltip" data-placement="top" title="Conversion rate difference against the original (A) variation"></i></th>
			    	<th style="width:120px; text-align:center">Chance To<br />Beat Orig.</th>
		    	</tr>
	    	</thead>
	    	
	    	<tbody>
	    	<?php
	    	if ( $reports ) {
	    		foreach ( $reports as $report ) {
	    			if ( $report->variant != $variant ) continue;
					
					$td_style = ( $vw['v'] == $report->variant && $vw['c'] >= 90 ) ? 'font-weight:bold; color:#009900"' : '';
					echo '<tr>';
	    			echo '<td>Variation <strong>' . strtoupper($report->variant) . '</strong>';
	    			if ( $report->variant == 'a' ) {
	    				echo ' <span class="text-muted">(original)</span>';
					}
					
					$diff_color = 'inherit';
					$_diff = str_replace('%', '', $report->improvement);
					if ( $report->variant != 'a' && $_diff > 0 )
						$diff_color = '#009900';
					if ( $report->variant != 'a' && $_diff < 0 )
						$diff_color = '#cc0000';
					
					$detail_url = admin_url('admin.php?variant=' . $report->variant);
					if ( isset($_GET) ) {
						foreach ( $_GET as $k => $v ) {
							$detail_url .= "&{$k}={$v}";
						}
					}
	    			echo ' <a href="' . admin_url('post.php?ib2preview=' . $post_id . '&variant=' . $report->variant) . '" target="_blank"><i class="fa fa-external-link"></i></a></td>';
					echo '<td style="width:100px; text-align:center">' . $report->weight . '%</td>';
					//echo '<td style="width:100px; text-align:center">' . $stat->page_views . '</td>';
					echo '<td style="width:100px; text-align:center">' . $report->visitors . '</td>';
					echo '<td style="width:100px; text-align:center">' . $report->conversions . '</td>';
					echo '<td style="width:100px; text-align:center">' . $report->conversion_rate . '%</td>';
					echo '<td style="width:120px; text-align:center; color: ' . $diff_color . '">' . $report->improvement . '</td>';
					echo '<td style="width:120px; text-align:center;' . $td_style . '">' . $report->confidence . '</td>';
					echo '</tr>';
	    		}
	    	}
	    	?>
	    	</tbody>
	  	</table>
	</div>
</div>

<?php
$referers = ib2_get_trafficstats($post_id, $range, 'referer', $variant, 30);
?>
<div class="ib2-admin-box">
	<div class="ib2-admin-box-title">Traffic Sources</div>
	<div class="ib2-admin-box-inside">
		<table class="table table-hover table-bordered">
			<thead>
		    	<tr>
			    	<th>Referrer</th>
			    	<th style="width:100px; text-align:center">Visitors</th>
			    	<th style="width:100px; text-align:center">Percentage</th>
		    	</tr>
	    	</thead>
	    	
	    	<tbody>
	    	<?php
	    	if ( $referers && isset($referers['results']) ) {
	    		foreach ( $referers['results'] as $ref ) {
	    			$referer = ( empty($ref->referer) ) ? 'None (direct)' : '<a href="' . esc_url($ref->referer) . '" target="_blank">' . esc_url($ref->referer) . '</a>';
					$percentage = @round(($ref->visitors / $referers['total_visitors']) * 100, 2);
					$bar_width = @round(($percentage * 185) / 100, 2);
					echo '<tr>';
	    			echo '<td>' . $referer . '</td>';
					echo '<td style="width:100px; text-align:center">' . $ref->visitors . '</td>';
					echo '<td style="width:250px; vertical-align:middle"><div style="width:' . $bar_width . 'px;height:20px;background-color:#5cb85c; display:inline-block"></div> ' . $percentage . '%</td>';
				
					echo '</tr>';
	    		}
	    	}
	    	?>
	    	</tbody>
	  	</table>
	</div>
</div>

<?php
$locations = ib2_get_trafficstats($post_id, $range, 'location', $variant, 30);
?>

<div class="ib2-admin-box">
	<div class="ib2-admin-box-title">Traffic By Location</div>
	<div class="ib2-admin-box-inside">
		<table class="table table-hover table-bordered">
			<thead>
		    	<tr>
			    	<th>Location</th>
			    	<th style="width:100px; text-align:center">Visitors</th>
			    	<th style="width:100px; text-align:center">Percentage</th>
		    	</tr>
	    	</thead>
	    	
	    	<tbody>
	    	<?php
	    	if ( $locations && isset($locations['results']) ) {
	    		foreach ( $locations['results'] as $ref ) {
	    			$location = ( empty($ref->location) || trim($ref->location) == '()' ) ? 'Unknown' : stripslashes($ref->location);
					$percentage = @round(($ref->visitors / $locations['total_visitors']) * 100, 2);
					$bar_width = @round(($percentage * 185) / 100, 2);
					echo '<tr>';
	    			echo '<td>' . $location . '</td>';
					echo '<td style="width:100px; text-align:center">' . $ref->visitors . '</td>';
					echo '<td style="width:250px; vertical-align:middle"><div style="width:' . $bar_width . 'px;height:20px;background-color:#5cb85c; display:inline-block"></div> ' . $percentage . '%</td>';
				
					echo '</tr>';
	    		}
	    	}
	    	?>
	    	</tbody>
	  	</table>
	</div>
</div>

<?php

//$browsers = ib2_get_trafficstats($post_id, $range, 'browser', $variant);


?>
