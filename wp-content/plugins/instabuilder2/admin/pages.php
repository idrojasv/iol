<?php
$mode = ( isset($_GET['mode']) ) ? $_GET['mode'] : '';
$group_id = ( isset($_GET['group_id']) ) ? (int) $_GET['group_id'] : 0;

/* Pages */
$all_pages = ib2_pages_query('group_id=' . $group_id);
$offset = ( isset($_GET['paged']) ) ? $_GET['paged'] - 1 : 0;
if ( $offset < 0 ) $offset = 0;
$perpage = 20;
$offset = $offset * $perpage;
$pages = ib2_pages_query('group_id=' . $group_id . '&start=' . $offset . '&limit=' . $perpage);
$pagination = array(
		'base' => esc_url(add_query_arg('paged', '%#%')),
		'format' => '',
		'total' => ceil( count($all_pages) / $perpage ),
		'current' => $offset / $perpage + 1,
		'type' => 'array'
	);
	
$page_links = paginate_links($pagination);

$all_groups = ib2_groups_query();
if ( $mode == '' ) :
	/* Groups */
	$start = ( isset($_GET['start']) ) ? $_GET['start'] - 1 : 0;
	if ( $start < 0 ) $start = 0;
	$perpage = 20;
	$start = $start * $perpage;
	$groups = ib2_groups_query('start=' . $start . '&limit=' . $perpage);
	$pagination = array(
			'base' => esc_url(add_query_arg('start', '%#%')),
			'format' => '',
			'total' => ceil( count($all_groups) / $perpage ),
			'current' => $start / $perpage + 1,
			'type' => 'array'
		);
		
	$group_links = paginate_links($pagination);

endif;

if ( isset($_GET['group_id']) ) {
	$group = ib2_get_group($_GET['group_id']);
}

if ( isset($_GET['group_id']) && $group ) {
	echo '<div class="page-header">';
	echo '<h2>Group: ' . ib2_esc($group->name) . ' <small><a href="' . admin_url('admin.php?page=ib2-dashboard') . '">&laquo; Back to Dashboard</a></small></h2>'; 
	echo '</div>';
}

//$t_c = ib2_get_templates();
//echo count($t_c);

$options = get_option('ib2_options');

if ( !empty($options['fb_appid']) ) :
?>
<div id="fb-root"></div>
<script>
	window.fbAsyncInit = function() {
		FB.init({
			appId      : '<?php echo ib2_esc($options['fb_appid']); ?>',
			cookie     : true,
			xfbml      : true,
			version    : 'v2.2'
		});
		
		(function($) {
			$('.fb-publish-btn').each(function(){
				$(this).click(function(e){
					var post_id = $(this).data('postid');
					FB.getLoginStatus(function(response) {
						if ( response.status == 'connected' ) {
							publish_to_facebook(post_id);
						} else {
							facebook_login(post_id);
						}
		
						e.preventDefault();
					});
					e.preventDefault();
				});
			});
			
			var facebook_login = function( post_id ) {
				FB.login(function(response) {
					if ( response.authResponse ) {
						if ( response.status == 'connected' ) {
							publish_to_facebook( post_id );
						}
					}
				}, {scope: 'email,public_profile,manage_pages,publish_pages'});
			};
			
			var publish_to_facebook = function( post_id ) {
				FB.ui({
  					method: 'pagetab'
				}, function( response ){
					if ( response != null && response.tabs_added != null ) {
    					var fbIds = new Array();
    					var length = response.tabs_added.length;
    					length = (typeof length === 'undefined') ? true : false;
    					
    					if ( length ) {
    						$.each(response.tabs_added, function(id) {
	    						fbIds.push(id);
	        				});
	        		
	        				var data = {
								action: 'ib2_publish_facebook',
								fb_ids: fbIds,
								post_id: post_id
							};
							
							$.post(ajaxurl, data, function(response){
								if ( response.success ) {
									window.location.href = response.fburl;
								}
							});
    					} else {
    						console.log(response);
    					}
    				} else {
    					console.log(response);
    				}
				});
			}
		})(jQuery);
	};

	(function(d, s, id){
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) {return;}
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/en_US/sdk.js";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
</script>
<?php
endif;

if ( !isset($_GET['group_id']) ) :
?>
<div class="row">
	<div class="col-lg-3 col-sm-6">
		<div class="panel widget bg-primary">
			<div class="row-table">
				<div class="col-xs-4 bg-danger pv-lg" style="text-align:center">
					<i class="fa fa-file-o fa-3x"></i>
				</div>
				<div class="col-xs-8 pv-lg">
					<div class="h2 mt0"><?php echo ib2_total_pages('draft'); ?></div>
					<div class="text-uppercase text-center">Unpublished Pages</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="col-lg-3 col-sm-6">
		<div class="panel widget bg-primary">
			<div class="row-table">
				<div class="col-xs-4 bg-success pv-lg" style="text-align:center">
					<i class="fa fa-file-text fa-3x"></i>
				</div>
				<div class="col-xs-8 pv-lg">
					<div class="h2 mt0"><?php echo ib2_total_pages('publish'); ?></div>
					<div class="text-uppercase text-center">Published Pages</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="col-lg-3 col-sm-6">
		<div class="panel widget bg-primary">
			<div class="row-table">
				<div class="col-xs-4 bg-info pv-lg" style="text-align:center">
					<i class="fa fa-binoculars fa-3x"></i>
				</div>
				<div class="col-xs-8 pv-lg">
					<div class="h2 mt0"><?php echo ib2_total_visits(); ?></div>
					<div class="text-uppercase text-center">Visits</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="col-lg-3 col-sm-6">
		<div class="panel widget bg-primary">
			<div class="row-table">
				<div class="col-xs-4 bg-warning pv-lg" style="text-align:center">
					<i class="fa fa-users fa-3x"></i>
				</div>
				<div class="col-xs-8 pv-lg">
					<div class="h2 mt0"><?php echo ib2_total_unique_visitors(); ?></div>
					<div class="text-uppercase text-center">Unique Visitors</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php if ( isset($_GET['new_variant']) && $_GET['new_variant'] == 'true' ) : ?><div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button> Page variation has been added. <a href="<?php echo admin_url('post.php?post=' . $_GET['post_id'] . '&action=edit&ib2editor=true&variant=' . $_GET['variant']); ?>" class="alert-link" target="_blank">Click here</a> to edit the new page variation.</div><?php endif; ?>

<div class="ib2-admin-box">
	<div class="ib2-admin-box-title">Visitors Chart for the last 2 weeks</div>
	<div class="ib2-admin-box-inside">
		<div class="row">
			<div class="col-sm-9">
				<div id="ib2-chart" class="jqplot-target" style="margin:0 auto; position: relative;"></div>
			</div>
			<div class="col-sm-3" style="padding-top:25px">
				<div class="well">
					<a href="#" data-gnum="0" role="button" class="btn btn-success btn-lg ib2-new-page" style="width:100%"><i class="fa fa-file-text-o"></i> New Page</a>
				</div>
				<div class="well">
					<a href="#" data-gnum="0" role="button" class="btn btn-primary btn-lg create-group-btn" style="width:100%"><i class="fa fa-folder-o"></i> New Group</a>
				</div>
				<div class="well">
					<a href="#" role="button" class="btn btn-danger btn-lg page-from-import" style="width:100%"><i class="fa fa-upload"></i> Import</a>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
	$biweekly = ib2_biweekly_visitors();
	$datalines = array();
	if ( $biweekly ) {
		foreach ( $biweekly as $s ) {
			$datalines[] = "['{$s->v_date}', {$s->visitors}]";
		}
	} else {
		$datalines[] = "['" . date_i18n("Y-m-d") . "', 0]";
	}
?>
	
<script type="text/javascript">
jQuery(document).ready(function($){
	var datalines = [<?php echo implode(",", $datalines); ?>];
	
	var plot1 = $.jqplot("ib2-chart", [datalines], {
        title: 'Total Visitors For The Last 2 Weeks',
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
        seriesDefaults: {
            rendererOptions: {
                smooth: true
            }
        },
        series: [
           {
				color: '#89A54E',
				fill: true,
				fillColor: '#89A54E',
				fillAndStroke: true,
        		fillAlpha: 0.4
           }
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
                min: "<?php echo date_i18n("Y-m-d", strtotime("14 days ago")); ?>",
                max: "<?php echo date_i18n("Y-m-d"); ?>",
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

<?php endif; if ( $mode == '' ) : ?>
<!-- Groups -->
<div class="ib2-admin-box">
	<div class="ib2-admin-box-title">Page Groups</div>
	<div id="page-groups" class="ib2-admin-box-inside">
		<p style="text-align:right"><a href="#" class="btn btn-success btn-sm create-group-btn"><i class="fa fa-folder-o"></i> New Group</a></p>
		<div id="new-group" style="display:<?php echo ( ( isset($_GET['group_error']) || isset($_GET['group_created']) ) ? 'block' : 'none' ); ?>">
			<?php if ( isset($_GET['group_error']) ) : ?>
				<?php if ( $_GET['group_error'] == 'empty' ) : ?><div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>ERROR: Please enter a group name.</div><?php endif; ?>
				<?php if ( $_GET['group_error'] == 'exists' ) : ?><div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>ERROR: Group name already exists.</div><?php endif; ?>
			<?php endif; ?>
			
			<?php if ( isset($_GET['group_created']) && $_GET['group_created'] == 'true' ) : ?><div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><i class="fa fa-check"></i> Group has been created.</div><?php endif; ?>
				
			<form name="group_form" id="group-form" class="form-inline" role="form" method="post">
				<input type="hidden" name="ib2action" value="ib2_create_group" />
				<div class="form-group">
					<input type="text" class="form-control" name="new_group_name" id="new-group-name" placeholder="Enter Group Name">
				</div>
				<button type="submit" class="btn btn-primary">Create Group</button>
			</form>
			<hr>
		</div>

		<form name="ib2_groups_form" id="ib2-groups-form" method="post">
		<input type="hidden" name="ib2action" value="delete_groups" />
		<table class="table table-hover">
			<thead>
		    	<tr>
			    	<th><input type="checkbox" class="groups-parent-check"></th>
			    	<th>Group Name</th>
			    	<th># Pages</th>
			    	<th>Action</th>	
		    	</tr>
	    	</thead>
	    	<tfoot>
		    	<tr>
			    	<th><input type="checkbox" class="groups-parent-check"></th>
			    	<th>Group Name</th>
			    	<th># Pages</th>
			    	<th>Action</th>	
		    	</tr>
	    	</tfoot>
	    	<tbody>
	    		<?php
	    		if ( $groups ) {
	    			$i = 0;
	    			foreach ( $groups as $group ) {
	    				$class = ($i%2) ? '' : ' class="active"';
	    				echo '<tr' . $class . '>';
						echo '<td><input type="checkbox" name="group[]" class="group-child-check" value="' . $group->ID . '"></td>';
						echo '<td>' . ib2_esc($group->name) . '</td>';
						echo '<td><span class="text-muted">' . $group->totalpages . ' page(s)</span></td>';
						echo '<td>';
						echo '<div class="btn-group">';
							echo '<a href="' . admin_url('admin.php?page=ib2-dashboard&mode=group&group_id=' . $group->ID) . '" role="button" class="btn btn-default" title="View Groups"><i class="fa fa-folder-o"></i></a>';
							echo '<a href="#" role="button" class="btn btn-default ib2-new-page" title="Add new page" data-gnum="' . $group->ID . '"><i class="fa fa-file"></i></a>';
						  	echo '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" title="More Action"><i class="fa fa-cog"></i> <span class="caret"></span></button>';
								echo '<ul class="dropdown-menu" role="menu">';
    								echo '<li><a href="' . admin_url('admin.php?page=ib2-dashboard&ib2action=clone_group&group_id=' . $group->ID) . '">Duplicate</a></li>';
    								echo '<li class="divider"></li>';
									echo '<li><a href="' . admin_url('admin.php?page=ib2-dashboard&ib2action=delete_group&group_id=' . $group->ID) . '" onclick="return confirm(\'Are you sure you want to delete this group?\nAll pages within this group will be deleted too.\')">Delete Group</a></li>';
								echo '</ul>';
						echo '</div>';
						echo '</td>';
						echo '<tr>' . "\n";
						$i++;
	    			}
	    		}
	    		?>
	    	</tbody>
	  	</table>
		<div class="row">
			<div class="col-sm-6" style="padding-top:15px; padding-bottom:15px">
				<button type="submit" class="btn btn-danger">Delete Selected</button>
			</div>
			<div class="col-sm-6" style="text-align:right">
				<?php
				if ( $group_links ) {
					echo '<ul class="pagination">';
					$i = 0;
					foreach ( $group_links as $l ) {
						$current = ( isset($_GET['start']) ) ? $_GET['start'] : 0;
						$active = ( $current == $i ) ? ' class="active"' : '';
						echo '<li' . $active . '>' . $l . '</li>';
						$i++;
					}
					echo '</ul>';
				}
				?>
			</div>
			<div class="clearfix"></div>
		</div>
		</form>
	</div>
</div>
<!-- ./Groups -->
<?php endif; ?>

<!-- No Group Pages -->
<?php $page_box_title = ( isset($_GET['group_id']) ) ? 'Pages' : 'Ungrouped Pages'; ?>
<div class="ib2-admin-box">
	<div class="ib2-admin-box-title"><?php echo $page_box_title; ?></div>
	<div class="ib2-admin-box-inside">
		<p style="text-align:right"><a href="#" class="btn btn-success btn-sm ib2-new-page" data-gnum="<?php echo $group_id; ?>"><i class="fa fa-file-o"></i> Create Landing Page</a></p>
		<form name="ib2_pages_form" id="ib2-pages-form" method="post">
		<input type="hidden" name="ib2action" value="delete_pages" />
		<input type="hidden" name="group_id" value="<?php echo $group_id; ?>" />
		<table class="table table-hover">
			<thead>
		    	<tr>
			    	<th><input type="checkbox" class="pages-parent-check"></th>
			    	<th>Page</th>
			    	<th>Status</th>
			    	<th>Action</th>	
		    	</tr>
	    	</thead>
	    	<tfoot>
		    	<tr>
			    	<th><input type="checkbox" class="pages-parent-check"></th>
			    	<th>Page</th>
			    	<th>Status</th>
			    	<th>Action</th>	
		    	</tr>
	    	</tfoot>
	    	<tbody>
	    		<?php
	    		if ( $pages ) {
	    			$i = 0;
	    			foreach ( $pages as $page ) {
	    				$class = ($i%2) ? '' : ' class="active"';
						$real_url = get_permalink($page->post_id);
						if ( $page->status != 'publish' ) {
							$real_url = esc_url(add_query_arg('preview', 'true', $real_url));
						}
						
						$new_title = null;
						$new_slug = null;
						list($permalink, $post_name) = get_sample_permalink($page->post_id, $new_title, $new_slug);
						if ( strlen($post_name) > 30 ) {
							$post_name_abridged = substr($post_name, 0, 14). '&hellip;' . substr($post_name, -14);
						} else {
							$post_name_abridged = $post_name;
						}
						$display_url = str_replace(array('%pagename%','%postname%'), $post_name_abridged, $permalink);
							
	    				echo '<tr' . $class . '>';
						echo '<td><input type="checkbox" name="page[]" class="page-child-check" value="' . $page->post_id . '"></td>';
						echo '<td>' . ib2_esc($page->name) . '<br />';
						echo '<a id="permalink-url-' . $page->post_id . '" href="' . $real_url . '" target="_blank">' . $display_url . '</a></td>';
						echo '<td><span class="text-muted">' . $page->status . '</span></td>';
						echo '<td>';
						echo '<div class="btn-group">';
							echo '<a href="' . admin_url('post.php?post=' . $page->post_id . '&action=edit&ib2editor=true') . '" role="button" class="btn btn-default" title="Edit"><i class="fa fa-edit"></i></a>';
						  	echo '<a href="#" role="button" class="btn btn-default change-page-slug" title="Change Permalink" data-postid="' . $page->post_id . '" data-permalink="' . str_replace(array('%pagename%/','%postname%/','%pagename%','%postname%'), '', $permalink) . '" data-slug="' . $post_name . '" data-status="' . $page->status . '"><i class="fa fa-link"></i></a>';
						  	echo '<a href="' . admin_url('admin.php?page=ib2-dashboard&post_id=' . $page->post_id . '&mode=stats') . '" role="button" class="btn btn-default" title="Stats"><i class="fa fa-bar-chart"></i></a>';
							
						  	echo '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" title="More Action"><i class="fa fa-cog"></i> <span class="caret"></span></button>';
								echo '<ul class="dropdown-menu" role="menu">';
    								echo '<li><a href="' . admin_url('admin.php?page=ib2-dashboard&ib2action=clone_page&post_id=' . $page->post_id . '&group_id=' . $group_id) . '">Duplicate</a></li>';
    								echo '<li><a href="#" class="page-new-variation" data-postid="' . $page->post_id . '" data-groupid="' . $group_id . '">Add Split Test Variation</a></li>';
									echo '<li><a href="' . esc_url(add_query_arg('ib2mode', 'save_html', $real_url)) . '">Save HTML (single file only)</a></li>';
									echo '<li><a href="' . esc_url(add_query_arg('ib2mode', 'save_html_rich', $real_url)) . '">Save HTML + Graphics</a></li>';
									
									if ( !empty($options['fb_appid']) && $page->status == 'publish' )
										echo '<li><a href="#" class="fb-publish-btn" data-postid="' . $page->post_id . '">Publish To Facebook</a><li>';
										
									if ( isset($_GET['group_id']) ) {
										echo '<li><a href="' . admin_url('admin.php?page=ib2-dashboard&ib2action=remove_from_group&post_id=' . $page->post_id . '&group_id=' . $group_id) . '" onclick="return confirm(\'Are you sure you want to remove this page from the group?\');">Remove From Group</a></li>';
									} else {
    									echo '<li><a href="#" class="page-to-group-add" data-postid="' . $page->post_id . '">Add To Group</a></li>';
    								}
									if ( $page->status == 'publish' ) {
										echo '<li><a href="' . admin_url('admin.php?page=ib2-dashboard&ib2action=unpublish_page&post_id=' . $page->post_id . '&group_id=' . $group_id) . '">Unpublish</a></li>';
									} else {
										echo '<li><a href="' . admin_url('admin.php?page=ib2-dashboard&ib2action=publish_page&post_id=' . $page->post_id . '&group_id=' . $group_id) . '">Publish</a></li>';
									}
									echo '<li><a href="' . admin_url('admin.php?page=ib2-dashboard&ib2action=export_file&post_id=' . $page->post_id) . '">Export</a></li>';
    								echo '<li class="divider"></li>';
									echo '<li><a href="' . admin_url('admin.php?ib2action=reset_stats&post_id=' . $page->post_id) . '" onclick="return confirm(\'Are you sure you want to reset this page stats?\nThis action cannot be undone.\')">Reset Stats</a></li>';
									echo '<li><a href="' . admin_url('admin.php?page=ib2-dashboard&ib2action=delete_page&post_id=' . $page->post_id . '&group_id=' . $group_id) . '" onclick="return confirm(\'Are you sure you want to delete this page?\nThis action cannot be undone.\')">Delete Page</a></li>';
								echo '</ul>';
						echo '</div>';
						echo '</td>';
						echo '<tr>' . "\n";
						$i++;
	    			}
	    		} else {
	    			echo '<tr><td colspan="4"><em>No pages found.</em></p>';
	    		}
	    		?>
	    	</tbody>
	  	</table>
		<div class="row">
			<div class="col-sm-6" style="padding-top:15px; padding-bottom:15px">
				<button type="submit" class="btn btn-danger">Delete Selected</button>
			</div>
			<div class="col-sm-6" style="text-align:right">
				<?php
				if ( $page_links ) {
					echo '<ul class="pagination">';
					$i = 0;
					foreach ( $page_links as $l ) {
						$current = ( isset($_GET['paged']) ) ? $_GET['paged'] : 0;
						$active = ( $current == $i ) ? ' class="active"' : '';
						echo '<li' . $active . '>' . $l . '</li>';
						$i++;
					}
					echo '</ul>';
				}
				?>
			</div>
			<div class="clearfix"></div>
		</div>
		</form>
	</div>
</div>
<!-- ./Pages -->

<input type="hidden" id="ib2-group-id" value="0" />

<!-- Change Permalink Modal -->
<div id="ib2-change-permalink" style="display:none;">
	<div class="ib2-template-header">
		<button type="button" class="close ib2-permalink-close"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		Change Permalink
	</div>
	<div class="ib2-template-content">
		<div id="permalink-alert" class="alert alert-success alert-dismissible" role="alert" style="display:none">
			<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			Permalink has been changed!
		</div>
    	<div class="form-group permalink-form">
			<div class="input-group">
				<span id="slug_url" class="input-group-addon"></span>
				<input type="text" class="form-control" id="ib2-new-slug" value="">
				<span class="input-group-btn">
					<button class="btn btn-default" id="save-new-permalink" type="button">OK</button>
				</span>
			</div><!-- /input-group -->
		</div>
		<input type="hidden" id="ib2-slug-postid" value="0">
		<input type="hidden" id="ib2-current-slug" value="">
		<input type="hidden" id="ib2-page-status" value="">
    </div>
    <div class="ib2-template-footer">
 		<a href="#" class="ib2-permalink-close btn btn-default" role="button">Close</a>
	</div>
</div>

<!-- New Variation Modal -->
<div id="ib2-new-variant" style="display:none;">
	<div class="ib2-template-header">
		<button type="button" class="close ib2-variant-close"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		Create New Variation
	</div>
	<div class="ib2-template-content">
    	<div class="form-group">
	    	<label for="variant-type">Creation Mode</label>
	    	<select class="form-control" id="variant-type">
				<option value="duplicate">Duplicate Current Variation</option>
				<option value="template">Use New Template</option>
				<option value="scratch">Create From Scratch</option>
			</select>
	  	</div>
				
		<div id="ib2-templts" class="ib2-tmpls" style="display:none;">
			<hr />
			<ul class="nav nav-pills new-variant-type">
				<li class="active"><a href="#" class="ib2-tmplt-type" data-type="sales">Sales Page</a></li>
				<li><a href="#" class="ib2-tmplt-type" data-type="optin">Squeeze Page</a></li>
				<li><a href="#" class="ib2-tmplt-type" data-type="launch">Launch Page</a></li>
				<li><a href="#" class="ib2-tmplt-type" data-type="webinar">Webinar</a></li>
				<li><a href="#" class="ib2-tmplt-type" data-type="coming">Coming Soon</a></li>
				<li><a href="#" class="ib2-tmplt-type" data-type="others">Others</a></li>
			</ul>
			
			<hr>
			<form id="template-search-form" method="post" class="form-inline">
				<div class="form-group">
				<select id="ib2-tmplt-subtype" class="form-control">
					<option value="">-- Sub-Type --</option>
					<option value="textsqueeze">Text Squeeze</option>
					<option value="videosqueeze">Video Squeeze</option>
					<option value="minisqueeze">Mini Squeeze</option>
					<option value="2stepsoptin">2 Steps Opt-In</option>
					<option value="3stepsoptin">3 Steps Opt-In</option>
					<option value="surveyoptin">Survey Opt-In</option>
					<option value="textsales">Text Sales Page</option>
					<option value="videosales">Video Sales Page</option>
					<option value="hybridsales">Hybrid Sales Page</option>
					<option value="otosales">OTO Sales Page</option>
					<option value="webinarsignup">Webinar Sign-Up</option>
					<option value="webinarthanks">Webinar Thank You</option>
					<option value="download">Download Page</option>
					<option value="confirmation">Confimation Page</option>
					<option value="thankyou">Thank You Page</option>
				</select>
				</div>
				<div class="form-group">
					<input type="text" id="ib2-tmplt-tags" class="form-control" placeholder="e.g. enter keywords here" />
				</div>
				<button type="submit" class="btn btn-default" id="search-templates">GO</button>
			</form>
			
			<div class="ib2-templts-area">
				<h3>Sales Page Templates</h3>
				<p style="display:none; margin-top:40px; text-align:center" class="ib2-templts-loader">
					<img src="<?php echo IB2_IMG; ?>preload-bar.gif" border="0" /><br />
					<em>Loading...</em>
				</p>
				<div class="ib2-templts-content">
					<?php ib2_get_templates_html('type=sales'); ?>
				</div>
				<div style="clear:left"></div>
			</div>
		</div>

	  	<p style="text-align:right" id="non-templates-area"><button id="variant-create" type="button" class="btn btn-primary btn-lg" data-status="variant" data-mode="duplicate">Create Now</button></p>
    </div>
    <div class="ib2-template-footer">
 		<a href="#" class="ib2-variant-close btn btn-default" role="button">Close</a>
	</div>
</div>

<!-- Import Modal -->
<div id="ib2-import-file" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ib2ImportFile" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Import Page From File</h4>
			</div>
			<div class="modal-body">
		    	<form method="post" action="<?php admin_url('admin.php?page=ib2-dashboard'); ?>" enctype="multipart/form-data">
					<input type="hidden" name="ib2action" value="import_file" />
					<input type="hidden" name="import_post_id" value="0" />
					<div class="form-group">
					    <label for="import_from_file">Upload IB 2.0 File To Import</label>
					    <input type="file" name="import" id="import_from_file">
					    <p class="help-block">Click "Browse" to select IB 2.0 landing page file.</p>
					</div>
					<button type="submit" id="import_now" class="btn btn-lg btn-success">Import Now</button>
				</form>
		    </div>
		    <div class="modal-footer">
			    <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
		    </div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Add Group Modal -->
<div id="ib2-group-add-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ib2GroupAddModal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Add To Group</h4>
			</div>
		    <div class="modal-body">
		    	<form name="group-add-form" id="group-add-form" method="post">
		    		<input type="hidden" name="ib2action" value="addgroup">
		    		<input type="hidden" id="the_post_id" name="the_post_id" value="0">
		    		<div class="form-group">
		    			<label for="the-group-list">Select a Group</label>
		    			<select id="the_group_id" name="the_group_id" class="form-control">
		    				<?php if ( $all_groups ) {
		    					foreach ( $all_groups as $gr ) {
		    						echo '<option value="' . $gr->ID . '">' . ib2_esc($gr->name) . '</option>' . "\n";
		    					}
		    				} ?>
		    			</select>
		    		</div>
		    		<p style="text-align:right">
		    			<button type="submit" id="add-to-group" class="btn btn-success">Add To Group</button>
		    		</p>
		    	</form>
		    </div>
		    <div class="modal-footer">
			    <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
		    </div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
