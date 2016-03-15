<!DOCTYPE html>
<!-- HTML5 Boilerplate -->
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->

<head>

    <meta charset="utf-8">
    <!-- Always force latest IE rendering engine & Chrome Frame -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title>Pet Sitter Search</title>
    <meta name="description" content="Pet Sitting!">
    <meta name="keywords" content="responsive, grid, system, web design">

    <meta name="author" content="Corey">

    <meta http-equiv="cleartype" content="on">

    <link rel="shortcut icon" href="/favicon.ico">

    <!-- Responsive and mobile friendly stuff -->
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="../assets/css/responsivegridsystem/html5reset.css" media="all">
    <link rel="stylesheet" href="../assets/css/responsivegridsystem/col.css" media="all">
    <link rel="stylesheet" href="../assets/css/responsivegridsystem/2cols.css" media="all">
    <link rel="stylesheet" href="../assets/css/responsivegridsystem/3cols.css" media="all">
    <link rel="stylesheet" href="../assets/css/responsivegridsystem/4cols.css" media="all">
    <link rel="stylesheet" href="../assets/css/responsivegridsystem/5cols.css" media="all">
    <link rel="stylesheet" href="../assets/css/responsivegridsystem/6cols.css" media="all">
    <link rel="stylesheet" href="../assets/css/responsivegridsystem/7cols.css" media="all">
    <link rel="stylesheet" href="../assets/css/responsivegridsystem/8cols.css" media="all">
    <link rel="stylesheet" href="../assets/css/responsivegridsystem/9cols.css" media="all">
    <link rel="stylesheet" href="../assets/css/responsivegridsystem/10cols.css" media="all">
    <link rel="stylesheet" href="../assets/css/responsivegridsystem/11cols.css" media="all">
    <link rel="stylesheet" href="../assets/css/responsivegridsystem/12cols.css" media="all">

    <script src="../assets/js/jquery-2.2.1.min.js"></script>
    <script src="../assets/js/jquery-ui.js"></script>
    <link rel="stylesheet" href="../assets/css/default.css" media="all">
    <link rel="stylesheet" href="../assets/css/jquery-ui.css" media="all">
    <link rel="stylesheet" href="../assets/css/jquery-ui.theme.css" media="all">
    <link rel="stylesheet" href="../assets/css/jquery-ui.structure.css" media="all">
</head>

<body>
    <div class="header">
        <?php echo html::image('assets/images/rover_logo.png');?>

        <?php
            //pre process sitters so we can json encode nicely
            $sitter_ratings = array();
            foreach ($sitters as $sitter) {
                $sitter_ratings[$sitter->id] = $sitter->get_ratings_score();
            }
        ?>
        <div class="section group">
            <div class="col span_1_of_3">
            </div>
            <div class="col span_1_of_3">
                <h1>Find Sitters in Your Area</h1>
                <br />
                <p>Use the search box to narrow your search for your perfect sitter</p>
                <br />
                <h2 class="">Rating:</h2>
                <br />
                <br />
                <div id="slider"></div>
                <br />

                <div id="min"></div>
                <div id="max"></div>


                <br />
                <br />
            </div>
            <div class="col span_1_of_3">
            </div>
        </div>
    </div>


    <div class="body">
        <div class="section group">
            <div class="col span_1_of_5">
            </div>
            <div class="col span_3_of_5">
                <table cellspacing="0" style="width:100%;">
                    <tr>
                        <th style='align:left;'>Name</th>
                        <th style='align:center;'>Picture</th>
                        <th style='align:center;'>Average Stay Rating</th>
                    </tr>   
                    <?php
                        foreach($sitters as $sitter) {
                            echo "<tr id=table_row_" . $sitter->id . ">";
                            echo "<td style='align:left;'>" . $sitter->name . "</td>";
                            echo "<td style='align:center;'>" . html::image($sitter->image, array('width'=>'50px;')) . "</td>";
                            echo "<td style='align:center;'>" . $sitter_ratings[$sitter->id] . "</td>";   
                            echo "</tr>";
                        }
                    ?>
                </table>
            </div>
            <div class="col span_1_of_5">
            </div>
        </div>
    </div>
    <div class="footer">
    </div>

    <script>

        var sitters = <?php echo json_encode($sitter_ratings);?>;

        $( "#slider" ).slider({
            range: true,
            min:0,
            max:5,
            step:.25,
            values: [ 0, 5 ],
            slide: function(event, ui) {
                var delay = function() {
                    var handleIndex = $(ui.handle).data('index.uiSliderHandle');
                    var label = handleIndex == 0 ? '#min' : '#max';
                    $(label).html(ui.value).position({
                        my: 'center top',
                        at: 'center bottom',
                        of: ui.handle,
                        offset: "0, 10"
                    });
                };
                
                // wait for the ui.handle to set its position
                setTimeout(delay, 5);
            }
        });

        $( "#slider" ).slider({
            change: function( event, ui ) {
                var min_value = $("#slider").slider("values")[0];
                var max_value = $("#slider").slider("values")[1];

                for (var staff_id in sitters) {
                    console.log(sitters[staff_id]);
                    if (sitters[staff_id] >= min_value && sitters[staff_id] <= max_value) {
                        $("#table_row_" + staff_id).show();
                    } else {
                        $("#table_row_" + staff_id).hide();
                    }
                }
            }
        });
    </script>
  </body>
</html>
    