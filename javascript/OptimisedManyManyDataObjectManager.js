jQuery(function($) {
	var $addDiv = $('body > .add-dataobject'),
		$existingDiv = $('body > .existing-dataobject'),
		$form = $existingDiv.children('#DataObjectManager_Popup_SearchForm'),
		$inputs = $form.find('input'),
		$tableRows = $existingDiv.find('table tbody tr'),
		$firstTableRow = $tableRows.eq(0),
		$messages = $('.message', $addDiv),
		searchingText = 'Searching&hellip;',
		tableRowsLength = $tableRows.length,
		xhrIndex = 0,
		xhrLastUpdate = 0;
	$inputs.keyup(function(e) {
		if( e.which == 13 ) e.preventDefault();
		var keycodes = [
			9, // Tab
			13, // Return / Enter
			16, // Shift
			17, // Control
			18, // Alt / Option
			37, // Left arrow
			38, // Top arrow
			39, // Right arrow
			40, // Bottom arrow
			224 // Command
		];
		if( $.inArray(e.which, keycodes) == -1 ) {
			xhrIndex++;
			var curIndex = xhrIndex,
				clearAllRows = function() {
					for( i = 0; i < tableRowsLength; i++ ) {
						$row = $tableRows.eq(i);
						$row.data('row-id', 0);
						$row.children().each(function() { $(this).html('&nbsp;'); });
					}
				};
			var values = { limit:tableRowsLength, xhrIndex:xhrIndex },
				sendQuery = false;
			$inputs.each(function() {
				var val = $(this).val();
				if( val ) {
					sendQuery = true;
					values[$(this).attr('id')] = val;
				}
			});
			if( sendQuery ) {
				setTimeout(
					function() {
						if( curIndex == xhrIndex ) {
							if( !$firstTableRow.data('row-id') ) $firstTableRow.children().eq(0).html(searchingText);
							$.ajax({
								data: values,
								dataType: 'json',
								success: function(data) {
									var i;
									if( data && data.xhrIndex > xhrLastUpdate ) {
										xhrLastUpdate = data.xhrIndex;
										var $column, $columns, img, index, result, resultIndex, $row, height;
										if( !data.results.length ) {
											clearAllRows();
											$firstTableRow.children().eq(0).html(
												( xhrLastUpdate == xhrIndex ) ? 'No records found' : searchingText
											);
										}
										else {
											for( i = 0; i < data.results.length; i++ ) {
												result = data.results[i];
												$row = $tableRows.eq(i);
												height = Math.round($row.height() * .7, 0);
												if( height > 30 ) height = 30;
												if( $row.data('row-id') != result.ID ) {
													$row.data('row-id', result.ID);
													$columns = $row.children();
													resultIndex = 0;
													for( index in result ) {
														if( $.inArray(index, ['ID', 'RelationshipID']) == -1 ) {
															$columns.eq(resultIndex).text(result[index]);
															resultIndex++;
														}
													}
													img = '<img height="' + height + '" width="' + height + '" ';
													if( result['RelationshipID'] )
														img += 'src="/ss-tools/images/dataobjectmanager/added.png" class="added"';
													else
														img += 'src="/ss-tools/images/dataobjectmanager/add.png" class="add"';
													$columns.eq(resultIndex).html(img + ' />');
												}
											}
										}
										for( i = i; i < tableRowsLength; i++ ) {
											$row = $tableRows.eq(i);
											$row.data('row-id', 0);
											$row.children().each(function() { $(this).html('&nbsp;'); });
										}
									}
								},
								url: window.location.pathname.replace(/add$/, 'search')
							});
						}
					},
					400
				);
			}
			else clearAllRows();
		}
	});
	$tableRows.find('img.add').live('click', function(e) {
		var $image = $(e.target);
		$image.attr('class', 'loading');
		$image.attr('src', $image.attr('src').replace(/add.png/, 'loading.gif'));
		var values = { id:$image.closest('tr').data('row-id') };
		$.ajax({
			data: values,
			dataType: 'json',
			success: function(data) {
				$image.attr('class', 'added');
				$image.attr('src', $image.attr('src').replace(/loading.gif/, 'added.png'));
				var location = window.location,
					formId, list;
				if( location.search ) {
					formId = location.search.match(/DataObjectManagerId=([^&]*)/);
					if( formId ) {
						list = top.jQuery("#" + formId[1] + "_CheckedList");
						list.val(list.val() + values.id + ",");
					}
				}
			},
			url: window.location.pathname.replace(/add$/, 'attach')
		});
	});
	if( $messages.length ) {
		$existingDiv.slideUp(function() {
			$addDiv.find('#field-holder').css({'height' : '100%'});
			$addDiv.slideDown();
		});
	}
	$('#show-add-dataobject').click(function() {
		$existingDiv.slideUp(function() {
			$addDiv.find('#field-holder').css({'height' : '100%'});
			$addDiv.slideDown();
		});
	});
	$('#show-existing').click(function() {
		$addDiv.slideUp(function() {
			$existingDiv.slideDown();
		});
		return false;
	});
});