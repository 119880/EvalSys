<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/requests/admin.php';

$method = $_SERVER['REQUEST_METHOD'];

if (empty($_GET['id']) || empty($_GET['type']) || empty($_GET['year']) || empty($_GET['semester']))
{
    echo 'Invalid Request';
    exit;
}

$response = eval_result(
    mysqli_real_escape_string($conn, $_GET['type']),
    mysqli_real_escape_string($conn, $_GET['id']),
    mysqli_real_escape_string($conn, $_GET['year']),
    mysqli_real_escape_string($conn, $_GET['semester']));

if (!$response)
{
    echo 'Invalid Request';
    exit;
}

if (!$response->data->num_rows) {
    echo 'Question not found';
    exit;
}

$info = (object) $response->info->fetch_assoc();

?>


<html>
	<head>
		<meta charset="utf-8" />
		<style>
            body {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
			.result-box {
				padding: 30px;
				font-size: 16px;
				line-height: 24px;
				font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
				color: #555;
			}

			.result-box table {
				width: 100%;
				line-height: inherit;
				text-align: left;
			}

			.result-box table td {
				padding: 5px;
				vertical-align: top;
			}

			.result-box table tr td:nth-child(4) {
				text-align: right;
			}

			.result-box table tr.top table td {
				padding-bottom: 20px;
			}

			.result-box table tr.top table td.title {
				font-size: 24px;
				line-height: 45px;
				color: #333;
			}

			.result-box table tr.information table td {
				padding-bottom: 40px;
			}

			.result-box table tr.heading td {
				background:rgb(75, 51, 255);
				border-bottom: 1px solid #ddd;
				font-weight: bold;
                color: white;
			}

			.result-box table tr.details td {
				padding-bottom: 20px;
			}

			.result-box table tr.item td {
				border-bottom: 1px solid #eee;
			}

			.result-box table tr.item.last td {
				border-bottom: none;
			}

			.result-box table tr.total td:nth-child(4) {
                padding-top: 15px;
				border-top: 2px solid #eee;
				font-weight: bold;
			}

			@media only screen and (max-width: 600px) {
				.result-box table tr.top table td {
					width: 100%;
					display: block;
					text-align: center;
				}

				.result-box table tr.information table td {
					width: 100%;
					display: block;
					text-align: center;
				}
			}

			/** RTL **/
			.result-box.rtl {
				direction: rtl;
				font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
			}

			.result-box.rtl table {
				text-align: right;
			}

			.result-box.rtl table tr td:nth-child(4) {
				text-align: left;
			}
            
            .buttons {
                text-align:center;
                margin: 20px;
            }

            .buttons button {
                background: black;
                color: white;
                border-radius: 10px;
                width: 100px;
                padding: 10px;
                font-size: 16px;
            }

            .border-print {
                max-width: 900px;
				margin: auto;
                border: 1px solid #eee;
				box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            }
		</style>
	</head>

	<body>
        <div class="buttons">
            <button id='export'>Export</button>
            <button id='printbtn'>Print</button>
        </div>
        <div class="border-print">
            <div id="print" class="result-box">
                <table cellpadding="0" cellspacing="0">
                    <tr class="top">
                        <td colspan="4">
                            <table>
                                <tr>
                                    <td class="title">
                                        <p><?php echo $SETTINGS->title; ?>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <?php echo date("Y-m-d H:m:s"); ?><br />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="title">
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr class="information">
                        <td colspan="4">
                            <table>
                                <tr>
                                    <td>
                                        <strong>
                                        <?php echo ($_GET['type'] == "Teacher")? $info->edp_code . " (" . $info->name . ") " . $info->fname . " " . $info->lname : $info->fname . " " . $info->lname;?>'s Evaluation Results
                                        </strong><br />
                                        <?php echo ($_GET['semester'] == '1') ? 'Academic Year '. $_GET['year'] . '-' . (intval($_GET['year']) + 1) . ' 1st Semester'
                                            : 'Academic Year '. $_GET['year'] . '-' . (intval($_GET['year']) + 1) . ' 2nd Semester';
                                            echo '<br />Type: '. $_GET['type'];
                                        ?>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr class="heading">
                        <td>Skills</td>
                        <td>Rating</td>
                        <td>Remarks</td>
                    </tr>

                    <?php
                        $total_row = $response->data->num_rows;
                        $total_points = 0;

                        while ($result = $response->data->fetch_assoc())
                        {
                            echo '<tr class="item">
                                <td>'.$result['question'].'</td>
                                <td>'.$result['average'].'</td>
                                <td>'.remarks_enum($result['average']).'</td>
                            </tr>';

                            $total_points += $result['average'];
                        }

                        $total_rating = $total_points / $total_row;
                    ?>

                    <tr class="total">
                        <td><b>Total Rating: </b></td>
                        <td><b><?php echo round($total_rating, 4); ?></b></td>
                        <td><b><?php echo remarks_enum($total_rating); ?></b></td>
                    </tr>
                </table>
            </div>
        </div>
        <?php include 'scripts.php'; ?>
        <script>
            $('#printbtn').click(function() {
                $('#print').print()
            });

            $('#export').click(function(){
                window.jsPDF = window.jspdf.jsPDF;

                var doc = new jsPDF();
                var elementHTML = document.querySelector("#print");

                doc.html(elementHTML, {
                    callback: function(doc) {
                        doc.save('<?php echo ($_GET['type'] == "Teacher")? "ES-". $info->edp_code . "-" . $info->name . "-" . $info->fname . "-" . $info->lname : $info->fname . "-" . $info->lname;?>');
                    },
                    x: 15,
                    y: 15,
                    width: 170,
                    windowWidth: 900
                });
            });
        </script>
	</body>
</html>