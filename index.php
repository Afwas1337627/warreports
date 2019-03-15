<?php
  $start_time = microtime(true);
 ?>
<html>
<head>
  <title>War reports summary</title>
  <!-- Latest compiled and minified CSS -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.28.5/css/theme.bootstrap_3.min.css" integrity="sha256-cerl+DYHeG2ZhV/9iueb8E+s7rubli1gsnKuMbKDvho=" crossorigin="anonymous" />
  <!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

  <link rel="shortcut icon" type="image/x-icon" href="./favicon.ico" />
  <style type="text/css">
h1 {
  text-align: center;
}
table {
  font-size: 14px;
}
pre:first-of-type {
    display: none;
    white-space: pre-wrap;
}
:focus {
outline: 0;
}
</style>
<!-- Latest compiled and minified JavaScript -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script>
    function toggle_visibility(tag)
    {
        var e = document.getElementsByTagName(tag)[0];
        console.log(e);
        if (e.style.display == 'block') {
            e.style.display = 'none';
        } else {
            e.style.display = 'block';
        }
    }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.28.5/js/jquery.tablesorter.min.js" integrity="sha256-qW1prHl/Pkqu4uMxFepBr/umy73wqs47F8ubIqK0w1A=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.28.5/js/jquery.tablesorter.widgets.min.js" integrity="sha256-FLqkOiW9slo+v9XYvuv733VRVQdyCGres0oNhFah454=" crossorigin="anonymous"></script>
<!-- <script src="jquery.fixedheadertable.min.js"></script> -->
</head>
<body>
  <div class="container">
<!--    <div style="text-align: center;"><p><a href="https://eu.relentless.pw/blackjack/neg.php">Zen count < 0</a></p></div>
    <div style="text-align: center;"><p><a href="https://eu.relentless.pw/blackjack/low.php">Zen count 0 - 3</a></p></div>
    <div style="text-align: center;"><p><a href="https://eu.relentless.pw/blackjack/mid.php">Zen count 4 - 9</a></p></div>
    <div style="text-align: center;"><p><a href="https://eu.relentless.pw/blackjack/high.php">Zen count > 10</a></p></div>
-->
    <h1>War reports summary</h1>
    <div id="time"></div>
      	<?php
        try {
            $dbh = new PDO("sqlite:members.sqlite");
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "SELECT member_id, member_name, member_level, faction, points, joins, clears " .
            "FROM members ORDER BY points DESC;";

          	$results = $dbh->query( $query );
        } catch ( PDOException $e ) {
            echo"<pre>" . $e->getCode() . ": " . $e->getMessage() . "</pre>";
        }
      ?>
    <table id="myTable" class="tablesorter table header fixed">
      <caption>War reports summary</caption>
      <thead>
        <tr>
          <th data-sorter="false">#</th><th data-sorter="false">Name</th><th>Level</th><th data-sorter="false">Faction</th><th>Points</th><th>Joins</th><th>Clears</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $i = 1;
          foreach ( $results as $key => $result ) {
              print "<tr>";
              print "<td>$i</td>";
              print '<td><a href="https://www.torn.com/profiles.php?XID=' . $result['member_id'] . ' target="_blank">' . $result['member_name'] . ' [' . $result['member_id'] . "]</a></td>";
              print "<td style=\"padding-left: 15px;\">" . $result['member_level'] . "</td>";
			  print "<td style=\"text-align: center;\">" . $result['faction'] . "</td>";
              print "<td style=\"text-align: center;\">" . $result['points'] . "</td>";
              print "<td style=\"padding-left: 25px;\">" . $result['joins'] . "</td>";
              print "<td style=\"padding-left: 25px;\">" . $result['clears'] . "</td>";
              print "</tr>";
              $i += 1;
          } // foreach
        ?>
      </tbody>
    </table>
</div>
</body>
<?php
  $end_time = microtime(true);
 ?>
 <script>
  document.getElementById("time").textContent = "Script took <?php echo round($end_time - $start_time, 5); ?> seconds.";
</script>
<script>
  $(function(){
    // add new widget called indexFirstColumn
    $.tablesorter.addWidget({
        // give the widget a id
        id: "indexFirstColumn",
        // format is called when the on init and when a sorting has finished
        format: function(table) {
            // loop all tr elements and set the value for the first column
            $(table).find("tr td:first-child").each(function(index){
                $(this).text(index+1);
            })
        }
    });

    $.tablesorter.themes.bootstrap = {
        // these classes are added to the table. To see other table classes available,
        // look here: http://getbootstrap.com/css/#tables
        //table        : 'table table-bordered table-striped',
        table        : 'table',
        caption      : 'caption',
        // header class names
        header       : 'bootstrap-header', // give the header a gradient background (theme.bootstrap_2.css)
        sortNone     : '',
        sortAsc      : '',
        sortDesc     : '',
        active       : '', // applied when column is sorted
        hover        : '', // custom css required - a defined bootstrap style may not override other classes
        // icon class names
        icons        : '', // add "icon-white" to make them white; this icon class is added to the <i> in the header
        iconSortNone : 'bootstrap-icon-unsorted', // class name added to icon when column is not sorted
        iconSortAsc  : 'glyphicon glyphicon-chevron-up', // class name added to icon when column has ascending sort
        iconSortDesc : 'glyphicon glyphicon-chevron-down', // class name added to icon when column has descending sort
        filterRow    : '', // filter row class; use widgetOptions.filter_cssFilter for the input/select element
        footerRow    : '',
        footerCells  : '',
        even         : '', // even row zebra striping
        odd          : ''  // odd row zebra striping
    };

    $("#myTable").tablesorter({
    		// this will apply the bootstrap theme if "uitheme" widget is included
    		// the widgetOptions.uitheme is no longer required to be set
    		theme : "bootstrap",
        sortInitialOrder: "desc",
    		widthFixed: true,
    		headerTemplate : '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!
    		// widget code contained in the jquery.tablesorter.widgets.js file
    		// use the zebra stripe widget if you plan on hiding any rows (filter widget)
    		widgets : [ "uitheme", 'zebra','indexFirstColumn'],
    		widgetOptions : {
    			// using the default zebra striping class name, so it actually isn't included in the theme variable above
    			// this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
    			// zebra : ["even", "odd"],

    			// class names added to columns when sorted
    			// columns: [ "primary", "secondary", "tertiary" ],

    			// reset filters button
    			// filter_reset : ".reset",

    			// extra css class name (string or array) added to the filter element (input or select)
    			// filter_cssFilter: "form-control",

    			// set the uitheme widget to use the bootstrap theme class names
    			// this is no longer required, if theme is set
    			// ,uitheme : "bootstrap"

    		}
  	});
  });
</script>
</html>
