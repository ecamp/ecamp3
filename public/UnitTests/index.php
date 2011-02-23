<?php

$resultFile = __DIR__ . "/../../tests/results.xml";

$result_xml = file_get_contents($resultFile);

$result = new SimpleXMLElement($result_xml);
$testTime = filemtime($resultFile);

$shortFeedback = $result->testsuite->attributes()->failures == 0 ? "pass" : "fail"

?>

<html>
	<head>
		<title>UnitTests</title>

		<style type="text/css">
			*
			{
				font-family: Verdana;
			}

			table
			{
				border-color: black;
				background-color: white;
			}


			th
			{
				text-align: left;
				padding:2px;

				background-color: black;
				color: white;
			}

			td
			{
				padding:1px;
			}

			tr.test_fail
			{
				background-color:#ff8c00;
			}

			tr.test_pass
			{
				background-color:#32cd32;
			}

			table.overview tr:nth-child(2n)
			{
				background-color: #d3d3d3;
			}

			div.fail, table.fail
			{
				background-color: #ff4500;
			}

			div.pass, table.pass
			{
				background-color:#32cd32;
			}

		</style>
	</head>

	<body style="padding:0; margin:0">

		<div style="padding:10; margin-top:20px;
					border-bottom:5px solid black;
					border-top:5px solid black;"
			 class="<?= $shortFeedback ?>">

			<h1><?= $result->testsuite->attributes()->name ?></h1>

			<table style="width:40%;" class="<?= $shortFeedback ?>">
				<tr style="height:50px;">
					<td>
						<b>From: </b>
						<br />

						<i><?= date("d. M Y  -  H:i", $testTime); ?> Uhr</i>
					</td>
					<td style="vertical-align:middle;">
						<form action="run.php" style="vertical-align:middle; margin:0; padding:0">
							<input type="submit" value="REFRESH - Run UnitTests" />
						</form>
					</td>
				</tr>
			</table>
		</div>

		<div style="margin: 20px; margin-top: 50px;">

			<p>
				<a href="/UnitTests/Result/">GoTo Code Coverage</a>
			</p>

			<table width="50%" style="border: 2px solid black" class="overview">
				<?php foreach($result->testsuite->attributes() as $key => $value) : ?>
				<tr>
					<td><b><?= ucfirst($key); ?>:</b></td>
					<td><?= $value; ?></td>
				</tr>
				<?php endforeach; ?>
			</table>

			<br />
			<br />
			<h2>TestClasses:</h2>
			<ol>
				<?php foreach($result->testsuite->testsuite as $testCase): ?>
					<li>
						<a href="#<?= $testCase->attributes()->name; ?>">
							<?= $testCase->attributes()->name; ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ol>

			<br />
			<br />

			<?php foreach($result->testsuite->testsuite as $testCase): ?>
				<a name="<?= $testCase->attributes()->name; ?>" />
				<h2><?= $testCase->attributes()->name; ?>:</h2>

				<p>
					<table width="80%" style="border: 2px solid black" class="overview">
						<?php foreach($testCase->attributes() as $key => $value) : ?>
						<tr>
							<td><b><?= ucfirst($key); ?>:</b></td>
							<td><?= $value; ?></td>
						</tr>
						<?php endforeach; ?>
					</table>
				</p>

				<p>
					<table width="90%" cellspacing="0" class="test_results" border="1">
						<thead>
							<tr>
								<th>Test Name</th>
								<th>Result</th>
							</tr>
						</thead>
						<tbody>

							<?php foreach($testCase->testcase as $test_result): ?>

								<?php if(isset($test_result->failure)) : ?>
									<tr class="test_fail">
								<?php else: ?>
									<tr class="test_pass">
								<?php endif; ?>

										<td><?php echo $test_result['name'] ?></td>

										<td>
											<pre><?php echo $test_result->failure ?></pre>
										</td>
									</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</p>
				<br />
				<br />

			<?php endforeach; ?>
		</div>

	</body>
</html>