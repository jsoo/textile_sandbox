<?php

define('PATH_TO_GIT', '/usr/local/git/bin/git');

if ( ! empty($_POST['textile_this']) )
{
	$input = $_POST['textile_this'];
	include 'textile/classTextile.php';
	$parser = new Textile;
	$rendered = $parser->TextileThis($input);
}
else
	$rendered = $input = '';

chdir(trim(`pwd`) . '/textile');
$git_branch = substr(shell_exec(PATH_TO_GIT . ' branch | grep ^\*'), strlen('* '));
$git_latest_commit = shell_exec(PATH_TO_GIT . '  log -1 classTextile.php');
$commit = $author = $date = $notes = '';
foreach ( explode("\n", $git_latest_commit) as $line )
{
	if ( ! $commit && preg_match('/^commit (.+)/', $line, $match) )
	{
		$commit = $match[1];
		continue;
	}
	if ( ! $author && preg_match('/^Author: (.+)/', $line, $match) )
	{
		$author = $match[1];
		continue;
	}
	if ( ! $date && preg_match('/^Date: (.+)/', $line, $match) )
	{
		$date = $match[1];
		continue;
	}
	if ( $line = trim($line) )
		$notes[] = $line;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Textile Sandbox</title>
	<link type="text/css" rel="stylesheet" href="sandbox.css" />
</head>
<body>
<div id="content">
<h1>Textile Sandbox</h1>
<table>
<caption><code>classTextile.php</code> status:</caption>
<tr>
	<th>Branch</th>
	<td><?php echo $git_branch; ?></td>
</tr>
<tr>
	<th>Commit</th>
	<td><?php echo $commit; ?></td>
</tr>
<tr>
	<th>Author</th>
	<td><?php echo $author; ?></td>
</tr>
<tr>
	<th>Date</th>
	<td><?php echo $date; ?></td>
</tr>
<tr>
	<th>Notes</th>
	<td><?php echo implode('<br />', $notes); ?></td>
</tr>
</table>
<form method="post">
<div id="form_content">
<h2>Input</h2>
<textarea name="textile_this" rows="20" cols="40"><?php echo $input; ?></textarea><br />
<input type="submit" value="Textile this &rarr;" />
</div>
</form>
<?php if ( $rendered ) : ?>
<div id="html_source">
<h2>HTML source</h2>
<pre><code><?php echo htmlspecialchars($rendered); ?>
</code></pre>
</div>
<div id="web_output">
<h2>Web output</h2>
<?php echo $rendered; ?>
</div>
<?php endif; ?>
</div>
</body>
</html>
