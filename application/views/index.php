<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report</title>
</head>
<body>
 <div class="container">
    <div class="row">
        <h3>Report</h3>
        <table border='1'>
    <thead>
        <tr>
            <!-- <th>Interval Start</th> -->
            <th>Interval </th>
            <th>Apps Used</th>
            <th>Productive (%)</th>
            <th>Unproductive (%)</th>
            <th>Neutral (%)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($report as $row): ?>
        <tr>
            <td><?= $row->interval_time; ?></td>
            <td><?= $row->apps_used; ?></td>
            <td><?= $row->productive_percentage; ?>%</td>
            <td><?= $row->unproductive_percentage; ?>%</td>
            <td><?= $row->neutral_percentage; ?>%</td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

    </div>
 </div>   

</body>
</html>