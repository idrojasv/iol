<?php
if ( !isset($_GET['post_id']) )
	wp_die("ERROR: Cannot found Post ID.");

$post_id = (int) $_GET['post_id'];
$post = get_post($post_id);
if ( !$post )
	wp_die("ERROR: Post/Page Not Found.");

$search_mode = ( isset($_GET['search_mode']) ) ? urldecode($_GET['search_mode']) : 'today';
$q = $search_mode;
if ( $search_mode == 'range' && isset($_GET['date_from']) && isset($_GET['date_to']) ) {
	$search_mode = array(urldecode($_GET['date_from']), urldecode($_GET['date_to']));
}

$stats = ib2_get_pagestats($post_id, $search_mode);
$sub_title = ( !is_array($search_mode) ) ? ucwords($search_mode) : '';
if ( $search_mode == '7 days' ) {
	$sub_title = 'Last 7 Days';
} else if ( $search_mode == '14 days' ) {
	$sub_title = 'Last 2 Weeks';
} else if ( $search_mode == '30 days' ) {
	$sub_title = 'Last 30 Days';
} else if ( $q == 'range' && is_array($search_mode) ) {
	$sub_title = date_i18n("F d, Y", strtotime(urldecode($_GET['date_from']))) . ' - ' . date_i18n("F d, Y", strtotime(urldecode($_GET['date_to'])));
}

// Calculate Data...
// manipulate original data
/*
$stats[0]->visitors = 3273;
$stats[0]->conversions = 951;
$stats[1]->visitors = 3380;
$stats[1]->conversions = 1099;
$stats[2]->visitors = 3347;
$stats[2]->conversions = 975;
*/

$reports = false;
$vw = array('v' => 'a', 'c' => 0);
if ( $stats ) {
	$a_cr = 0;
	$i = 0;
	$reports = array();
	$control = array();
	foreach ( $stats as $stat ) {
		$visitors = $stat->visitors;
		$uvisitors = $stat->uvisitors;
		$conversions = $stat->conversions;
		$cr = @round(($conversions / $visitors ) * 100, 2);
		$diff = ( $stat->variant == 'a' ) ? "-" : ($cr - $a_cr) . '%';
		if ( $stat->variant == 'a' ) {
			$a_cr = $cr;
			$control = array($visitors, $conversions);
		}
		
		$p_value = ( $stat->variant == 'a' ) ? 0 : ib2_cumnormdist(ib2_zscore($control, array($visitors, $conversions)));
		$confidence = ( $stat->variant == 'a' ) ? 0 : @round($p_value*100, 2);
	
		$reports[$i] = new stdClass;
		$reports[$i]->weight = $stat->weight;
		$reports[$i]->variant = $stat->variant;
		$reports[$i]->visitors = $visitors;
		$reports[$i]->uvisitors = $uvisitors;
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

global $wpdb;

$q_dates = '';
$min_date = date_i18n("Y-m-d", strtotime("6 months ago"));
$max_date = date_i18n("Y-m-d");
$chart_title = 'Visitors';
if ( $search_mode == '7 days' ) {
	$chart_title = 'Visitors for the last 7 days';
	$min_date = date_i18n("Y-m-d", strtotime("8 days ago"));
	$q_dates = " AND `date` BETWEEN (CURRENT_DATE - INTERVAL 7 DAY) AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
} else if ( $search_mode == '14 days' ) {
	$chart_title = 'Visitors for the last 2 weeks';
	$min_date = date_i18n("Y-m-d", strtotime("15 days ago"));
	$q_dates = " AND `date` BETWEEN (CURRENT_DATE - INTERVAL 14 DAY) AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
} else if ( $search_mode == '30 days' ) {
	$chart_title = 'Visitors for the last 30 days';
	$min_date = date_i18n("Y-m-d", strtotime("31 days ago"));
	$q_dates = " AND `date` BETWEEN (CURRENT_DATE - INTERVAL 30 DAY) AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
} else if ( $search_mode == 'today' ) {
	$chart_title = 'Visitors for today';
	$min_date = date_i18n("Y-m-d", strtotime("yesterday"));
	$q_dates = " AND `date` BETWEEN CURRENT_DATE AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
} else if ( $search_mode == 'yesterday' ) {
	$chart_title = 'Visitors for yesterday';
	$min_date = date_i18n("Y-m-d", strtotime("2 days ago"));
	$max_date = date_i18n("Y-m-d", strtotime("yesterday"));
	$q_dates = " AND `date` BETWEEN date_add(date_sub(curdate(), interval 1 day), interval 1 second) AND CURRENT_DATE";
} else if ( $q == 'range' && is_array($search_mode) ) {
	$min_date = date_i18n("Y-m-d", strtotime(urldecode($_GET['date_from'])));
	$max_date = date_i18n("Y-m-d", strtotime(urldecode($_GET['date_to'])));
	$q_dates = " AND `date` BETWEEN '{$min_date}' AND '{$max_date}'";
	
	$chart_title = 'Visitors from ' . $min_date . ' - ' . $max_date;
}

$back = '<a href="' . admin_url('admin.php?page=ib2-dashboard') . '">&laquo; Back to Dashboard</a>';
if ( isset($_GET['funnel_id']) ) {
	$back = '<a href="' . admin_url('admin.php?page=ib2-funnel&mode=edit&group_id=' . $_GET['funnel_id']) . '">&laquo; Back</a>';
}
?>
<div class="page-header">
	<?php
	echo '<h2>Stats: ' . esc_attr($post->post_title) . ' <small>' . $back . '</small></h2>'; 
	?>
</div>

<?php if ( isset($_GET['reset']) && $_GET['reset'] == 'true' ) : ?>
<div class="updated"><p><strong>Stats has been reset.</strong></p></div>	
<?php endif; ?>

<div class="ib2-admin-box">
	<div class="ib2-admin-box-title">Chart</div>
	<div class="ib2-admin-box-inside">
		<div class="chart-wrapper">
			<div id="ib2-chart" class="jqplot-target" style="width:80%; margin:0 auto; position: relative;"></div>
		</div>
	</div>
</div>

<?php 
$vars = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}ib2_variants` WHERE `post_id` = {$post_id}");
$chart_data = array();
$chart_data_names = array();
if ( $vars ) {
	foreach ( $vars as $var ) {
		$v_name = $var->variant;

		// get daily hits
		$c_sql = "SELECT COUNT(visitorid) FROM `{$wpdb->prefix}ib2_conversions`
				WHERE `post_id` = v.post_id
				AND `variant` = v.variant
				AND `date` BETWEEN DATE(v.date) AND DATE_ADD(DATE(v.date), Interval 1 day)";
				
		$sql = "SELECT DATE(v.date) As v_date,
				COUNT(v.visitorid) AS visitors,
				({$c_sql}) AS conversions
				FROM `{$wpdb->prefix}ib2_hits` v
				WHERE v.post_id = $post_id AND v.variant = '{$v_name}'
				{$q_dates}
				GROUP BY DATE(v.date)";
				
		$results = $wpdb->get_results($sql);
		$data = array();
		if ( $results ) {
			foreach ( $results as $res ) {
				$data[] = '["' . $res->v_date . '", ' . $res->visitors . ']';
			}
		} else {
			$data[] = '["' . date_i18n("Y-m-d") . '", 0]';
		}
		
		$name = 'variant_' . $v_name;
		$chart_data_names[] = $name;
		$chart_data[$name] = $data;
	}
}

$chart_colors = array(
	'#89A54E',
	'#3071a9',
	'#ec971f',
	'#f6ca45',
	'#c9302c',
	'#6a509b',
	'#c1d29e',
	'#9ab7cf',
	'#cbb2c4',
	'#9e8b74',
	'#332a29',
	'#737373',
	'#050505'
);
?>
<script type="text/javascript">
jQuery(document).ready(function($){
	
	<?php
		if ( count($chart_data) > 0 ) {
			foreach ( $chart_data as $k => $v ) {
				echo "var {$k} = [" . implode(",", $v) . "];\n";
			}
		} else {
			echo "var variant_a = [\"" . date_i18n("Y-m-d") . "\", 0]";
			$chart_data_names = array('variant_a');
		}
	?>
	
	var plot1 = $.jqplot("ib2-chart", [<?php echo implode(",", $chart_data_names); ?>], {
        seriesColors: ["rgba(78, 135, 194, 0.7)", "rgb(211, 235, 59)"],
        title: '<?php echo $chart_title; ?>',
        highlighter: {
            show: true,
            sizeAdjust: 2.5,
            tooltipOffset: 9
        },
        grid: {
            drawGridlines:true,
			borderWidth: 0.25,
			shadow: false,
			gridLineColor: '#F5F5F5'
        },
        legend: {
            show: true,
            location:'ne',
			placement:'insideGrid'
        },
        seriesDefaults: {
            rendererOptions: {
                smooth: true
            }
        },
        series: [
            <?php 
            $series = array(); 
            if ( $chart_data_names ) {
            	$i = 0;
	            foreach ( $chart_data_names as $name ) {
	            	$newname = str_replace("_", " ", $name);
					$string = '{';
					$string .= ' color: "' . $chart_colors[$i] . '", label: "' . ucwords($newname) . '" }';
					
					$series[] = $string;
					$i++;
	            }						
			} 
			
			echo implode(",", $series);
			?> 	 
        ],
        axesDefaults: {
            rendererOptions: {
                baselineWidth: 1,
                baselineColor: '#444444',
                drawBaseline: false
            }
        },
        axes: {
            xaxis: {
                renderer: $.jqplot.DateAxisRenderer,
                tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                tickOptions: {
                    formatString: "%b %e",
                    angle: -30,
                    textColor: '#dddddd'
                },
                min: "<?php echo $min_date; ?>",
                max: "<?php echo $max_date; ?>",
                drawMajorGridlines: false
            },
            yaxis: {
                renderer: $.jqplot.LogAxisRenderer,
                pad: 0,
                rendererOptions: {
                    minorTicks: 1
                },
                tickOptions: {
                    formatString: "%'d",
                    showMark: false
                },
                labelRenderer: jQuery.jqplot.CanvasAxisLabelRenderer,
				label:'Visitors',
				labelOptions: {
					fontSize: '10pt',
					fontBold: true,
					textColor: '#a7a7a7',
				}
            }
        }
    });
});
</script>
<div class="search-form" style="text-align:right; margin-bottom:18px">
	<form class="form-inline" id="stats-form" role="form" method="get" action="<?php echo admin_url('admin.php'); ?>">
		<input type="hidden" name="page" value="ib2-dashboard">
		<input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
		<input type="hidden" name="mode" value="stats">
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
	<div class="ib2-admin-box-title">Summary (<?php echo $sub_title; ?>)</div>
	<div class="ib2-admin-box-inside">
		<table class="table table-hover table-bordered">
			<thead>
		    	<tr>
			    	<th>Variant</th>
			    	<th style="width:100px; text-align:center">Weight</th>
			    	<!--<th>Page Views</th>-->
			    	<th style="width:100px; text-align:center">Visits</th>
			    	<th style="width:100px; text-align:center">Conversions</th>
			    	<th style="width:100px; text-align:center">Conv. Rate</th>
			    	<th style="width:120px; text-align:center">Improvement <i class="fa fa-question stats-tooltip" data-toggle="tooltip" data-placement="top" title="Conversion rate difference against the original (A) variation"></i></th>
			    	<th style="width:120px; text-align:center">Chance To<br />Beat Orig.</th>
			    	<th style="text-align:center">Action</th>
		    	</tr>
	    	</thead>
	    	
	    	<tbody>
	    	<?php
	    	if ( $reports ) {
				$wt = 0;
				$vt = 0;
				$ct = 0;
	    		foreach ( $reports as $report ) {
					$wt += $report->weight;
					$vt += $report->visitors;
					$ct += $report->conversions;
					
					if ( empty($report->variant) )
						$report->variant = 'a';
					
					$tr_class = ( $vw['v'] == $report->variant && $vw['c'] >= 90 ) ? ' class="success"' : '';
					$td_style = ( $vw['v'] == $report->variant && $vw['c'] >= 90 ) ? 'font-weight:bold; color:#009900"' : '';
					echo '<tr' . $tr_class . '>';
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
					echo '<td style="text-align:center"><a href="' . $detail_url . '" class="btn btn-default btn-sm" role="button"><i class="fa fa-binoculars"></i> View Details</a></td>';
					echo '</tr>';
	    		}
				$crt = @(($ct/$vt)*100);
				?>
				</tbody>
		    	<tfoot>
			    	<tr class="info">
				    	<th>TOTAL</th>
				    	<th style="width:100px; text-align:center"><?php echo $wt; ?>%</th>
				    	<!--<th>Page Views</th>-->
				    	<th style="width:100px; text-align:center"><?php echo $vt; ?></th>
				    	<th style="width:100px; text-align:center"><?php echo $ct; ?></th>
				    	<th style="width:100px; text-align:center"><?php echo $crt; ?>%</th>
				    	<th style="width:120px; text-align:center">-</th>
				    	<th style="text-align:center">-</th>
				    	<th style="text-align:center"></th>
			    	</tr>
		    	</tfoot>
				<?php
	    	} else {
	    		echo '</tbody>';
	    	}
	    	?>
	    	
	  	</table>
	  	<?php
	  	$reset_url = admin_url('admin.php?ib2action=reset_stats&post_id=' . $post_id);
		if ( isset($_GET['funnel_id']) )
			$reset_url .= '&funnel_id=' . $_GET['funnel_id'];
	  	?>
	  	<p class="text-right"><a href="<?php echo $reset_url; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to reset this page stats?\nThis action cannot be undone.')">Reset Stats</a></p>
	</div>
</div>
<!-- ./Groups -->