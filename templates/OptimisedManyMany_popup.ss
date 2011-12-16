<% require css(ss-tools/css/OptimisedManyManyDataObjectManager.css) %>
<% require javascript(ss-tools/javascript/OptimisedManyManyDataObjectManager.js) %>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<% base_tag %>
    <meta content="text/html; charset=utf-8" http-equiv="Content-type"/>
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
	</head>
	<body class="CustomManyMany-popup DataObjectManager-popup loading <% if String %><% if NestedController %>nestedController<% end_if %><% else %><% if DetailForm.NestedController %>nestedController<% end_if %><% end_if %>	">
		<div class="existing-dataobject">
			<form method="post" id="DataObjectManager_Popup_SearchForm">
				<h2>Search for an existing member</h2>
				<p>
					<% control SearchableFields %>
					<input type="text" id="$ID" placeholder="$Name" autocomplete="off" />
					<% end_control %>
				</p>
			</form>
			<table cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<% control SearchableFields %>
						<th class="$HTMLClass" style="width:$Top.TableColumnWidth">$Name</td>
						<% end_control %>
						<th class="Add last">Add</th>
					</tr>
				</thead>
				<tbody>
					$SearchableResultsTableBody
				</tbody>
			</table>
			<div class="dataobjectmanager-actions ">
				<a id="show-add-dataobject" class="popup-button">
					<span class="uploadlink"><img src="dataobject_manager/images/add.png">Add new $name</span>
				</a>
			</div>
		</div>
		<div class="add-dataobject $PopupClasses" style="clear: both;">
			$DetailForm
		</div>
	</body>
</html>
